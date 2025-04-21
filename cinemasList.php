<?php

/*
========================================================================
                                ALGO
                POUR AFFICHER ET GÉRER LA LISTE DES CINÉMAS
========================================================================

Variables :
    cinemas : Liste de cinémas
    CINEMAID, DENOMINATION, ADRESSE : attributs d'un cinéma

Début du programme :

1. Démarrer la session
2. Importer la configuration (connexion à la base de données)

3. Vérifier si l'utilisateur est connecté :
    - Si non connecté, rediriger vers la page de connexion (index.php)

4. Requête SQL : récupérer la liste des cinémas
    - Pour chaque cinéma, obtenir CINEMAID, DENOMINATION et ADRESSE

5. Affichage HTML :
    - Afficher un titre principal
    - Si la liste est vide, afficher "Aucun cinéma"
    - Sinon :
        Pour chaque cinéma :
            a. Afficher son nom (DENOMINATION)
            b. Afficher son adresse
            c. Lien vers la page des séances du cinéma
            d. Bouton "Supprimer" avec formulaire caché et confirmation JS
            e. Lien "Modifier" qui redirige vers la page d'édition (editCinema.php)

6. Afficher un lien pour ajouter un nouveau cinéma
7. Afficher un lien pour revenir à l'accueil

8. Script JavaScript :
    - Fonction supprimerCine(cinemaId)
        a. Demande de confirmation
        b. Envoie du formulaire de suppression correspondant

Fin du programme
========================================================================
*/


session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT CINEMAID, DENOMINATION, ADRESSE FROM cinema";
$stmt = $pdo->query($sql);
$cinemas = $stmt->fetchAll();
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
        <?php if (empty($cinemas)): ?>
            <p>Aucun cinéma.</p>
        <?php else: ?>
            <ul>
        <?php foreach ($cinemas as $cinema): ?>
        <li>
            <strong><?= htmlspecialchars($cinema['DENOMINATION']) ?></strong><br>
            Adresse : <?= htmlspecialchars($cinema['ADRESSE']) ?><br>
            <a href="cinemaShowtimes.php?CinemaId=<?= $cinema['CINEMAID'] ?>" class="btn">Voir les séances</a><br>
            <a href="#" onclick="supprimerCine(<?= $cinema['CINEMAID'] ?>); return false;">Supprimer ce cinéma</a><br>

            <form id="formulaireSupprimerCine-<?= $cinema['CINEMAID'] ?>" action="deleteCinema.php" method="post" style="display: none;">
                <input type="hidden" name="cinemaId" value="<?= $cinema['CINEMAID'] ?>">
            </form>

            <a href="editCinema.php?cinemaId=<?= $cinema['CINEMAID'] ?>" class="btn">Modifier le cinéma</a><br>
            


         </li>
        <?php endforeach; ?>
         </ul>
            
        <?php endif; ?>

        <p><a href="editCinema.php">Ajouter un cinéma </a></p>

    

        <p><a href="index.php">Retour à l'accueil</a></p>
    </main>
    <footer>
        <p>&copy; 2025 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
</body>
<script>
function supprimerCine(cinemaId) {
    if (confirm("Voulez-vous vraiment supprimer ce cinéma ?")) {
        document.getElementById("formulaireSupprimerCine-" + cinemaId).submit();
    }
}
</script>

</html>
