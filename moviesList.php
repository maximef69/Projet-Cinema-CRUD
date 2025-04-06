<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT FILMID, TITRE FROM film";
$stmt = $pdo->query($sql);
$allMovies = $stmt->fetchAll();
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
        <h1>Liste des Films</h1>
    </header>
    <main>

        <h2>Films disponibles</h2>
        <?php if (empty($allMovies)): ?>
            <p>Aucun film disponible pour le moment.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Consulter les seances</th> <!-- Nouvelle colonne pour le bouton -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allMovies as $movie): ?>
                        <tr>
                            <td><?= htmlspecialchars($movie['TITRE']) ?></td>
                            <td>
                                
                                <a href="movieShowTimes.php?filmId=<?= $movie['FILMID'] ?>" class="btn">Voir les horaires</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p><a href="index.php">Retour à l'accueil</a></p>
    </main>
    <footer>
        <p>&copy; 2025 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
</body>
</html>
