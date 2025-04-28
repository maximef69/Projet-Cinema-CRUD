<?php
/*
========================================================================
                         ALGO
        POUR AFFICHER LES HORAIRES D'UN CINÉMA
========================================================================

Début du programme

1. Inclure le fichier de configuration pour la base de données

2. Vérifier si l'utilisateur est connecté
   a. Si non, rediriger l'utilisateur vers la page d'accueil (index.php)

3. Vérifier si un CINEMAID est passé dans l'URL (GET)
   a. Si non, rediriger vers la liste des cinémas (cinemasList.php)

4. Si CINEMAID est passé :
   a. Récupérer le nom du cinéma dans la base de données en fonction de CINEMAID
   b. Vérifier si le cinéma existe, sinon afficher "Cinéma inconnu"

5. Récupérer les horaires pour ce cinéma (FILMID, CINEMAID, HEUREDEBUT, HEUREFIN, VERSION)
   a. Récupérer les films programmés pour ce cinéma avec leurs horaires et versions

6. Si aucun film n'est programmé pour ce cinéma, afficher un message : "Aucun film n'est actuellement programmé"

7. Si des films sont programmés, afficher les horaires dans un tableau :
   a. Afficher le titre du film, heure de début, heure de fin, et la version

8. Afficher les liens de retour vers la liste des cinémas et l'accueil

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

// Vérifier si un cinéma a été sélectionné
if (!isset($_GET['CinemaId'])) {
    header("Location: cinemasList.php");
    exit();
}

$cinemaId = $_GET['CinemaId'];

// Récupérer les infos du cinéma
$sql = "SELECT DENOMINATION FROM cinema WHERE CINEMAID = :cinemaId";
$stmt = $pdo->prepare($sql);
$stmt->execute(['cinemaId' => $cinemaId]);
$cinema = $stmt->fetch();

$denomination = $cinema['DENOMINATION'] ?? 'Cinéma inconnu';


// Récupérer les séances
$sql = "SELECT s.FILMID, s.CINEMAID, s.HEUREDEBUT, s.HEUREFIN, s.VERSION, f.TITRE AS NOM_FILM
        FROM seance s
        JOIN film f ON s.FILMID = f.FILMID
        WHERE s.CINEMAID = :cinemaId
        ORDER BY s.HEUREDEBUT";
$stmt = $pdo->prepare($sql);
$stmt->execute(['cinemaId' => $cinemaId]);
$horaires = $stmt->fetchAll();

// Fonction pour formater la date en date française
function formatDate($dateString) {
    $mois = [
        'Jan' => 'Jan', 'Feb' => 'Fév', 'Mar' => 'Mar', 'Apr' => 'Avr',
        'May' => 'Mai', 'Jun' => 'Juin', 'Jul' => 'Juil', 'Aug' => 'Aoû',
        'Sep' => 'Sep', 'Oct' => 'Oct', 'Nov' => 'Nov', 'Dec' => 'Déc'
    ];
    $timestamp = strtotime($dateString);
    $moisAnglais = date('M', $timestamp);
    $moisFr = $mois[$moisAnglais] ?? $moisAnglais;
    return date('d', $timestamp) . ' ' . $moisFr . ' ' . date('Y à H:i', $timestamp);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Horaires du Cinéma</title>
    <link rel="stylesheet" href="css/cinema.css">
</head>
<body>
<header>
    <h1>Films programmés dans ce cinéma</h1>
</header>
<main>
    <h2>Horaires pour le cinéma <?= htmlspecialchars($denomination) ?></h2>

    <p>
        <a href="editShowtime.php?cinemaId=<?= urlencode($cinemaId) ?>&from=cinemaShowtime.php?CinemaId=<?= urlencode($cinemaId) ?>" class="button">
            ➕ Ajouter une nouvelle séance
        </a>
    </p>

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
                    <th>Modifier</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($horaires as $horaire): 
                    $formId = "formulaireSupprimerSeance-" . $horaire['CINEMAID'] . "-" . $horaire['FILMID'] . "-" . strtotime($horaire['HEUREDEBUT']);
                ?>
                    <tr>
                        <td><?= htmlspecialchars($horaire['NOM_FILM']) ?></td>
                        <td><?= formatDate($horaire['HEUREDEBUT']) ?></td>
                        <td><?= formatDate($horaire['HEUREFIN']) ?></td>
                        <td><?= htmlspecialchars($horaire['VERSION']) ?></td>
                        <td>
                            <a href="editShowtime.php?cinemaId=<?= urlencode($horaire['CINEMAID']) ?>&filmId=<?= urlencode($horaire['FILMID']) ?>&heureDebut=<?= urlencode($horaire['HEUREDEBUT']) ?>&from=cinemaShowtime.php?CinemaId=<?= urlencode($cinemaId) ?>">
                                Modifier
                            </a>
                        </td>
                        <td>
                            <a href="#" onclick="supprimerSeance('<?= $horaire['CINEMAID'] ?>','<?= $horaire['FILMID'] ?>','<?= strtotime($horaire['HEUREDEBUT']) ?>'); return false;">Supprimer</a>
                            <form id="<?= $formId ?>" action="deleteShowtime.php" method="post" style="display: none;">
                                <input type="hidden" name="cinemaId" value="<?= $horaire['CINEMAID'] ?>">
                                <input type="hidden" name="filmId" value="<?= $horaire['FILMID'] ?>">
                                <input type="hidden" name="heureDebut" value="<?= $horaire['HEUREDEBUT'] ?>">
                                <input type="hidden" name="from" value="cinemaShowtime.php?CinemaId=<?= urlencode($cinemaId) ?>">
                            </form>
                        </td>
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

<script>
function supprimerSeance(cinemaId, filmId, timestamp) {
    const formId = `formulaireSupprimerSeance-${cinemaId}-${filmId}-${timestamp}`;
    if (confirm("Voulez-vous vraiment supprimer cette séance ?")) {
        document.getElementById(formId).submit();
    }
}
</script>
</body>
</html>
