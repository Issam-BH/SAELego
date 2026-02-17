

## Prérequis pour les utilisateurs Windows

Il est obligatoire d'installer Docker Desktop sur votre machine. Lors de l'installation, assurez-vous d'accepter l'intégration de WSL2 (Windows Subsystem for Linux), car cela garantit les meilleures performances et la compatibilité des chemins de fichiers.

Vous devez également faire attention aux fins de ligne. Windows utilise CRLF, tandis que notre conteneur Linux exige LF. Pour éviter les erreurs de compilation du code C, assurez-vous que votre éditeur de code (comme VS Code) est configuré pour sauvegarder les fichiers au format LF, ou utilisez un fichier .gitattributes à la racine de votre projet.

## Démarrage de l'environnement

Ouvrez votre terminal (PowerShell ou le terminal intégré de VS Code) à la racine du projet, là où se trouve le fichier docker-compose.yml.

Exécutez la commande suivante pour construire et démarrer les serveurs en arrière-plan:

`docker-compose up -d --build`

Attendez quelques instants que Docker télécharge les images et installe les dépendances. Votre site sera ensuite accessible à l'adresse http://localhost:8080.

## Configuration Post-Déploiement

Une fois les conteneurs lancés, il y a deux étapes cruciales à réaliser une seule fois pour préparer le backend.

**Étape A : Compilation du programme C**

Le programme de pavage doit être compilé directement à l'intérieur du conteneur Linux pour fonctionner correctement avec PHP. Exécutez cette commande pour compiler le code source:

`docker exec -it saelego_app gcc /var/www/html/C_backend/pavage.c -o /var/www/html/bin/pavage -lm`

Ensuite, donnez les droits d'exécution au fichier généré avec cette commande:

`docker exec -it saelego_app chmod +x /var/www/html/bin/pavage`

**Étape B : Importation de la base de données**

Les tables et les données initiales doivent être importées avec les droits d'administrateur. Exécutez cette commande pour forcer l'importation du dump SQL:

`docker exec -i saelego_db mysql -u root -proot_password lego_db < ./SQL/dump_lego_app.sql`

Après ces étapes, votre environnement local est entièrement configuré et prêt pour le développement.