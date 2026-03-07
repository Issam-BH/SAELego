const mongoose = require('mongoose');

const connectDB = async () => {
    try {
        // Remplacez par votre URI MongoDB
        await mongoose.connect('mongodb://127.0.0.1:27017/lego_fidelite', {
            useNewUrlParser: true,
            useUnifiedTopology: true
        });
        console.log('MongoDB connecté avec succès.');
    } catch (error) {
        console.error('Erreur de connexion MongoDB:', error.message);
        process.exit(1);
    }
};

module.exports = connectDB;