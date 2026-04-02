const mongoose = require('mongoose');

const ImageBankSchema = new mongoose.Schema({
    name: { type: String, required: true },
    width: { type: Number, required: true },
    height: { type: Number, required: true },
    textData: { type: String, required: true } 
});

module.exports = mongoose.model('ImageBank', ImageBankSchema);