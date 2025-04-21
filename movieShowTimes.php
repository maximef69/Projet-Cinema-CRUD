<?php

/*
========================================================================
                            ALGO
         POUR AFFICHER LES HORAIRES D'UN FILM SPÉCIFIQUE
========================================================================

Début du programme

1. Démarrer la session
2. Importer le fichier de configuration pour accéder à la base de données

3. Vérifier si l'utilisateur est connecté :
    - Si non connecté, rediriger vers la page de connexion (index.php)

4. Vérifier si un film a été sélectionné via l'URL (paramètre GET : filmId)
    - Si aucun filmId n'est fourni, rediriger vers la liste des films

5. Récupérer les horaires (séances) de ce film :
    a. Faire une requête SQL pour récupérer les horaires associés au film
       en joignant les données avec la table cinéma pour récupérer le nom du cinéma
    b. Enregistrer les résultats dans une liste `schedules`

6. Récupérer le titre du film correspondant via une autre requête SQL

7. Définir une fonction `formatDate(dateString)` :
    a. Convertit une date au format texte en une version lisible (ex. : 12 Avr 2025 à 14:30)
    b. Traduit les noms des mois anglais en français

8. Affichage HTML :
    a. Afficher le titre du film
    b. Si aucune séance :
        - Afficher un message d'absence d'horaires
    c. Sinon :
        - Afficher un tableau contenant :
            - Le nom du cinéma
            - L’heure de début formatée
            - L’heure de fin formatée
            - La version du film (ex : VO, VF)

9. Afficher des liens pour retourner :
    a. à la liste des films
    b. à l’accueil

Fin du programme
========================================================================
*/


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

    // Récupérer les horaires pour ce film
    $sql = "SELECT s.CINEMAID, s.FILMID, s.HEUREDEBUT, s.HEUREFIN, s.VERSION, c.DENOMINATION AS NOM_CINEMA
            FROM seance s
            JOIN cinema c ON s.CINEMAID = c.CINEMAID
            WHERE s.FILMID = :filmId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['filmId' => $filmId]);
    $horaires = $stmt->fetchAll();

    // Récupérer le titre du film
    $sql = "SELECT TITRE FROM film WHERE FILMID = :filmId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['filmId' => $filmId]);
    $film = $stmt->fetch();
    $filmTitre = $film ? $film['TITRE'] : 'Film inconnu';

} else {
    header("Location: moviesList.php");
    exit();
}

function formatDate($dateString) {
    $mois = [
        'Jan' => 'Jan', 'Feb' => 'Fév', 'Mar' => 'Mar', 'Apr' => 'Avr',
        'May' => 'Mai', 'Jun' => 'Juin', 'Jul' => 'Juil', 'Aug' => 'Aoû',
        'Sep' => 'Sep', 'Oct' => 'Oct', 'Nov' => 'Nov', 'Dec' => 'Déc'
    ];

    $timestamp = strtotime($dateString);
    $moisAnglais = date('M', $timestamp);
    $moisFr = $mois[$moisAnglais];
    return date('d', $timestamp) . ' ' . $moisFr . ' ' . date('Y à H:i', $timestamp);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Horaires du Film</title>
    <link rel="stylesheet" href="css/cinema.css">
</head>
<body>
<header>
    <h1>Horaires du Film</h1>
</header>
<main>
    <h2>Horaires pour : <?= htmlspecialchars($filmTitre) ?></h2>

    <p>
        <a href="editShowtime.php?filmId=<?= urlencode($filmId) ?>&from=movieShowtime.php?filmId=<?= urlencode($filmId) ?>" class="button">
            ➕ Ajouter une séance dans un autre cinéma
        </a>
    </p>

    <?php if (empty($horaires)): ?>
        <p>Aucun horaire disponible pour ce film pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Cinéma</th>
                    <th>Heure de début</th>
                    <th>Heure de fin</th>
                    <th>Version</th>
                    <th>Modifier</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($horaires as $horaire): ?>
                    <tr>
                        <td><?= htmlspecialchars($horaire['NOM_CINEMA']) ?></td>
                        <td><?= formatDate($horaire['HEUREDEBUT']) ?></td>
                        <td><?= formatDate($horaire['HEUREFIN']) ?></td>
                        <td><?= htmlspecialchars($horaire['VERSION']) ?></td>
                        <td>
                            <a href="editShowtime.php?filmId=<?= urlencode($filmId) ?>&cinemaId=<?= urlencode($horaire['CINEMAID']) ?>&heureDebut=<?= urlencode($horaire['HEUREDEBUT']) ?>&from=movieShowtime.php?filmId=<?= urlencode($filmId) ?>">
                                Modifier
                            </a>
                        </td>
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
