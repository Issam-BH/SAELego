const { exec } = require('child_process');
const path = require('path');

class PavageService {
    static generateLevel() {
        return new Promise((resolve, reject) => {
            
            // CORRECTION 1 : On remonte de 2 dossiers (services -> backend -> jeu1) 
            // puis on descend dans JAVA/target/classes
            const classPath = path.join(__dirname, '../../JAVA/target/classes');
            
            const javaCommand = `java -cp "${classPath}" fr.univ_eiffel.WebGameAdapter`;
            
            console.log(`Exécution du programme Java : ${javaCommand}`);

            // CORRECTION 2 : On définit le répertoire de travail (cwd) sur le dossier JAVA.
            // Ainsi, quand Java cherchera "sae_java_group/image.txt", il le trouvera au bon endroit !
            const javaDirectory = path.join(__dirname, '../../JAVA');

            exec(javaCommand, { cwd: javaDirectory }, (error, stdout, stderr) => {
                if (error) {
                    console.error("Erreur lors du lancement de Java :", stderr || error.message);
                    return reject(error);
                }
                
                try {
                    const levelData = JSON.parse(stdout);
                    
                    if (levelData.error) throw new Error(levelData.error);
                    
                    console.log(`Succès : JSON reçu du Java ! (${levelData.bricksQueue.length} briques à placer)`);
                    resolve(levelData);
                } catch (e) {
                    console.error("Le Java n'a pas renvoyé un JSON valide. Sortie reçue :", stdout);
                    reject(new Error("Erreur JSON depuis Java."));
                }
            });
        });
    }
}

module.exports = PavageService;