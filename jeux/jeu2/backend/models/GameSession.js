const mongoose = require('mongoose');

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

module.exports = mongoose.model('GameSession', GameSessionSchema);