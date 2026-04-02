const ImageBank = require('../models/ImageBank');

class PavageService {
    static async generateLevel() {
        try {
            // 1. Récupérer l'image depuis la BDD
            const imagesCount = await ImageBank.countDocuments();
            if (imagesCount === 0) {
                throw new Error("Aucune image dans la base de données. Avez-vous lancé le script d'import ?");
            }
            
            // Prendre une image au hasard
            const random = Math.floor(Math.random() * imagesCount);
            const imageToUse = await ImageBank.findOne().skip(random);

            console.log(`✅ Image "${imageToUse.name}" piochée en BDD. Génération du puzzle...`);

            // 2. Transformer le texte de l'image en liste de briques et créer la grille cible
            const bricksQueue = [];
            const targetGrid = [];
            const lines = imageToUse.textData.trim().split('\n');

            let brickId = 0;

            for (let y = 0; y < lines.length; y++) {
                const colors = lines[y].trim().split(' ');
                const row = [];
                
                for (let x = 0; x < colors.length; x++) {
                    const hexColor = colors[x];
                    const colorCss = `#${hexColor}`; // On ajoute le # pour le CSS côté React
                    
                    // Ajout à la grille cible (pour vérifier les réponses du joueur)
                    row.push({ color: colorCss });
                    
                    // Création de la brique de puzzle associée
                    bricksQueue.push({
                        id: `brick_${brickId++}`,
                        shape: '1x1',          // Taille de la brique gérée par server.js
                        color: colorCss, 
                        targetX: x,            // Coordonnée où le joueur doit la placer
                        targetY: y
                    });
                }
                targetGrid.push(row);
            }

            // 3. Mélanger les briques (Algorithme de Fisher-Yates) pour que ce soit un vrai puzzle
            for (let i = bricksQueue.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [bricksQueue[i], bricksQueue[j]] = [bricksQueue[j], bricksQueue[i]];
            }

            console.log(`✅ Puzzle généré avec succès ! (${bricksQueue.length} briques mélangées)`);

            // 4. Retourner les données au server.js
            return {
                imageName: imageToUse.name,
                gridWidth: imageToUse.width,
                gridHeight: imageToUse.height,
                targetGrid: targetGrid,
                bricksQueue: bricksQueue
            };

        } catch (err) {
            console.error("❌ Erreur dans generateLevel :", err.message);
            throw err; // On renvoie l'erreur au server.js
        }
    }
}

module.exports = PavageService;