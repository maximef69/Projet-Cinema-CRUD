<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['cinemaId'], $_POST['filmId'], $_POST['heureDebut'])) {
    $cinemaId = $_POST['cinemaId'];
    $filmId = $_POST['filmId'];
    $heureDebut = $_POST['heureDebut'];

    $sql = "DELETE FROM seance 
            WHERE CINEMAID = :cinemaId 
              AND FILMID = :filmId 
              AND HEUREDEBUT = :heureDebut";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'cinemaId' => $cinemaId,
        'filmId' => $filmId,
        'heureDebut' => $heureDebut
    ]);
}

// Rediriger vers la page d'origine
$redirect = $_POST['from'] ?? 'cinemasList.php';
header("Location: " . $redirect);
exit();
