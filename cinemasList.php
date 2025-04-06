<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT CINEMAID, DENOMINATION, ADRESSE FROM cinema";
$stmt = $pdo->query($sql);
$allCinemas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Films</title>
    <link rel="stylesheet" href="css/cinema.css">
</head>
<body>
    <header>
        <h1>Liste des Cinémas</h1>
    </header>
    <main>

        <h2>Cinémas :</h2>
        <?php if (empty($allCinemas)): ?>
            <p>Aucun cinéma.</p>
        <?php else: ?>
            <ul>
        <?php foreach ($allCinemas as $cinema): ?>
        <li>
            <strong><?= htmlspecialchars($cinema['DENOMINATION']) ?></strong><br>
            Adresse : <?= htmlspecialchars($cinema['ADRESSE']) ?><br>
            <a href="cinemaShowtimes.php?CinemaId=<?= $cinema['CINEMAID'] ?>" class="btn">Voir les séances</a>
         </li>
        <?php endforeach; ?>
         </ul>
            
        <?php endif; ?>

        <p><a href="index.php">Retour à l'accueil</a></p>
    </main>
    <footer>
        <p>&copy; 2025 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
</body>
</html>
