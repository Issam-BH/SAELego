C'est très juste ! J'avais mentionné `jimp` dans les commandes, mais c'est beaucoup plus clair de bien séparer ce qui sert au serveur web de ce qui sert spécifiquement à traiter les images pour la base de données.

Voici le tutoriel mis à jour avec une section dédiée et bien expliquée pour la bibliothèque d'importation des images dans la BDD.

***

```markdown
# 🧱 Guide d'Utilisation et Configuration - Jeu de Fidélité Lego

Ce projet est une application de jeu de puzzle (style Lego) séparée en un **Backend Node.js** (connecté à MongoDB) et un **Frontend React**. Ce guide vous explique comment configurer l'environnement, importer de nouvelles images pour les transformer en puzzles, et lancer le jeu.

---

## 🛠️ 1. Prérequis et Installation des bibliothèques

Avant de commencer, assurez-vous d'avoir installé sur votre machine :
* **Node.js** (version 16 ou supérieure)
* **MongoDB** (en cours d'exécution sur votre machine locale `127.0.0.1:27017`)

### A. Installation du Backend (Serveur)
Ouvrez un terminal, placez-vous dans le dossier du backend et installez les dépendances nécessaires au serveur de jeu et à la connexion base de données (`mongoose`) :

```bash
cd jeu1/backend
npm install express mongoose socket.io cors
```

### B. 📦 Bibliothèque pour importer les images dans la BDD
Pour que le script d'importation puisse lire vos images (PNG/JPG) et les convertir en codes couleurs pour MongoDB, **vous devez installer la bibliothèque `jimp`**. 

⚠️ *Attention très importante : Pour éviter les erreurs `Jimp.read is not a function`, installez impérativement la version `0.22.10` :*
```bash
npm install jimp@0.22.10
```

### C. Installation du Frontend (Site React)
Ouvrez un **deuxième terminal**, placez-vous dans le dossier du frontend et installez les dépendances React :

```bash
cd jeu1/frontend
npm install
```

---

## 🖼️ 2. Comment préparer et importer des images dans la BDD

Le jeu ne demande pas aux joueurs d'uploader des images. Les images doivent être traitées et stockées dans la base de données par vos soins, à l'avance.

1. Prenez une image de votre choix (ex: `mario.png`, `logo.jpg`).
2. Placez cette image dans le dossier `jeu1/backend/scripts/`.
3. Ouvrez le fichier `jeu1/backend/scripts/importImages.js` avec votre éditeur de code.
4. Tout en bas du fichier, dans la fonction `run()`, modifiez le nom du fichier pour cibler votre image, et donnez-lui un nom. 
   *Exemple pour une image nommée mario.png :*
   ```javascript
   await processAndSaveImage(path.join(__dirname, 'mario.png'), 'Mario_Bros');
   ```
5. **Vérifiez la connexion BDD** : Assurez-vous que la connexion dans le script (toujours dans la fonction `run()`) pointe bien vers la base de votre jeu (`lego_fidelite`) :
   ```javascript
   await mongoose.connect('mongodb://127.0.0.1:27017/lego_fidelite');
   ```
6. Dans le terminal du backend, lancez le script d'importation :
   ```bash
   node scripts/importImages.js
   ```
7. Le script va utiliser `jimp` pour réduire l'image (20x20 max) et extraire les pixels, puis `mongoose` pour les envoyer dans MongoDB. Si tout fonctionne, le terminal affichera : `Image 'Mario_Bros' traitée et sauvegardée avec succès !`

---

## 🚀 3. Lancer l'application

Une fois vos images importées en base de données, vous pouvez démarrer les serveurs pour jouer.

**Étape 1 : Démarrer le Backend (Serveur de jeu)**
Dans votre premier terminal (dossier `jeu1/backend`) :
```bash
node server.js
```
*Vous devriez voir : "✅ Backend Node.js démarré sur le port 3001" et "MongoDB connecté avec succès."*

**Étape 2 : Démarrer le Frontend (Site Web React)**
Dans votre deuxième terminal (dossier `jeu1/frontend`) :
```bash
npm start
```
*Le site va s'ouvrir automatiquement dans votre navigateur (généralement à l'adresse `http://localhost:3000`).*

---

## 📱 4. Jouer sur le site

1. Allez sur le site Web.
2. Lancez une partie (Solo ou Duplicate).
3. Le serveur backend va automatiquement **piocher une image au hasard** parmi celles que vous avez importées avec le script.
4. Il va la transformer instantanément en un puzzle décomposé en briques Lego.
5. La grille de jeu est **100% responsive** : elle s'adapte à la taille de votre écran (PC ou Mobile) grâce au système Flexbox en CSS, sans aucune superposition de briques.
6. Placez les briques avant la fin du chronomètre pour marquer des points de fidélité.

---

## 🐛 5. Résolution des problèmes fréquents

* **Erreur `Aucune image dans la base de données` :** Vous avez oublié de lancer le script `importImages.js` ou celui-ci n'envoie pas les images dans la bonne base de données (`lego_fidelite`). Vérifiez l'URL de connexion dans le script.
* **Erreur `Jimp.read is not a function` :** Vous avez installé la mauvaise version de Jimp. Faites `npm uninstall jimp` puis `npm install jimp@0.22.10`.
* **Les cases du tableau débordent de l'écran :** Assurez-vous d'avoir bien mis à jour le fichier `jeu1/frontend/src/styles/game.css` avec les propriétés `flex: 1` et `aspect-ratio: 1/1` pour avoir un affichage fluide et responsive.
```