const ImageBank = require('../models/ImageBank');

class PavageService {
    static async generateLevel() {
        try {
            // 1. Récupérer l'image depuis la BDD
            const imagesCount = await ImageBank.countDocuments();
            if (imagesCount === 0) {
                throw new Error("Aucune image dans la base. Avez-vous lancé le script d'import ?");
            }
            const random = Math.floor(Math.random() * imagesCount);
            const imageToUse = await ImageBank.findOne().skip(random);

            console.log(`✅ Image "${imageToUse.name}" piochée en BDD. Génération du puzzle Node.js...`);

            // 2. Transformer le texte de l'image en liste de briques
            const bricksQueue = [];
            const lines = imageToUse.textData.trim().split('\n');

            let brickId = 0;

            for (let y = 0; y < lines.length; y++) {
                const colors = lines[y].trim().split(' ');
                
                for (let x = 0; x < colors.length; x++) {
                    const hexColor = colors[x];
                    
                    // On ignore les pixels transparents ou noirs purs s'ils servent de fond (optionnel, on garde tout ici)
                    bricksQueue.push({
                        id: `brick_${brickId++}`,
                        shape: '1x1', // Important: votre server.js attend le format "1x1" pour la vérification !
                        color: `#${hexColor}`, 
                        targetX: x,            
                        targetY: y
                    });
                }
            }

            // 3. Mélanger les briques (Algorithme de Fisher-Yates)
            for (let i = bricksQueue.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [bricksQueue[i], bricksQueue[j]] = [bricksQueue[j], bricksQueue[i]];
            }

            console.log(`✅ Puzzle généré avec succès ! (${bricksQueue.length} briques mélangées)`);

            // 4. Générer la grille cible (targetGrid) attendue par server.js
            const targetGrid = [];
            for (let y = 0; y < lines.length; y++) {
                const row = [];
                const colors = lines[y].trim().split(' ');
                for (let x = 0; x < colors.length; x++) {
                    row.push({ color: `#${colors[x]}` });
                }
                targetGrid.push(row);
            }

            return {
                imageName: imageToUse.name,
                gridWidth: imageToUse.width,
                gridHeight: imageToUse.height,
                targetGrid: targetGrid,
                bricksQueue: bricksQueue
            };

        } catch (err) {
            console.error("❌ Erreur dans generateLevel :", err.message);
            throw err;
        }
    }
}

module.exports = PavageService;