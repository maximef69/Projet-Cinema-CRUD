<?php
/*
========================================================================
                    ALGO
               POUR SUPPRIMER UN FILM
========================================================================

Début du programme

1. Démarrer la session
2. Inclure le fichier de configuration (connexion à la base de données)

3. Vérifier si l'utilisateur est connecté :
    - Si non, rediriger vers la page d'accueil (index.php)

4. Vérifier que la requête est de type POST et contient l'identifiant du film (filmId) :
    - Si oui :
        a. Récupérer l'identifiant du film depuis la variable POST
        b. Préparer une requête SQL pour supprimer le film où l'identifiant correspond
        c. Exécuter la requête de suppression
        d. Rediriger vers la page de la liste des films (moviesList.php)
    - Sinon :
        a. Rediriger directement vers la page de la liste des films

Fin du programme
========================================================================
*/
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filmId'])) {
    $filmId = $_POST['filmId'];

    $sql = "DELETE FROM film WHERE FILMID = :filmId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['filmId' => $filmId]);

    header("Location: moviesList.php");
    exit();
} else {
    header("Location: moviesList.php");
    exit();
}
