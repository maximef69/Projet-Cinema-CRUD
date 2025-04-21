<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cinemaId'])) {
    $cinemaId = $_POST['cinemaId'];

    $sql = "DELETE FROM cinema WHERE CINEMAID = :cinemaId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['cinemaId' => $cinemaId]);

    header("Location: cinemasList.php");
    exit();
} else {
    header("Location: cinemasList.php");
    exit();
}
