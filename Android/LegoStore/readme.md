# Documentation Technique - Application Android (LegoStore)

## Présentation
L'application LegoStore est le versant mobile du projet img2brick. Développée en Java, elle permet aux utilisateurs de suivre l'état du service et d'interagir avec l'écosystème de la boutique LEGO depuis un terminal Android.

## Architecture du Projet
Le code source se situe dans le répertoire `Android/LegoStore/`. L'application suit les standards de développement Android modernes :

* Langage : Java 11+.
* Système de Build : Gradle (Kotlin DSL) utilisant `build.gradle.kts`.
* Gestion des ressources : Layouts XML et composants Material Design.

## Composants Principaux

### 1. Activité Principale (MainActivity.java)
C'est le point d'entrée de l'application. Elle est responsable de :
* L'initialisation de l'interface utilisateur définie dans `activity_main.xml`.
* La gestion du cycle de vie de l'application mobile.

### 2. Service d'Arrière-plan (PingWorker.java)
Ce composant utilise l'API WorkManager pour effectuer des tâches périodiques :
* Synchronisation : Il assure la liaison avec le backend PHP via l'endpoint `api_app_ping.php`.
* Maintenance de session : Il vérifie la disponibilité des services et met à jour l'état de l'application en arrière-plan.

### 3. Manifeste (AndroidManifest.xml)
Il définit les permissions nécessaires (Internet) et déclare les composants de l'application.

## Interface Utilisateur (UI)
L'interface est structurée autour de plusieurs fichiers de ressources :
* Layout : `activity_main.xml` utilise des conteneurs Android standards pour afficher les informations de la boutique.
* Thèmes : Support du mode clair et du mode nuit via les fichiers `themes.xml`.
* Icônes : Utilisation d'icônes adaptatives pour une intégration fluide sur toutes les versions d'Android.

## Compilation et Déploiement

### Prérequis
* Android Studio Jellyfish ou ultérieur.
* Android SDK (Target API 34+ recommandée).

### Instructions de Build
1. Ouvrir le projet dans le dossier `Android/LegoStore`.
2. Synchroniser le projet avec les fichiers Gradle (`gradlew`).
3. Générer l'APK via la commande :

   ```bash
   ./gradlew assembleDebug
   ```
## Distribution

* Une fois compilé, l'APK (LegoStore_v1.apk) est destiné à être placé dans le dossier public/apk/ du serveur PHP pour permettre le téléchargement direct par les utilisateurs.

### Intégration avec le Backend

* L'application communique avec l'API PHP pour :

    - Le monitoring : Vérification de l'état du serveur.

   -  Les données : Récupération des points de fidélité ou des informations utilisateur via api_points.php.