const mongoose = require('mongoose');

const GameSessionSchema = new mongoose.Schema({
    players: [{ type: String }],
    // Ajout de 'casse-briques' dans l'énumération des modes
    mode: { type: String, enum: ['solo', 'duplicate'] },
    gameType: { type: String, enum: ['reproduction', 'casse-briques'], default: 'reproduction' },
    scores: { type: Map, of: Number },
    date: { type: Date, default: Date.now }
});

module.exports = mongoose.model('GameSession', GameSessionSchema);