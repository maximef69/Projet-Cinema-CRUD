<?php

/*
========================================================================
                                ALGO
                    POUR AJOUTER OU MODIFIER UN CINÉMA
========================================================================

Variables :
    nom, adresse : Chaîne
    cinemaId : Entier
    modification : Booléen ← FAUX
    erreur : Chaîne ← ''

Début du programme :

1. Connexion à la base de données (via fichier config.php)

2. Si un cinemaId est passé en GET (dans l’URL)
    ALORS
        modification ← VRAI
        cinemaId ← valeur de $_GET['cinemaId']

        Requête SELECT pour récupérer les infos du cinéma
        SI le cinéma existe
            nom ← DENOMINATION du cinéma
            adresse ← ADRESSE du cinéma
        SINON
            erreur ← "Cinéma introuvable."
        FIN SI
    FIN SI

3. Si le formulaire est soumis (méthode POST)
    ALORS
        Vérifier que les champs "nameCinema" et "adresseCinema" sont remplis

        Si les champs sont valides ALORS
            Nettoyer les valeurs (trim)

            Si on est en mode modification ALORS
                Exécuter une requête UPDATE pour modifier les infos du cinéma
            SINON
                Exécuter une requête INSERT pour ajouter un nouveau cinéma
            FIN SI

            Rediriger vers la page cinemasList.php
        SINON
            erreur ← "Veuillez remplir tous les champs."
        FIN SI
    FIN SI

4. Affichage HTML :
    - Afficher un titre conditionnel : "Modifier un cinéma" ou "Ajouter un cinéma"
    - Afficher le formulaire pré-rempli en cas de modification
    - Afficher le message d’erreur s’il y en a un
    - Lien vers la liste des cinémas

Fin du programme
========================================================================
*/



require_once 'config.php';

$erreur = '';
$nom = '';
$adresse = '';
$modification = false;

// Si un cinemaId est passé en GET, on est en mode édition
if (isset($_GET['cinemaId'])) {
    $modification = true;
    $cinemaId = $_GET['cinemaId'];

    // Charger les infos du cinéma existant
    $sql = "SELECT * FROM cinema WHERE CINEMAID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $cinemaId]);
    $cinema = $stmt->fetch();

    if ($cinema) {
        $nom = $cinema['DENOMINATION'];
        $adresse = $cinema['ADRESSE'];
    } else {
        $erreur = "Cinéma introuvable.";
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envoyer'])) {
    if (!empty($_POST['nameCinema']) && !empty($_POST['adresseCinema'])) {
        $nom = trim($_POST['nameCinema']);
        $adresse = trim($_POST['adresseCinema']);

        // Vérifier si on est en mode modification
        if ($modification) {
            // Mise à jour
            $sql = "UPDATE cinema SET DENOMINATION = :nom, ADRESSE = :adresse WHERE CINEMAID = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nom' => $nom,
                'adresse' => $adresse,
                'id' => $cinemaId
            ]);
        } else {
            // Insertion
            $sql = "INSERT INTO cinema (DENOMINATION, ADRESSE) VALUES (:nom, :adresse)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nom' => $nom,
                'adresse' => $adresse
            ]);
        }

        header("Location: cinemasList.php");
        exit();
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $modification ? 'Modifier un cinéma' : 'Ajouter un cinéma' ?></title>
    <link rel="stylesheet" href="css/cinema.css">
</head>
<body>
    <header>
        <h1><?= $modification ? 'Modifier un cinéma' : 'Ajouter un cinéma' ?></h1>
    </header>
    <main>
        <?php if (!empty($erreur)): ?>
            <p style="color: red;"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <h2><?= $modification ? 'Modifier les informations du cinéma' : 'Votre cinéma n\'existe pas ! Créez-le !' ?></h2>

        <form method="post">
            <fieldset>
                <?php if ($modification): ?>
                    <input type="hidden" name="cinemaId" value="<?= htmlspecialchars($cinemaId) ?>">
                <?php endif; ?>

                <label for="nameCinema">Nom du cinéma :
                    <input type="text" id="nameCinema" name="nameCinema" required value="<?= htmlspecialchars($nom) ?>">
                </label>

                <label for="adresseCinema">L'adresse du cinéma :
                    <textarea name="adresseCinema" id="adresseCinema" required><?= htmlspecialchars($adresse) ?></textarea>
                </label><br>

                <input type="submit" name="envoyer" value="<?= $modification ? 'Modifier' : 'Le créer' ?>"><br>
            </fieldset>
        </form>

        <a href="cinemasList.php">Retour à la liste des cinémas</a><br>
    </main>
    <footer>
        <p>&copy; 2025 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
</body>
</html>
