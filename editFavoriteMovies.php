<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_film'])) {
    $filmId = $_POST['filmId'];
    $sql = "INSERT INTO prefere (USERID, FILMID, COMMENTAIRE) VALUES (:userId, :filmId, '')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId, 'filmId' => $filmId]);
    header("Location: editFavoriteMovies.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove_film'])) {
    $filmId = $_POST['filmId'];
    $sql = "DELETE FROM prefere WHERE USERID = :userId AND FILMID = :filmId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId, 'filmId' => $filmId]);
    header("Location: editFavoriteMovies.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_comment'])) {
    $filmId = $_POST['filmId'];
    $comment = $_POST['comment'];
    $sql = "UPDATE prefere SET COMMENTAIRE = :comment WHERE USERID = :userId AND FILMID = :filmId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['comment' => $comment, 'userId' => $userId, 'filmId' => $filmId]);
    header("Location: editFavoriteMovies.php");
    exit();
}


$sql = "SELECT f.FILMID, f.TITRE, p.COMMENTAIRE FROM prefere p JOIN film f ON p.FILMID = f.FILMID WHERE p.USERID = :userId";
$stmt = $pdo->prepare($sql);
$stmt->execute(['userId' => $userId]);
$favoriteMovies = $stmt->fetchAll();


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
            <?php foreach ($favoriteMovies as $movie): ?>
                <li>
                    <strong><?= htmlspecialchars($movie['TITRE']) ?></strong>
                    
                    
                    <form action="editFavoriteMovies.php" method="post" style="display:inline;">
                        <input type="hidden" name="filmId" value="<?= $movie['FILMID'] ?>">
                        <button type="submit" name="remove_film">Supprimer</button>
                    </form>

                    
                    <form action="editFavoriteMovies.php" method="post" style="margin-top: 5px;">
                        <input type="hidden" name="filmId" value="<?= $movie['FILMID'] ?>">
                        <textarea name="comment" rows="2" cols="40"><?= htmlspecialchars($movie['COMMENTAIRE']) ?></textarea>
                        <button type="submit" name="update_comment">Mettre à jour</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <h2>Ajouter un film à vos favoris</h2>
        <form action="editFavoriteMovies.php" method="post">
            <select name="filmId">
                <?php foreach ($allMovies as $movie): ?>
                    <option value="<?= $movie['FILMID'] ?>"><?= htmlspecialchars($movie['TITRE']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="add_film">Ajouter</button>
        </form>

        <p><a href="index.php">Retour à l'accueil</a></p>
    </main>
</body>
</html>
