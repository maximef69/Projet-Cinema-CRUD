<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_film'])) {
    $filmId = $_POST['filmId'];
    $comment = $_POST['comment'];

    $sql = "INSERT INTO prefere (USERID, FILMID, COMMENTAIRE) VALUES (:userId, :filmId, :comment)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId, 'filmId' => $filmId, 'comment' => $comment]);

    header("Location: editFavoriteMovies.php");
    exit();
}
?>
