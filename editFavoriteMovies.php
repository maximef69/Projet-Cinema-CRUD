<?php
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
        <ul>
            <?php if (empty($favoriteMovies)): ?>
                <p>Aucun film favori pour le moment.</p>
            <?php else: ?>
                <?php foreach ($favoriteMovies as $movie): ?>
                    <li>
                        <strong><?= htmlspecialchars($movie['TITRE']) ?></strong>
                        <p>Commentaire : <?= htmlspecialchars($movie['COMMENTAIRE']) ?></p>
                        
                        <a href="deleteFavoriteMovie.php?filmId=<?= $movie['FILMID'] ?>">
                        <img src="../images/deleteIcon.png" alt="Supprimer" style="width: 12px; height: 12px; margin-right: 6px;">
                        </a>
                        Supprimer

                    
                        <a href="updateFavoriteMovie.php?filmId=<?= $movie['FILMID'] ?>">
                        <img src="../images/modifyIcon.png" alt="Modifier" style="width: 12px; height: 12px;">
                        </a>
                        Modifier
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

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

            <button type="submit" name="add_film">Ajouter</button>
        </form>

        <p><a href="index.php">Retour à l'accueil</a></p>
    </main>
    <footer>
        <p>&copy; 2025 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
</body>
</html>
