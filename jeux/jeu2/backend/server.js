const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const mongoose = require('mongoose');
const cors = require('cors');

mongoose.connect('mongodb://127.0.0.1:27017/lego_fidelite');

const GameSessionSchema = new mongoose.Schema({
    players: [{ 
        type: String, 
        required: true 
    }], 
    gameType: { 
        type: String, 
        default: 'casse-briques',
        immutable: true 
    },
    mode: { 
        type: String, 
        enum: ['solo', 'duplicate'], 
        required: true 
    },
    scores: { 
        type: Map, 
        of: Number 
    }, 
    date: { 
        type: Date, 
        default: Date.now 
    }
});

const GameSession = mongoose.model('GameSession', GameSessionSchema);

const PlayerSchema = new mongoose.Schema({
    fidelityId: {
        type: String,
        required: true,
        unique: true
    },
    loyaltyPoints: {
        type: Number,
        default: 0
    }
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

const COLOR_SCORES = {
    '#FF3333': 10,
    '#9933FF': 30,
    '#FF9933': 10,
    '#3366FF': 20,
    '#2ECC71': 10
};

function createEmptyGrid() {
    return Array(GRID_SIZE).fill(null).map(() => Array(GRID_SIZE).fill(null));
}

let gameState = {
    grid: createEmptyGrid(),
    score: 0,
    currentBrick: getRandomBrick(),
    isGameOver: false,
    abandoned: false
};

function getRandomBrick() {
    return SHAPES[Math.floor(Math.random() * SHAPES.length)];
}

function checkAndClearLines(grid) {
    let linesToClearRows = [];
    let points = 0;

    for (let y = 0; y < GRID_SIZE; y++) {
        if (grid[y].every(cell => cell !== null)) {
            linesToClearRows.push(y);
        }
    }

    let clearedCount = linesToClearRows.length;
    
    if (clearedCount > 0) {
        linesToClearRows.forEach(y => {
            for (let x = 0; x < GRID_SIZE; x++) {
                let cellColor = grid[y][x];
                if (COLOR_SCORES[cellColor]) {
                    points += COLOR_SCORES[cellColor];
                }
                grid[y][x] = null;
            }
        });
        
        points *= clearedCount;
    }

    return points;
}

function rotateMatrix(matrix) {
    return matrix[0].map((val, index) => 
        matrix.map(row => row[index]).reverse()
    );
}

function canPlaceAt(grid, matrix, startX, startY) {
    for (let r = 0; r < matrix.length; r++) {
        for (let c = 0; c < matrix[r].length; c++) {
            if (matrix[r][c]) {
                if (startY + r >= GRID_SIZE || startX + c >= GRID_SIZE || grid[startY + r][startX + c] !== null) {
                    return false;
                }
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
                if (canPlaceAt(grid, currentMatrix, x, y)) {
                    return true;
                }
            }
        }
        currentMatrix = rotateMatrix(currentMatrix);
    }
    return false;
}

async function saveScoreToDatabase(fidelityId, score) {
    try {
        const points = Math.floor(score / 10);
        
        await Player.updateOne(
            { fidelityId: fidelityId },
            { $inc: { loyaltyPoints: points } }
        );

        await GameSession.create({
            players: [fidelityId],
            mode: 'solo',
            scores: { [fidelityId]: score }
        });
    } catch (err) {
    }
}

io.on('connection', async (socket) => {
    const fidelityId = socket.handshake.query.fidelityId || 'anonymous';

    try {
        let player = await Player.findOne({ fidelityId });
        if (!player) {
            await Player.updateOne({ fidelityId }, { $set: { fidelityId } }, { upsert: true });
        }
    } catch (e) {
    }
    
    socket.emit('game_state', gameState);

    socket.on('restart_game', () => {
        gameState = {
            grid: createEmptyGrid(),
            score: 0,
            currentBrick: getRandomBrick(),
            isGameOver: false,
            abandoned: false
        };
        io.emit('game_state', gameState);
    });

    socket.on('abandon_game', () => {
        if (!gameState.isGameOver) {
            gameState.isGameOver = true;
            gameState.abandoned = true;
            saveScoreToDatabase(fidelityId, gameState.score);
            io.emit('game_state', gameState);
        }
    });

    socket.on('place_brick', ({ x, y, matrix, color }) => {
        if (gameState.isGameOver) return;

        if (canPlaceAt(gameState.grid, matrix, x, y)) {
            for (let r = 0; r < matrix.length; r++) {
                for (let c = 0; c < matrix[r].length; c++) {
                    if (matrix[r][c]) {
                        gameState.grid[y + r][x + c] = color;
                    }
                }
            }

            let earnedPoints = checkAndClearLines(gameState.grid);
            if (earnedPoints > 0) {
                gameState.score += earnedPoints;
            }

            gameState.currentBrick = getRandomBrick();

            if (!canFitAnywhere(gameState.grid, gameState.currentBrick)) {
                gameState.isGameOver = true;
                gameState.abandoned = false;
                saveScoreToDatabase(fidelityId, gameState.score);
            }

            io.emit('game_state', gameState);
        } else {
            socket.emit('invalid_move', { message: 'Emplacement invalide ou occupe' });
        }
    });
});

server.listen(3002);