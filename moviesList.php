<?php
/*
========================================================================
                            ALGO
             POUR AFFICHER LA LISTE DES FILMS
========================================================================

Début du programme

1. Démarrer la session
2. Inclure le fichier de configuration (connexion à la base de données)

3. Vérifier si l'utilisateur est connecté :
    - Si l'utilisateur n'est pas connecté, rediriger vers la page index.php

4. Exécuter une requête SQL pour récupérer tous les films :
    - Sélectionner les champs FILMID et TITRE depuis la table "film"
    - Stocker les résultats dans la variable `allMovies`

5. Affichage HTML :
    a. Afficher le titre de la page : "Liste des Films"
    b. Vérifier si `allMovies` est vide :
        - Si oui, afficher "Aucun film disponible pour le moment"
        - Sinon, afficher un tableau listant :
            - Le titre du film
            - Un bouton/lien pour consulter les horaires du film
            - Un bouton/lien pour modifier les informations du film
            - Un bouton/lien pour supprimer le film

6. Pour chaque film, ajouter un formulaire de suppression caché
    - Utiliser JavaScript pour afficher une boîte de confirmation avant suppression
    - Si confirmé, soumettre le formulaire vers `deleteMovie.php`

7. Afficher un lien pour ajouter un nouveau film (`editMovie.php`)
8. Afficher un lien pour retourner à la page d'accueil

Fin du programme
========================================================================
*/

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
                        <th>Consulter les séances</th>
                        <th>Modifier</th>
                        <th>Supprimer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allMovies as $movie): ?>
                        <tr>
                            <td><?= htmlspecialchars($movie['TITRE']) ?></td>
                            <td>
                                <a href="movieShowTimes.php?filmId=<?= $movie['FILMID'] ?>" class="btn">Voir les horaires</a>
                            </td>
                            <td>
                                <a href="editMovie.php?filmId=<?= $movie['FILMID'] ?>">
                                    <img src="../images/modifyIcon.png" alt="Modifier" style="width: 12px; height: 12px;">
                                    Modifier
                                </a>
                            </td>
                            <td>
                                <img src="../images/deleteIcon.png" alt="Supprimer" style="width: 12px; height: 12px;">
                                <a href="#" onclick="supprimerFilm(<?= $movie['FILMID'] ?>); return false;">Supprimer</a>
                                <form id="formulaireSupprimerFilm-<?= $movie['FILMID'] ?>" action="deleteMovie.php" method="post" style="display: none;">
                                    <input type="hidden" name="filmId" value="<?= $movie['FILMID'] ?>">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="editMovie.php">Ajouter un film</a>
        <p><a href="index.php">Retour à l'accueil</a></p>
    </main>
    <footer>
        <p>&copy; 2025 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
    
    <script>
    function supprimerFilm(filmId) {
        if (confirm("Voulez-vous vraiment supprimer ce film ?")) {
            document.getElementById("formulaireSupprimerFilm-" + filmId).submit();
        }
    }
    </script>
</body>
</html>
