<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Vérifier si un cinéma a été sélectionné
if (isset($_GET['CinemaId'])) {
    $cinemaId = $_GET['CinemaId'];
    $sql = "SELECT DENOMINATION FROM cinema WHERE CINEMAID = :cinemaId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['cinemaId' => $cinemaId]);
    $cinema = $stmt->fetch();

$denomination = $cinema ? $cinema['DENOMINATION'] : 'Cinéma inconnu';


    // Récupérer les horaires pour ce cinéma (FILMID, CINEMAID, HEUREDEBUT, HEUREFIN, VERSION)
    $sql = "SELECT s.FILMID, s.CINEMAID, s.HEUREDEBUT, s.HEUREFIN, s.VERSION, f.TITRE AS NOM_FILM
            FROM seance s
            JOIN film f ON s.FILMID = f.FILMID
            WHERE s.CINEMAID = :cinemaId";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['cinemaId' => $cinemaId]);
    $horaires = $stmt->fetchAll();
} else {
    // Si aucun cinéma n'est sélectionné, rediriger vers la liste des cinémas
    header("Location: cinemasList.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horaires du Cinéma</title>
    <link rel="stylesheet" href="css/cinema.css">
</head>
<body>
    <header>
        <h1>Films programmés dans ce cinéma</h1>
    </header>
    <main>
        <h2>Horaires pour le cinéma <?= htmlspecialchars($denomination) ?></h2>
        <?php if (empty($horaires)): ?>
            <p>Aucun film n'est actuellement programmé dans ce cinéma.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Film</th>
                        <th>Heure de début</th>
                        <th>Heure de fin</th>
                        <th>Version</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($horaires as $horaire): ?>
                        <tr>
                            <td><?= htmlspecialchars($horaire['NOM_FILM']) ?></td>
                            <td><?= htmlspecialchars($horaire['HEUREDEBUT']) ?></td>
                            <td><?= htmlspecialchars($horaire['HEUREFIN']) ?></td>
                            <td><?= htmlspecialchars($horaire['VERSION']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <p><a href="cinemasList.php">Retour à la liste des cinémas</a></p>
        <p><a href="index.php">Retour à l'accueil</a></p>
    </main>
    <footer>
        <p>&copy; 2025 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
</body>
</html>
