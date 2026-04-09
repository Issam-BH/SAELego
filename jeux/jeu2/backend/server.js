const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const mongoose = require('mongoose');
const cors = require('cors');

mongoose.connect('mongodb://127.0.0.1:27017/lego_fidelite').catch(() => {});

async function sendPointsToPHP(userId, pointsEarned) {
    console.log(`\n--- [Try send points] ---`);
    console.log(`User ID: ${userId}`);
    console.log(`Points: ${pointsEarned}`);

    if (userId.startsWith('guest_')) {
        console.log('❌ User are not login.');
        return;
    }
    if (pointsEarned <= 0) {
        console.log('❌ User have 0 points.');
        return;
    }

    try {
        const response = await fetch('http://localhost/SAELego/PHP/public/api_points.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                user_id: userId, 
                add_points: pointsEarned 
            })
        });
        
        const textData = await response.text(); 
        console.log(`Request from PHP server:`, textData);
        
        const data = JSON.parse(textData);
        if (data.success) {
            console.log(`✅ Good, new points: ${data.new_total}`);
        }
    } catch (error) {
        console.error('❌ Error, conection with PHP:', error);
    }
}

const GameSessionSchema = new mongoose.Schema({
    players: [{ type: String, required: true }], 
    gameType: { type: String, default: 'casse-briques', immutable: true },
    mode: { type: String, enum: ['solo', 'duplicate'], required: true },
    scores: { type: Map, of: Number }, 
    winner: [String],
    date: { type: Date, default: Date.now }
});
const GameSession = mongoose.model('GameSession', GameSessionSchema);

const PlayerSchema = new mongoose.Schema({
    fidelityId: { type: String, required: true, unique: true },
    loyaltyPoints: { type: Number, default: 0 }
});
const Player = mongoose.model('Player', PlayerSchema);

const app = express();
app.use(cors());
const server = http.createServer(app);
const io = new Server(server, { cors: { origin: '*' } });

const GRID_SIZE = 10;
const SHAPES = [
    { id: 1, matrix: [[0, 1, 0], [1, 1, 1]], color: '#FF3333' },
    { id: 2, matrix: [[1, 0], [1, 0], [1, 1]], color: '#9933FF' },
    { id: 3, matrix: [[1, 1, 0], [0, 1, 1]], color: '#FF9933' },
    { id: 4, matrix: [[0, 1, 0], [1, 1, 1], [0, 1, 0]], color: '#3366FF' },
    { id: 5, matrix: [[1, 1], [1, 0]], color: '#2ECC71' }
];
const COLOR_SCORES = { '#FF3333': 10, '#9933FF': 30, '#FF9933': 10, '#3366FF': 20, '#2ECC71': 10 };

const activeRooms = {};

function createEmptyGrid() { return Array(GRID_SIZE).fill(null).map(() => Array(GRID_SIZE).fill(null)); }
function getRandomBrick() { return SHAPES[Math.floor(Math.random() * SHAPES.length)]; }

function checkAndClearLines(grid) {
    let rowsToClear = [];
    let colsToClear = [];
    let points = 0;

    for (let y = 0; y < GRID_SIZE; y++) {
        if (grid[y].every(cell => cell !== null)) rowsToClear.push(y);
    }

    for (let x = 0; x < GRID_SIZE; x++) {
        let colFull = true;
        for (let y = 0; y < GRID_SIZE; y++) {
            if (grid[y][x] === null) { colFull = false; break; }
        }
        if (colFull) colsToClear.push(x);
    }

    rowsToClear.forEach(y => {
        for (let x = 0; x < GRID_SIZE; x++) {
            if (grid[y][x] && COLOR_SCORES[grid[y][x]]) points += COLOR_SCORES[grid[y][x]];
        }
    });

    colsToClear.forEach(x => {
        for (let y = 0; y < GRID_SIZE; y++) {
            if (!rowsToClear.includes(y)) {
                if (grid[y][x] && COLOR_SCORES[grid[y][x]]) points += COLOR_SCORES[grid[y][x]];
            }
        }
    });

    rowsToClear.forEach(y => {
        for (let x = 0; x < GRID_SIZE; x++) grid[y][x] = null;
    });

    colsToClear.forEach(x => {
        for (let y = 0; y < GRID_SIZE; y++) grid[y][x] = null;
    });

    let totalCleared = rowsToClear.length + colsToClear.length;
    return points * totalCleared;
}

function rotateMatrix(matrix) { return matrix[0].map((val, index) => matrix.map(row => row[index]).reverse()); }

function canPlaceAt(grid, matrix, startX, startY) {
    for (let r = 0; r < matrix.length; r++) {
        for (let c = 0; c < matrix[r].length; c++) {
            if (matrix[r][c] === 1) {
                const targetY = startY + r;
                const targetX = startX + c;
                if (targetY < 0 || targetY >= GRID_SIZE || targetX < 0 || targetX >= GRID_SIZE) return false;
                if (grid[targetY][targetX] !== null) return false;
            }
        }
    }
    return true;
}

function canFitAnywhere(grid, brick) {
    let currentMatrix = brick.matrix;
    for (let rot = 0; rot < 4; rot++) {
        for (let y = 0; y < GRID_SIZE; y++) {
            for (let x = 0; x < GRID_SIZE; x++) {
                if (canPlaceAt(grid, currentMatrix, x, y)) return true;
            }
        }
        currentMatrix = rotateMatrix(currentMatrix);
    }
    return false;
}

function getRandomValidPlacement(grid, brick) {
    let currentMatrix = brick.matrix;
    let validPlacements = [];
    for (let rot = 0; rot < 4; rot++) {
        for (let y = 0; y < GRID_SIZE; y++) {
            for (let x = 0; x < GRID_SIZE; x++) {
                if (canPlaceAt(grid, currentMatrix, x, y)) validPlacements.push({ x, y, matrix: JSON.parse(JSON.stringify(currentMatrix)) });
            }
        }
        currentMatrix = rotateMatrix(currentMatrix);
    }
    return validPlacements.length > 0 ? validPlacements[Math.floor(Math.random() * validPlacements.length)] : null;
}

async function endGame(code, forcedWinnerId = null) {
    let room = activeRooms[code];
    if (!room || room.status === 'finished') return;
    room.status = 'finished';

    let winners = [];
    let maxScore = -1;
    let playersFid = [];
    let scoresMap = {};
    let totalPointsAwarded = 0;

    for (let pid in room.players) {
        let p = room.players[pid];
        playersFid.push(p.fidelityId);
        scoresMap[p.fidelityId] = p.score;
        if (!forcedWinnerId) {
            if (p.score > maxScore) { maxScore = p.score; winners = [pid]; }
            else if (p.score === maxScore && maxScore !== -1) { winners.push(pid); }
        }
    }

    if (forcedWinnerId) winners = [forcedWinnerId];

    let isDraw = winners.length > 1;

    for (let wid of winners) {
        let w = room.players[wid];
        let pts = w.score;
        if (isDraw && room.mode === 'duplicate') pts = Math.floor(pts / 2);
        
        if (pts > 0) {
            totalPointsAwarded += pts;
            await Player.updateOne({ fidelityId: w.fidelityId }, { $inc: { loyaltyPoints: pts } }, { upsert: true });
            sendPointsToPHP(w.fidelityId, pts);
        }
    }

    io.to(code).emit('room_game_over', { 
        winners: winners.map(id => room.players[id].pseudo),
        scores: scoresMap,
        isDraw: isDraw,
        forfeit: !!forcedWinnerId,
        pointsAwarded: totalPointsAwarded > 0
    });

    if (totalPointsAwarded > 0 || room.mode === 'duplicate') {
        await GameSession.create({ 
            players: playersFid, 
            mode: room.mode, 
            scores: scoresMap, 
            winner: winners.map(id => room.players[id].fidelityId) 
        });
    }

    if (room.timer) clearTimeout(room.timer);
}

function startTurn(code) {
    let room = activeRooms[code];
    if (!room || room.status !== 'playing') return;
    let activeCount = 0;
    for (let pid in room.players) {
        let p = room.players[pid];
        if (!p.isGameOver) {
            if (!canFitAnywhere(p.grid, room.currentBrick)) { 
                p.isGameOver = true; 
                io.to(pid).emit('personal_game_over'); 
            }
            else { p.hasPlaced = false; activeCount++; }
        }
    }
    if (activeCount === 0) return endGame(code);
    io.to(code).emit('state_update', { players: room.players });
    io.to(code).emit('turn_started', { brick: room.currentBrick, timeLimit: room.mode === 'duplicate' ? 15 : null });
    if (room.timer) clearTimeout(room.timer);
    if (room.mode === 'duplicate') room.timer = setTimeout(() => endTurnDueToTimeout(code), 15000);
}

function endTurnDueToTimeout(code) {
    let room = activeRooms[code];
    if (!room) return;
    for (let pid in room.players) {
        let p = room.players[pid];
        if (!p.isGameOver && !p.hasPlaced) {
            let placement = getRandomValidPlacement(p.grid, room.currentBrick);
            if (placement) {
                for (let r = 0; r < placement.matrix.length; r++) {
                    for (let c = 0; c < placement.matrix[r].length; c++) {
                        if (placement.matrix[r][c] === 1) p.grid[placement.y + r][placement.x + c] = room.currentBrick.color;
                    }
                }
                p.score += checkAndClearLines(p.grid);
            } else { p.isGameOver = true; io.to(pid).emit('personal_game_over'); }
            p.hasPlaced = true;
        }
    }
    io.to(code).emit('state_update', { players: room.players });
    checkTurnEnd(code);
}

function checkTurnEnd(code) {
    let room = activeRooms[code];
    if (!room) return;
    let done = true;
    for (let pid in room.players) { if (!room.players[pid].isGameOver && !room.players[pid].hasPlaced) done = false; }
    if (done) { room.currentBrick = getRandomBrick(); setTimeout(() => startTurn(code), 500); }
}

io.on('connection', (socket) => {
    const fidelityId = socket.handshake.query.fidelityId || 'guest_' + Math.floor(Math.random() * 10000);
    socket.on('start_solo', (pseudo) => {
        let code = 'SOLO_' + socket.id; socket.join(code);
        activeRooms[code] = { code, admin: socket.id, status: 'playing', mode: 'solo', players: { [socket.id]: { fidelityId, pseudo: pseudo || 'Joueur', grid: createEmptyGrid(), score: 0, isGameOver: false, hasPlaced: false } }, currentBrick: getRandomBrick(), timer: null };
        socket.emit('game_started', { players: activeRooms[code].players, mode: 'solo', code }); startTurn(code);
    });
    socket.on('create_room', (pseudo) => {
        let code = Math.random().toString(36).substring(2, 8).toUpperCase(); socket.join(code);
        activeRooms[code] = { code, admin: socket.id, status: 'lobby', mode: 'duplicate', players: { [socket.id]: { fidelityId, pseudo: pseudo || 'Admin', grid: createEmptyGrid(), score: 0, isGameOver: false, hasPlaced: false } }, currentBrick: null, timer: null };
        socket.emit('room_created', code);
    });
    socket.on('join_room', ({ code, pseudo }) => {
        let room = activeRooms[code];
        if (room && room.status === 'lobby' && Object.keys(room.players).length < 2) {
            socket.join(code); room.players[socket.id] = { fidelityId, pseudo: pseudo || 'Invite', grid: createEmptyGrid(), score: 0, isGameOver: false, hasPlaced: false };
            socket.emit('room_joined', code); io.to(code).emit('room_update', { players: Object.keys(room.players).length });
        } else socket.emit('error_message', 'Indisponible.');
    });
    socket.on('send_chat', ({ code, message }) => {
        const room = activeRooms[code]; if(room && room.players[socket.id]) socket.to(code).emit('receive_chat', { text: message, senderPseudo: room.players[socket.id].pseudo });
    });
    socket.on('start_game', (code) => {
        let room = activeRooms[code]; if (room && room.admin === socket.id) { room.status = 'playing'; room.currentBrick = getRandomBrick(); io.to(code).emit('game_started', { players: room.players, mode: 'duplicate', code }); startTurn(code); }
    });
    socket.on('place_brick', ({ code, x, y, matrix, color }) => {
        let room = activeRooms[code]; if (!room || room.status !== 'playing') return;
        let p = room.players[socket.id]; if (!p || p.isGameOver || p.hasPlaced) return;
        if (canPlaceAt(p.grid, matrix, x, y)) {
            for (let r = 0; r < matrix.length; r++) { for (let c = 0; c < matrix[r].length; c++) { if (matrix[r][c] === 1) p.grid[y + r][x + c] = color; } }
            p.score += checkAndClearLines(p.grid); p.hasPlaced = true; socket.emit('place_success'); io.to(code).emit('state_update', { players: room.players }); checkTurnEnd(code);
        } else socket.emit('invalid_move', { message: 'Invalide !', players: room.players });
    });
    socket.on('abandon_game', (code) => { 
        const room = activeRooms[code]; if (!room) return;
        if (room.mode === 'solo') endGame(code, socket.id);
        else endGame(code, Object.keys(room.players).find(id => id !== socket.id));
    });
    socket.on('disconnect', () => { for (let c in activeRooms) { if (activeRooms[c].players[socket.id]) { if (activeRooms[c].status === 'playing') endGame(c, Object.keys(activeRooms[c].players).find(id => id !== socket.id)); } } });
});
server.listen(4002, () => console.log('✅ Server game 2 have accesse 4002!'));