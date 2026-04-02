const mongoose = require('mongoose');
const Jimp = require('jimp');
const path = require('path');
const ImageBank = require('../models/ImageBank');
const dbConfig = require('../config/db'); // Assurez-vous que ce fichier connecte Mongoose

// Taille maximale pour éviter un pavage trop détaillé
const MAX_WIDTH = 20; 
const MAX_HEIGHT = 20;

async function processAndSaveImage(imagePath, imageName) {
    try {
        const image = await Jimp.read(imagePath);
        
        // Redimensionnement de l'image (pixelisation) pour ne pas la rendre trop grande
        image.scaleToFit(MAX_WIDTH, MAX_HEIGHT, Jimp.RESIZE_NEAREST_NEIGHBOR);
        
        let textRepresentation = "";
        
        // Parcours des pixels pour créer une matrice texte
        // (À adapter selon le format exact attendu par votre programme Java)
        for (let y = 0; y < image.bitmap.height; y++) {
            let row = [];
            for (let x = 0; x < image.bitmap.width; x++) {
                const hexColor = image.getPixelColor(x, y).toString(16).padStart(8, '0').substring(0, 6);
                row.push(hexColor); // On stocke la couleur Hexadécimale (ex: ffaabb)
            }
            textRepresentation += row.join(" ") + "\n";
        }

        // Sauvegarde dans MongoDB
        const newImage = new ImageBank({
            name: imageName,
            width: image.bitmap.width,
            height: image.bitmap.height,
            textData: textRepresentation
        });

        await newImage.save();
        console.log(`Image '${imageName}' traitée et sauvegardée avec succès !`);

    } catch (err) {
        console.error(`Erreur lors du traitement de ${imageName} :`, err);
    }
}

async function run() {
    // Remplacer l'URI par la vôtre si dbConfig ne gère pas la connexion ici
await mongoose.connect('mongodb://127.0.0.1:27017/lego_fidelite');    
    // Exemple d'utilisation : 
    // Mettez une image "test.png" dans le dossier scripts
    await processAndSaveImage(path.join(__dirname, 'test.png'), 'dizzy');
    
    mongoose.disconnect();
}

run();