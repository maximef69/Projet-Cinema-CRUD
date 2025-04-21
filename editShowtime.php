<?php
session_start();
require_once 'config.php';

// Vérification connexion utilisateur
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Récupération des données GET
$cinemaId = $_GET['cinemaId'] ?? null;
$filmId = $_GET['filmId'] ?? null;
$heureDebut = $_GET['heureDebut'] ?? null;
$from = $_GET['from'] ?? 'index.php'; // fallback de sécurité

// Variables pour formulaire
$heureFin = '';
$version = '';

// Si tous les identifiants sont présents, on tente de charger la séance existante
if ($cinemaId && $filmId && $heureDebut) {
    $sql = "SELECT * FROM seance WHERE CINEMAID = :cinemaId AND FILMID = :filmId AND HEUREDEBUT = :heureDebut";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'cinemaId' => $cinemaId,
        'filmId' => $filmId,
        'heureDebut' => $heureDebut
    ]);
    $seance = $stmt->fetch();

    if ($seance) {
        $heureFin = $seance['HEUREFIN'];
        $version = $seance['VERSION'];
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cinemaId = $_POST['cinemaId'];
    $filmId = $_POST['filmId'];
    $heureDebut = $_POST['heureDebut'];
    $heureFin = $_POST['heureFin'];
    $version = $_POST['version'];
    $from = $_POST['from'];
    $oldHeureDebut = $_POST['oldHeureDebut'] ?? $heureDebut;

    // Vérifie existence du cinéma
    $sql = "SELECT COUNT(*) FROM cinema WHERE CINEMAID = :cinemaId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['cinemaId' => $cinemaId]);
    $cinemaExists = $stmt->fetchColumn();

    if (!$cinemaExists) {
        die("Erreur : Le cinéma sélectionné n'existe pas.");
    }

    // Vérifie si la séance existe
    $sql = "SELECT COUNT(*) FROM seance WHERE CINEMAID = :cinemaId AND FILMID = :filmId AND HEUREDEBUT = :oldHeureDebut";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'cinemaId' => $cinemaId,
        'filmId' => $filmId,
        'oldHeureDebut' => $oldHeureDebut
    ]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        // Mise à jour
        $sql = "UPDATE seance 
                SET HEUREDEBUT = :heureDebut, HEUREFIN = :heureFin, VERSION = :version 
                WHERE CINEMAID = :cinemaId AND FILMID = :filmId AND HEUREDEBUT = :oldHeureDebut";
        $params = [
            'cinemaId' => $cinemaId,
            'filmId' => $filmId,
            'heureDebut' => $heureDebut,
            'heureFin' => $heureFin,
            'version' => $version,
            'oldHeureDebut' => $oldHeureDebut
        ];
    } else {
        // Insertion
        $sql = "INSERT INTO seance (CINEMAID, FILMID, HEUREDEBUT, HEUREFIN, VERSION)
                VALUES (:cinemaId, :filmId, :heureDebut, :heureFin, :version)";
        $params = [
            'cinemaId' => $cinemaId,
            'filmId' => $filmId,
            'heureDebut' => $heureDebut,
            'heureFin' => $heureFin,
            'version' => $version
        ];
    }

    // Exécution
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Redirection
    header("Location: $from");
    exit();
}

// Récupérer les films et cinémas
$films = $pdo->query("SELECT FILMID, TITRE FROM film ORDER BY TITRE")->fetchAll();
$cinemas = $pdo->query("SELECT CINEMAID, DENOMINATION FROM cinema ORDER BY DENOMINATION")->fetchAll();

$isFromMovie = str_contains($from, 'movieShowtime.php');
$isFromCinema = str_contains($from, 'cinemaShowtime.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter / Modifier une séance</title>
    <link rel="stylesheet" href="css/cinema.css">
</head>
<body>
<header>
    <h1><?= $filmId && $heureDebut ? 'Modifier' : 'Ajouter' ?> une séance</h1>
</header>
<main>
    <form action="editShowtime.php" method="post">
        <input type="hidden" name="from" value="<?= htmlspecialchars($from) ?>">

        <?php if ($heureDebut): ?>
            <input type="hidden" name="oldHeureDebut" value="<?= htmlspecialchars($heureDebut) ?>">
        <?php endif; ?>

        <!-- Film -->
        <label for="filmId">Film :</label>
        <?php if ($isFromMovie): ?>
            <select disabled>
                <?php foreach ($films as $film): ?>
                    <option value="<?= $film['FILMID'] ?>" <?= $film['FILMID'] == $filmId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($film['TITRE']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="filmId" value="<?= htmlspecialchars($filmId) ?>">
        <?php else: ?>
            <select name="filmId" id="filmId" required>
                <option value="">-- Sélectionnez un film --</option>
                <?php foreach ($films as $film): ?>
                    <option value="<?= $film['FILMID'] ?>" <?= $film['FILMID'] == $filmId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($film['TITRE']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <!-- Cinéma -->
        <label for="cinemaId">Cinéma :</label>
        <?php if ($isFromCinema): ?>
            <select disabled>
                <?php foreach ($cinemas as $cinema): ?>
                    <option value="<?= $cinema['CINEMAID'] ?>" <?= $cinema['CINEMAID'] == $cinemaId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cinema['DENOMINATION']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="cinemaId" value="<?= htmlspecialchars($cinemaId) ?>">
        <?php else: ?>
            <select name="cinemaId" id="cinemaId" required>
                <option value="">-- Sélectionnez un cinéma --</option>
                <?php foreach ($cinemas as $cinema): ?>
                    <option value="<?= $cinema['CINEMAID'] ?>" <?= $cinema['CINEMAID'] == $cinemaId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cinema['DENOMINATION']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <!-- Dates -->
        <label for="heureDebut">Heure de début :</label>
        <input type="datetime-local" name="heureDebut" id="heureDebut" required
               value="<?= $heureDebut ? date('Y-m-d\TH:i', strtotime($heureDebut)) : '' ?>">

        <label for="heureFin">Heure de fin :</label>
        <input type="datetime-local" name="heureFin" id="heureFin" required
               value="<?= $heureFin ? date('Y-m-d\TH:i', strtotime($heureFin)) : '' ?>">

        <!-- Version -->
        <label for="version">Version :</label>
        <input type="text" name="version" id="version" required value="<?= htmlspecialchars($version ?? '') ?>">

        <!-- Actions -->
        <button type="submit">Enregistrer</button>
        <a href="<?= htmlspecialchars($from) ?>">Annuler</a>
    </form>
</main>
<footer>
    <p>&copy; 2025 Gestion de Cinéma</p>
</footer>

<script>
    document.querySelector('form').addEventListener('submit', function (e) {
        const heureDebut = document.getElementById('heureDebut').value;
        const heureFin = document.getElementById('heureFin').value;

        if (heureDebut && heureFin && new Date(heureFin) <= new Date(heureDebut)) {
            alert("L'heure de fin doit être après l'heure de début.");
            e.preventDefault();
        }
    });
</script>
</body>
</html>
