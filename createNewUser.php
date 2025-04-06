<?php
require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Vérifier que les mots de passe correspondent
    if ($password !== $confirmPassword) {
        die("Erreur : Les mots de passe ne correspondent pas.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if (!isset($pdo)) {
        die("Erreur : Connexion à la base de données non établie.");
    }

    // Insérer l'utilisateur dans la base de données
    $sql = "INSERT INTO utilisateur (NOM, PRENOM, ADRESSECOURRIEL, PASSWORD) VALUES (:name, :surname, :email, :password)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'name' => $name,
        'surname' => $surname,
        'email' => $email,
        'password' => $hashedPassword
    ]);

    header("Location: index.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Nouvel Utilisateur</title>
    <link rel="stylesheet" href="css/cinema.css">
</head>
<body>
    <header>
        <h1>Bienvenue dans l'application de gestion de cinéma</h1>
    </header>
    <main>
        <p>Gérez votre cinéma efficacement avec notre application. Vous pouvez ajouter, mettre à jour et supprimer des enregistrements de films, gérer les horaires des séances, et bien plus encore.</p>

        <form action="createNewUser.php" method="post">
            <label for="name">Nom</label><br>
            <input type="text" id="name" name="name" required /><br>

            <label for="surname">Prénom</label><br>
            <input type="text" id="surname" name="surname" required /><br>

            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" required /><br>

            <label for="password">Mot de passe</label><br>
            <input type="password" id="password" name="password" required /><br>

            <label for="confirm_password">Confirmer le mot de passe</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required /><br>

            <input type="submit" name="envoyer" value="Créer l'utilisateur" />
        </form>

        <p><a href="index.php">Retour à la page d'accueil</a></p>
    </main>
    <footer>
        <p>&copy; 2025 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
</body>
</html>
