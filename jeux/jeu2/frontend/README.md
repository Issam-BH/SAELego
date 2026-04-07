# Jeu 2 - Casse-Briques de Lignes Lego

Ce repertoire contient le code source du deuxieme jeu de la boutique Lego : un casse-briques inspire de Tetris, adapte aux regles specifiques du cahier des charges (sans gravite, placement libre, briques lacunaires et systeme de points de fidelite).

## Description du jeu

Le joueur dispose d'une grille vide de 10x10. A chaque tour, une brique Lego (de forme et de couleur aleatoires) est proposee. 
Le but est de placer ces briques sur la grille pour former des lignes completes (horizontales ou verticales). 

**Regles specifiques :**
- Pas de gravite : la brique peut etre placee a n'importe quel emplacement libre.
- Rotation : le joueur peut pivoter la brique avant de la placer.
- Destruction : lorsqu'une ligne ou une colonne est remplie, elle est detruite.
- Score : les points sont attribues lors de la destruction des lignes, en fonction de la valeur de la couleur de chaque bloc. Des bonus multiplicatifs s'appliquent si plusieurs lignes sont detruites simultanement.
- Fin de partie : le jeu s'arrete si le joueur clique sur "Abandonner" ou s'il n'y a plus aucun emplacement valide sur la grille pour poser la brique courante (comprenant toutes ses rotations possibles).
- Fidelite : a la fin de la partie, le score est converti en points de fidelite et sauvegarde dans la base de donnees MongoDB, rattache a l'identifiant du joueur.

## Architecture

Le projet est divise en deux parties principales :
- **Backend** : Serveur Node.js utilisant Express et Socket.io pour la communication en temps reel, et Mongoose pour interagir avec la base de donnees MongoDB.
- **Frontend** : Application cliente developpee avec React, communiquant avec le serveur via WebSockets.

## Pre-requis

- Node.js installe sur votre machine.
- Un serveur MongoDB en cours d'execution (par defaut sur le port 27017).

## Installation et lancement

### 1. Configuration de la base de donnees
Assurez-vous que le service MongoDB tourne en arriere-plan. Le backend tentera de se connecter a l'URI suivante : `mongodb://127.0.0.1:27017/lego_fidelite`.

### 2. Demarrage du Backend
Ouvrez un terminal et placez-vous dans le dossier `backend`.

Installation des dependances :
```bash
npm install express socket.io mongoose cors
```
Lancement du serveur :
```bash

node server.js
```
Le serveur sera en ecoute sur le port 3002.
### 3. Demarrage du Frontend

Ouvrez un second terminal et placez-vous dans le dossier frontend.

Installation des dependances :
```Bash

npm install socket.io-client
```
Lancement de l'application :
```Bash

npm start
```
L'application sera accessible sur http://localhost:3000.
Tester avec un identifiant de fidelite specifique

Le frontend recupere l'identifiant de fidelite du client directement depuis l'URL. Si aucun identifiant n'est fourni, un identifiant "guest" est genere aleatoirement.

Pour simuler la connexion depuis le site PHP de la boutique avec un client precis, vous pouvez ajouter le parametre fidelityId dans l'URL de votre navigateur :
http://localhost:3000/?fidelityId=VOTRE_ID_CLIENT

A la fin de la partie (par blocage ou abandon), les points seront enregistres dans la collection players et l'historique de la session dans la collection gamesessions sous cet identifiant.