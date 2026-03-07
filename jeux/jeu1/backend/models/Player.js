const mongoose = require('mongoose');

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

module.exports = mongoose.model('Player', PlayerSchema);