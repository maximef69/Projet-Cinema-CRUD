<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Vérifier si un film a été sélectionné
if (isset($_GET['filmId'])) {
    $filmId = $_GET['filmId'];

    // Récupérer les horaires pour ce film (CINEMAID, FILMID, HEUREDEBUT, HEUREFIN, VERSION)
    $sql = "SELECT s.CINEMAID, s.FILMID, s.HEUREDEBUT, s.HEUREFIN, s.VERSION, c.DENOMINATION AS NOM_CINEMA
    FROM seance s
    JOIN cinema c ON s.CINEMAID = c.CINEMAID
    WHERE s.FILMID = :filmId";


    $stmt = $pdo->prepare($sql);
    $stmt->execute(['filmId' => $filmId]);
    $schedules = $stmt->fetchAll();
} else {
    // Si aucun film n'est sélectionné, rediriger vers la liste des films
    header("Location: moviesList.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horaires du Film</title>
    <link rel="stylesheet" href="css/cinema.css">
</head>
<body>
    <header>
        <h1>Horaires du Film</h1>
    </header>
    <main>
        <h2>Horaires pour le film sélectionné</h2>
        <?php if (empty($schedules)): ?>
            <p>Aucun horaire disponible pour ce film pour le moment.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Cinéma</th>
                        <th>Heure de début</th>
                        <th>Heure de fin</th>
                        <th>Version</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?= htmlspecialchars($schedule['NOM_CINEMA']) ?></td>
                            <td><?= htmlspecialchars($schedule['HEUREDEBUT']) ?></td>
                            <td><?= htmlspecialchars($schedule['HEUREFIN']) ?></td>
                            <td><?= htmlspecialchars($schedule['VERSION']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p><a href="moviesList.php">Retour à la liste des films</a></p>

        <p><a href="index.php">Retour à l'accueil</a></p>
    </main>
    <footer>
        <p>&copy; 2025 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
</body>
</html>
