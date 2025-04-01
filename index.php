<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'config.php'; 
session_start(); 

$sessionActive = isset($_SESSION['user_id']); // Vérifier si l'utilisateur est connecté

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$sessionActive) {

    // Récupérer les données du formulaire

    $emailUser = $_POST['email'];
    $password = $_POST['password'];

    // Requête pour récupérer l'utilisateur depuis la base de données
    
    $sql = "SELECT USERID, ADRESSECOURRIEL, PASSWORD FROM utilisateur WHERE ADRESSECOURRIEL = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $emailUser]);
    $user = $stmt->fetch();

    if ($user) {

        if (password_verify($password, $user['PASSWORD'])) {
            // Stocker l'utilisateur dans la session
            $_SESSION['user_id'] = $user['USERID'];
            $_SESSION['email'] = $user['ADRESSECOURRIEL'];

            header("Location: index.php");
            exit();
        } else {
            $_SESSION['login_error'] = 'Mot de passe incorrect.';
        }
    } else {
        $_SESSION['login_error'] = 'Aucun utilisateur trouvé avec cet email.';
    }

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue dans la gestion de cinéma</title>
    <link rel="stylesheet" href="css/cinema.css">
</head>
<body>
    <header>
        <h1>Bienvenue dans l'application de gestion de cinéma</h1>
    </header>
    <main>
        <p>Gérez votre cinéma efficacement avec notre application. Vous pouvez ajouter, mettre à jour et supprimer des enregistrements de films, gérer les horaires des séances, et bien plus encore.</p>

        <?php if ($sessionActive): ?>
            <p>Connecté en tant que <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong></p>
            <a href="editFavoriteMovies.php">Éditer ma liste de films préférés</a><br>
            <a href="logout.php">Se déconnecter</a>
        <?php else: ?>
            <?php if (isset($_SESSION['login_error'])): ?>
                <p style="color:red;"><?php echo $_SESSION['login_error']; ?></p>
                <?php unset($_SESSION['login_error']);  ?>
            <?php endif; ?>

            <form action="index.php" method="post">
                <label for="email">Email</label><br>
                <input type="email" id="email" name="email" required /><br>

                <label for="password">Mot de passe</label><br>
                <input type="password" id="password" name="password" required /><br>

                <input type="submit" name="envoyer" value="Se connecter">
            </form>

            <a href="createNewUser.php">Nouvel utilisateur</a>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2023 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
</body>
</html>
