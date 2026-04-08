const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');
const connectDB = require('./config/db');
const Player = require('./models/Player');
const GameSession = require('./models/GameSession');
const PavageService = require('./services/PavageService');

const app = express();
app.use(cors());
const server = http.createServer(app);
const io = new Server(server, { cors: { origin: '*' } });

// Connexion MongoDB
connectDB();

const activeGames = {};
const TURN_TIME_LIMIT = 15000;

function createEmptyGrid(w, h) {
    return Array(h).fill().map(() => Array(w).fill(null));
}

// Fonction utilitaire pour initialiser le niveau (utilisée pour solo et duplicate)
async function generateAndStartGame(room, mode, players, playerIds) {
    let levelData;
    try {
        levelData = await PavageService.generateLevel();
    } catch (err) {
        console.error("\n❌ Erreur de génération du niveau :", err.message);
        console.log("⚠️ Utilisation de la grille de secours...");
        levelData = {
            targetGrid: [
                [{color: '#ff0000'}, {color: '#ff0000'}, {color: '#0000ff'}, {color: '#0000ff'}],
                [{color: '#ff0000'}, {color: '#ff0000'}, {color: '#ffff00'}, {color: '#ffff00'}],
                [{color: '#008000'}, {color: '#008000'}, {color: '#ffffff'}, {color: '#ffffff'}],
                [{color: '#000000'}, {color: '#000000'}, {color: '#000000'}, {color: '#000000'}]
            ],
            bricksQueue: [
                {color: '#ff0000', shape: '2x2'}, {color: '#0000ff', shape: '2x1'},
                {color: '#ffff00', shape: '2x1'}, {color: '#008000', shape: '2x1'},
                {color: '#ffffff', shape: '2x1'}, {color: '#000000', shape: '4x1'}
            ]
        };
    }

    const targetGrid = levelData.targetGrid;
    const height = targetGrid.length;
    const width = targetGrid[0].length;

    const grids = {};
    const scores = {};
    players.forEach(socketId => {
        grids[socketId] = createEmptyGrid(width, height);
        scores[socketId] = 0;
    });

    activeGames[room] = {
        mode,
        status: 'playing',
        players,
        playerIds,
        targetGrid: targetGrid, 
        bricksQueue: levelData.bricksQueue, 
        currentTurn: 0,
        grids,
        scores,
        timer: null
    };
    
    io.to(room).emit('game_started', { 
        message: "La partie commence !", 
        targetGrid: targetGrid 
    });
    nextTurn(room);
}

io.on('connection', (socket) => {
    
    // --- MODE SOLO CLASSIQUE ---
    socket.on('join_game', async ({ fidelityId, mode }) => {
        socket.fidelityId = fidelityId;
        try {
            let player = await Player.findOne({ fidelityId });
            if (!player) await Player.updateOne({ fidelityId }, { $set: { fidelityId } }, { upsert: true });
        } catch (e) { console.error("Avertissement MongoDB:", e.message); }

        let room = `room_solo_${Date.now()}`;
        socket.join(room);
        await generateAndStartGame(room, 'solo', [socket.id], { [socket.id]: fidelityId });
    });

    // --- MODE DUPLICATE : CREATION ---
    socket.on('create_duplicate', async ({ fidelityId }) => {
        socket.fidelityId = fidelityId;
        const roomCode = Math.random().toString(36).substring(2, 8).toUpperCase(); // Code à 6 caractères
        socket.join(roomCode);
        
        activeGames[roomCode] = {
            mode: 'duplicate',
            status: 'lobby',
            admin: socket.id,
            players: [socket.id],
            playerIds: { [socket.id]: fidelityId }
        };

        socket.emit('lobby_created', { roomCode, isLobbyAdmin: true });
        io.to(roomCode).emit('lobby_update', { playerCount: 1 });
    });

    // --- MODE DUPLICATE : REJOINDRE ---
    socket.on('join_duplicate', async ({ fidelityId, roomCode }) => {
        const game = activeGames[roomCode];
        if (!game || game.status !== 'lobby') {
            socket.emit('game_error', { message: "Salon introuvable ou partie déjà commencée." });
            return;
        }
        if (game.players.length >= 2) {
            socket.emit('game_error', { message: "Le salon est complet." });
            return;
        }

        socket.fidelityId = fidelityId;
        socket.join(roomCode);
        game.players.push(socket.id);
        game.playerIds[socket.id] = fidelityId;

        socket.emit('lobby_created', { roomCode, isLobbyAdmin: false });
        io.to(roomCode).emit('lobby_update', { playerCount: game.players.length });
    });

    // --- MODE DUPLICATE : LANCER ---
    socket.on('start_duplicate', async ({ roomCode }) => {
        const game = activeGames[roomCode];
        if (game && game.admin === socket.id && game.players.length === 2) {
            await generateAndStartGame(roomCode, 'duplicate', game.players, game.playerIds);
        }
    });

    // --- CHAT TEXTUEL ---
    socket.on('send_chat_message', ({ roomCode, message, senderName }) => {
        if (activeGames[roomCode]) {
            io.to(roomCode).emit('receive_chat_message', {
                senderId: socket.id,
                senderName: senderName || "Joueur",
                message
            });
        }
    });

    // --- PLACEMENT DE BRIQUE ---
    socket.on('place_brick', ({ x, y, brick }) => {
        const room = Object.keys(activeGames).find(r => activeGames[r].players.includes(socket.id));
        const game = activeGames[room];
        if(!game || game.status !== 'playing') return;

        const [wStr, hStr] = brick.shape.split('x');
        const brickWidth = parseInt(wStr, 10) || 1;
        const brickHeight = parseInt(hStr, 10) || 1;
        const gridHeight = game.grids[socket.id].length;
        const gridWidth = game.grids[socket.id][0].length;

        if (y + brickHeight > gridHeight || x + brickWidth > gridWidth) return;

        for (let dy = 0; dy < brickHeight; dy++) {
            for (let dx = 0; dx < brickWidth; dx++) {
                if (game.grids[socket.id][y + dy][x + dx] !== null) return;
            }
        }

        for (let dy = 0; dy < brickHeight; dy++) {
            for (let dx = 0; dx < brickWidth; dx++) {
                game.grids[socket.id][y + dy][x + dx] = brick;
                const targetCell = game.targetGrid[y + dy][x + dx];
                if (targetCell && targetCell.color === brick.color) {
                    game.scores[socket.id] += 10;
                }
            }
        }

        io.to(room).emit('grid_updated', { 
            playerId: socket.id, 
            grid: game.grids[socket.id],
            score: game.scores[socket.id]
        });
        
        // En duplicate, on n'avance le tour que si TOUS les joueurs ont placé ou si le timer finit
        // Pour simplifier, on garde l'avancée automatique pour l'instant (adaptable selon règles exactes)
        nextTurn(room); 
    });
});

async function endGame(room) {
    const game = activeGames[room];
    io.to(room).emit('game_over', { scores: game.scores });

    try {
        for (const socketId of game.players) {
            const fid = game.playerIds[socketId];
            await Player.updateOne({ fidelityId: fid }, { $inc: { loyaltyPoints: game.scores[socketId] } });
        }
        await new GameSession({ players: Object.values(game.playerIds), mode: game.mode, scores: game.scores }).save();
    } catch (err) {
        console.error("Erreur lors de l'enregistrement de fin de partie :", err.message);
    }

    delete activeGames[room];
}

function nextTurn(room) {
    const game = activeGames[room];
    if (!game) return;
    
    if (game.currentTurn >= game.bricksQueue.length) return endGame(room);

    const currentBrick = game.bricksQueue[game.currentTurn];
    io.to(room).emit('new_turn', { brick: currentBrick, timeLimit: TURN_TIME_LIMIT });

    clearTimeout(game.timer);
    game.timer = setTimeout(() => {
        io.to(room).emit('time_up');
        game.currentTurn++;
        nextTurn(room);
    }, TURN_TIME_LIMIT);

    game.currentTurn++;
}

server.listen(4001, () => console.log('✅ Backend Node.js démarré sur le port 4001'));