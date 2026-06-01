# 🎬 Application de gestion de cinéma (CRUD PHP)

Bienvenue sur le dépôt du projet de **gestion de cinéma**, une application web développée en PHP permettant d'administrer des cinémas, des films, des séances, ainsi que les préférences des utilisateurs.

Ce projet met en pratique les concepts fondamentaux de la création d'une application web dynamique avec un système complet d'opérations **CRUD** (Create, Read, Update, Delete) et une gestion de sessions sécurisée.

---

## ⚙️ Fonctionnalités principales

L'application est divisée en plusieurs modules interactifs :

### 🔐 Authentification & utilisateurs
* **Connexion / déconnexion** (`index.php`, `logout.php`) : système de session utilisateur sécurisé avec vérification des mots de passe hachés (`password_verify`).
* **Création de compte** (`createNewUser.php`) : inscription de nouveaux utilisateurs dans la base de données.

### 🏢 Gestion des cinémas
* Affichage de la liste des cinémas (`cinemasList.php`).
* Édition des informations d'un cinéma (`editCinema.php`).
* Suppression d'un cinéma (`deleteCinema.php`).

### 🎞️ Gestion des films et horaires
* **Catalogue de films** : consultation (`moviesList.php`), édition (`editMovie.php`) et suppression (`deleteMovie.php`).
* **Séances / horaires** : gestion des heures de projection par film (`moviesShowTimes.php`) et par cinéma (`cinemaShowtimes.php`, `editShowtime.php`, `deleteShowTime.php`).

### ⭐ Liste de favoris personnalisée
Chaque utilisateur connecté peut gérer sa propre liste de films préférés :
* Ajouter un film (`addFavoriteMovie.php`).
* Consulter et éditer sa liste (`editFavoriteMovies.php`, `updateFavoriteMovie.php`).
* Retirer un film de ses favoris (`deleteFavoriteMovie.php`).

---

## 📁 Architecture du projet

Le projet est structuré de manière procédurale avec des fichiers dédiés pour chaque action :

* 📂 **`css/`** : contient les feuilles de style (ex: `cinema.css`) pour l'interface utilisateur.
* 📂 **`data/`** : contient un fichier JSON regroupant des données d'utilisateurs.
* 📂 **`images/`** : dossier contenant les ressources graphiques.
* 📄 **`config.php`** : fichier crucial gérant la connexion à la base de données via **PDO**.
* 📄 **Fichiers `.php` à la racine** : scripts traitant la logique métier, les formulaires et l'affichage HTML.

---

## 🚀 Installation et configuration

### Prérequis
* Un serveur web local (XAMPP, WAMP, MAMP ou Laragon).
* PHP 7.4 ou supérieur.
* MySQL / MariaDB.
* [Composer](https://getcomposer.org/) (si des dépendances doivent être installées).

### Étapes d'installation

1. **Cloner le dépôt** dans le dossier racine de votre serveur web (ex: `htdocs` ou `www`) :
   ```bash
git clone [https://github.com/maximef69/Projet-Cinema-CRUD.git](https://github.com/maximef69/Projet-Cinema-CRUD.git)
   cd Projet-Cinema-CRUD
