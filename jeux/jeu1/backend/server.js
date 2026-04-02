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

io.on('connection', (socket) => {
    socket.on('join_game', async ({ fidelityId, mode }) => {
        socket.fidelityId = fidelityId;
        
        try {
            let player = await Player.findOne({ fidelityId });
            if (!player) {
                await Player.updateOne({ fidelityId }, { $set: { fidelityId } }, { upsert: true });
            }
        } catch (e) {
            console.error("Avertissement MongoDB:", e.message);
        }

        let room = `room_${Date.now()}`;
        socket.join(room);

        let levelData;
        try {
            // Appel au nouveau service 100% Node.js qui interroge MongoDB
            levelData = await PavageService.generateLevel();
        } catch (err) {
            // ON AFFICHE LA VRAIE ERREUR ICI POUR VOUS AIDER À DÉBOGUER
            console.error("\n❌ Erreur de génération du niveau :", err.message);
            console.log("⚠️ Utilisation de la grille de secours en attendant de régler l'erreur ci-dessus...");
            
            // Grille de secours au cas où la base de données est vide
            levelData = {
                targetGrid: [
                    [{color: '#ff0000'}, {color: '#ff0000'}, {color: '#0000ff'}, {color: '#0000ff'}],
                    [{color: '#ff0000'}, {color: '#ff0000'}, {color: '#ffff00'}, {color: '#ffff00'}],
                    [{color: '#008000'}, {color: '#008000'}, {color: '#ffffff'}, {color: '#ffffff'}],
                    [{color: '#000000'}, {color: '#000000'}, {color: '#000000'}, {color: '#000000'}]
                ],
                bricksQueue: [
                    {color: '#ff0000', shape: '2x2'},
                    {color: '#0000ff', shape: '2x1'},
                    {color: '#ffff00', shape: '2x1'},
                    {color: '#008000', shape: '2x1'},
                    {color: '#ffffff', shape: '2x1'},
                    {color: '#000000', shape: '4x1'}
                ]
            };
        }

        const targetGrid = levelData.targetGrid;
        const height = targetGrid.length;
        const width = targetGrid[0].length;

        activeGames[room] = {
            mode,
            players: [socket.id],
            playerIds: { [socket.id]: fidelityId },
            targetGrid: targetGrid, 
            bricksQueue: levelData.bricksQueue, 
            currentTurn: 0,
            grids: { [socket.id]: createEmptyGrid(width, height) },
            scores: { [socket.id]: 0 },
            timer: null
        };
        
        io.to(room).emit('game_started', { 
            message: "La partie commence !", 
            targetGrid: targetGrid 
        });
        nextTurn(room);
    });

    socket.on('place_brick', ({ x, y, brick }) => {
        const room = Object.keys(activeGames).find(r => activeGames[r].players.includes(socket.id));
        const game = activeGames[room];
        
        if(!game) return;

        // EXTRACTION DES DIMENSIONS DE LA BRIQUE
        const [wStr, hStr] = brick.shape.split('x');
        const brickWidth = parseInt(wStr, 10) || 1;
        const brickHeight = parseInt(hStr, 10) || 1;

        const gridHeight = game.grids[socket.id].length;
        const gridWidth = game.grids[socket.id][0].length;

        // 1. Vérifier si la brique dépasse les bords
        if (y + brickHeight > gridHeight || x + brickWidth > gridWidth) {
            console.log("Échec : La brique sort des limites du plateau !");
            return;
        }

        // 2. Vérifier si les cases sont vides
        for (let dy = 0; dy < brickHeight; dy++) {
            for (let dx = 0; dx < brickWidth; dx++) {
                if (game.grids[socket.id][y + dy][x + dx] !== null) {
                    console.log("Échec : Une case est déjà occupée !");
                    return;
                }
            }
        }

        console.log(`Succès : Brique ${brick.shape} placée en [${x}, ${y}]`);
        
        // 3. Placer la brique et calculer le score
        for (let dy = 0; dy < brickHeight; dy++) {
            for (let dx = 0; dx < brickWidth; dx++) {
                game.grids[socket.id][y + dy][x + dx] = brick;
                
                // +10 points par case correcte
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
    
    // Fin de partie s'il n'y a plus de briques
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

server.listen(3001, () => console.log('✅ Backend Node.js démarré sur le port 3001'));