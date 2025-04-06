<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_comment'])) {
    $filmId = $_POST['filmId'];
    $comment = $_POST['comment'];

    $sql = "UPDATE prefere SET COMMENTAIRE = :comment WHERE USERID = :userId AND FILMID = :filmId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['comment' => $comment, 'userId' => $userId, 'filmId' => $filmId]);

    header("Location: editFavoriteMovies.php");
    exit();
}

// Récupérer l'ID du film depuis l'URL
if (isset($_GET['filmId'])) {
    $filmId = $_GET['filmId'];

    // Récupérer le film et son commentaire pour cet utilisateur
    $sql = "SELECT p.FILMID, f.TITRE, p.COMMENTAIRE FROM prefere p JOIN film f ON p.FILMID = f.FILMID WHERE p.USERID = :userId AND p.FILMID = :filmId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId, 'filmId' => $filmId]);
    $movie = $stmt->fetch();

    if (!$movie) {
        // Si aucun film n'est trouvé pour cet utilisateur, rediriger vers la page d'accueil
        header("Location: editFavoriteMovies.php");
        exit();
    }
} else {
    // Si aucun filmId n'est passé dans l'URL, rediriger vers la page d'accueil
    header("Location: editFavoriteMovies.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le commentaire</title>
    <link rel="stylesheet" href="css/cinema.css">
</head>
<body>
    <header>
        <h1>Modifier le commentaire sur le film</h1>
    </header>
    <main>
        <h2>Modifier le commentaire pour le film : <?= htmlspecialchars($movie['TITRE']) ?></h2>

        <form action="updateFavoriteMovie.php" method="post">
            <input type="hidden" name="filmId" value="<?= $movie['FILMID'] ?>">

            <label for="comment">Votre commentaire :</label><br>
            <textarea name="comment" id="comment" rows="4" cols="50"><?= htmlspecialchars($movie['COMMENTAIRE']) ?></textarea><br>

            <button type="submit" name="update_comment">Mettre à jour le commentaire</button>
        </form>

        <p><a href="editFavoriteMovies.php">Retour à la liste des films favoris</a></p>
    </main>
    <footer>
        <p>&copy; 2025 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
</body>
</html>
