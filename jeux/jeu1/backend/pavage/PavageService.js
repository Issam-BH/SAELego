const { exec } = require('child_process');
const path = require('path');

class PavageService {
    /**
     * Appelle le programme Java/C pour générer le niveau
     * @param {string} imagePath - Le chemin de l'image à pixelliser
     * @returns {Promise<Object>} - La grille cible et la file de briques
     */
    static generateLevel(imagePath = 'default.png') {
        return new Promise((resolve, reject) => {

            const javaClassPath = path.join(__dirname, '../../../JAVA/target/classes');
            const javaCommand = `java -cp ${javaClassPath} fr.univ_eiffel.Main "${imagePath}"`;
            
            exec(javaCommand, (error, stdout, stderr) => {
                if (error) {
                    console.error("Erreur exécution Java:", error);
                    return reject(error);
                }
                try {
                    const levelData = JSON.parse(stdout);
                    resolve(levelData);
                } catch (e) {
                    reject(new Error("Le Java n'a pas renvoyé un JSON valide."));
                }
            });

            console.log("PavageService: Génération du niveau mocké...");
            resolve({
                targetGrid: [
                    [{color: 'red'}, {color: 'red'}, null, null],
                    [null, {color: 'blue'}, {color: 'blue'}, null],
                    [null, null, {color: 'yellow'}, {color: 'yellow'}]
                ],
                bricksQueue: [
                    { color: 'red', shape: '1x1' }, 
                    { color: 'blue', shape: '1x1' }, 
                    { color: 'red', shape: '1x1' }, 
                    { color: 'yellow', shape: '1x1' },
                    { color: 'blue', shape: '1x1' },
                    { color: 'yellow', shape: '1x1' }
                ]
            });
        });
    }
}

module.exports = PavageService;