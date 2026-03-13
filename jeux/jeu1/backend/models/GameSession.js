const mongoose = require('mongoose');

const GameSessionSchema = new mongoose.Schema({
    players: [{ type: String }], // Tableau de fidelityIds
    mode: { type: String, enum: ['solo', 'duplicate'] },
    scores: { type: Map, of: Number }, // fidelityId -> score de la partie
    date: { type: Date, default: Date.now }
});

module.exports = mongoose.model('GameSession', GameSessionSchema);