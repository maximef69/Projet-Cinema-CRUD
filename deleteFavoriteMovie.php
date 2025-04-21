<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];

if (isset($_GET['filmId'])) {
    $filmId = $_GET['filmId'];

    $sql = "DELETE FROM prefere WHERE USERID = :userId AND FILMID = :filmId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId, 'filmId' => $filmId]);

    header("Location: editFavoriteMovies.php");
    exit();
} else {
    header("Location: editFavoriteMovies.php");
    exit();
}

