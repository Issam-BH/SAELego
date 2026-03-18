const mongoose = require('mongoose');

const connectDB = async () => {
    try {
        await mongoose.connect('mongodb://127.0.0.1:27017/lego_fidelite', {
            useNewUrlParser: true,
            useUnifiedTopology: true
        });
        console.log('MongoDB (Jeu 2) connecté avec succès.');
    } catch (error) {
        console.error('Erreur de connexion MongoDB (Jeu 2):', error.message);
        process.exit(1);
    }
};

module.exports = connectDB;