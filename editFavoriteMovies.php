<?php
/*
========================================================================
                         ALGO
        POUR MODIFIER LA LISTE DE FILMS PRÉFÉRÉS
========================================================================

Début du programme

1. Démarrer la session
2. Inclure le fichier de configuration pour la base de données

3. Vérifier si l'utilisateur est connecté
   a. Si l'utilisateur n'est pas connecté, rediriger vers la page d'accueil (index.php)

4. Récupérer l'ID de l'utilisateur à partir de la session

5. Récupérer les films favoris de l'utilisateur dans la base de données
   a. Exécuter une requête pour récupérer le FILMID, TITRE, et COMMENTAIRE pour chaque film préféré de l'utilisateur

6. Récupérer tous les films disponibles dans la base de données
   a. Exécuter une requête pour récupérer FILMID et TITRE de tous les films

7. Si aucun film préféré n'est trouvé pour l'utilisateur, afficher un message "Aucun film favori pour le moment."

8. Si des films sont trouvés, les afficher dans un tableau avec :
   a. Le titre du film
   b. Le commentaire de l'utilisateur
   c. Un lien pour supprimer ou modifier ce film

9. Afficher un formulaire pour permettre à l'utilisateur d'ajouter un nouveau film à sa liste de favoris
   a. Afficher un menu déroulant avec tous les films disponibles
   b. Ajouter un champ de texte pour permettre à l'utilisateur d'ajouter un commentaire

10. Lorsque le formulaire est soumis, traiter l'ajout du film à la liste des favoris
    a. Vérifier la validité de l'entrée et insérer le film sélectionné et le commentaire dans la base de données

11. Afficher des liens de retour vers l'accueil

Fin du programme
========================================================================
*/

session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Récupérer les films préférés de l'utilisateur
$sql = "SELECT f.FILMID, f.TITRE, p.COMMENTAIRE FROM prefere p JOIN film f ON p.FILMID = f.FILMID WHERE p.USERID = :userId";
$stmt = $pdo->prepare($sql);
$stmt->execute(['userId' => $userId]);
$favoriteMovies = $stmt->fetchAll();

// Récupérer tous les films
$sql = "SELECT FILMID, TITRE FROM film";
$stmt = $pdo->query($sql);
$allMovies = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier ma liste de films préférés</title>
    <link rel="stylesheet" href="css/cinema.css">
</head>
<body>
    <header>
        <h1>Modifier ma liste de films préférés</h1>
    </header>
    <main>
        <p>Gérez vos films favoris et ajoutez un commentaire personnel.</p>

        <h2>Vos films favoris</h2>
        <?php if (empty($favoriteMovies)): ?>
    <p>Aucun film favori pour le moment.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Commentaire</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($favoriteMovies as $movie): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($movie['TITRE']) ?></strong></td>
                    <td><?= htmlspecialchars($movie['COMMENTAIRE']) ?></td>
                    <td>
                        <a href="deleteFavoriteMovie.php?filmId=<?= $movie['FILMID'] ?>">
                            <img src="../images/deleteIcon.png" alt="Supprimer" style="width: 12px; height: 12px; margin-right: 6px;">
                            Supprimer
                        </a>
                        
                        <a href="updateFavoriteMovie.php?filmId=<?= $movie['FILMID'] ?>">
                            <img src="../images/modifyIcon.png" alt="Modifier" style="width: 12px; height: 12px;">
                            Modifier
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

        <h2>Ajouter un film à vos favoris</h2>
        <form action="addFavoriteMovie.php" method="post">
            <label for="filmId">Sélectionnez un film :</label>
            <select name="filmId" id="filmId">
                <?php foreach ($allMovies as $movie): ?>
                    <option value="<?= $movie['FILMID'] ?>"><?= htmlspecialchars($movie['TITRE']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="comment">Votre commentaire :</label>
            <textarea name="comment" id="comment" rows="2" cols="40"></textarea>

            <button type="submit" name="ajouterFilm">Ajouter</button>
        </form>

        <p><a href="index.php">Retour à l'accueil</a></p>
    </main>
    <footer>
        <p>&copy; 2025 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
</body>
</html>
