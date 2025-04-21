<?php
/*


========================================================================
                             ALGO
========================================================================

Variables :
    titre, titreOriginal : Chaîne
    filmId : Entier
    modification : Booléen ← FAUX
    erreur : Chaîne ← ''

Début du programme :

1. Connexion à la base de données (via fichier config.php)

2. Si filmId est présent dans l'URL (GET)
    ALORS
        modification ← VRAI
        filmId ← valeur de $_GET['filmId']

        Exécuter une requête SELECT pour récupérer le film ayant l'ID filmId

        SI un film est trouvé ALORS
            titre ← titre du film
            titreOriginal ← titre original du film
        SINON
            erreur ← "Film introuvable."
        FIN SI
    FIN SI

3. Si le formulaire est soumis (méthode POST)
    ALORS
        Vérifier que les champs "titre" et "titreOriginal" ne sont pas vides

        Si les champs sont remplis ALORS
            Enlever les espaces au début et à la fin des deux champs

            SI filmId est présent dans POST ALORS
                modification ← VRAI
                filmId ← valeur de $_POST['filmId']
            FIN SI

            SI modification = VRAI ALORS
                Exécuter une requête UPDATE pour modifier les données du film
            SINON
                Exécuter une requête INSERT pour ajouter un nouveau film
            FIN SI

            Rediriger vers la page moviesList.php
        SINON
            erreur ← "Veuillez remplir tous les champs."
        FIN SI
    FIN SI

4. Affichage du formulaire HTML :
    - Affiche le titre du formulaire selon qu’on est en mode ajout ou modification
    - Si en modification, inclure un champ caché contenant l'ID du film
    - Pré-remplir les champs "titre" et "titreOriginal" si disponible
    - Afficher un bouton : "Modifier" ou "Ajouter"
    - Afficher un lien vers la liste des films

Fin du programme
========================================================================

*/

require_once 'config.php';

$erreur = '';
$titre = '';
$titreOriginal = '';
$modification = false;

// Si un FILMID est passé en GET, on est en mode modification
if (isset($_GET['filmId'])) {
    $modification = true;
    $filmId = $_GET['filmId'];

    // Charger les infos du film existant
    $sql = "SELECT * FROM film WHERE FILMID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $filmId]);
    $film = $stmt->fetch();

    if ($film) {
        $titre = $film['TITRE'];
        $titreOriginal = $film['TITREORIGINAL'];
    } else {
        $erreur = "Film introuvable.";
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envoyer'])) {
    if (!empty($_POST['titre']) && !empty($_POST['titreOriginal'])) {
        $titre = trim($_POST['titre']);
        $titreOriginal = trim($_POST['titreOriginal']);

        if (isset($_POST['filmId'])) {
            $modification = true;
            $filmId = $_POST['filmId'];
        }

        if ($modification) {
            // Mise à jour
            $sql = "UPDATE film SET TITRE = :titre, TITREORIGINAL = :titreOriginal WHERE FILMID = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'titre' => $titre,
                'titreOriginal' => $titreOriginal,
                'id' => $filmId
            ]);
        } else {
            // Insertion
            $sql = "INSERT INTO film (TITRE, TITREORIGINAL) VALUES (:titre, :titreOriginal)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'titre' => $titre,
                'titreOriginal' => $titreOriginal
            ]);
        }

        header("Location: moviesList.php");
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
    <title><?= $modification ? 'Modifier un film' : 'Ajouter un film' ?></title>
    <link rel="stylesheet" href="css/cinema.css">
</head>
<body>
    <header>
        <h1><?= $modification ? 'Modifier un film' : 'Ajouter un film' ?></h1>
    </header>
    <main>
        <?php if (!empty($erreur)): ?>
            <p style="color: red;"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="post">
            <fieldset>
                <?php if ($modification): ?>
                    <input type="hidden" name="filmId" value="<?= htmlspecialchars($filmId ?? '') ?>">
                <?php endif; ?>

                <label for="titre">Titre du film :
                    <input type="text" id="titre" name="titre" required value="<?= htmlspecialchars($titre ?? '') ?>">
                </label>

                <label for="titreOriginal">Titre original :
                    <input type="text" id="titreOriginal" name="titreOriginal" required value="<?= htmlspecialchars($titreOriginal ?? '') ?>">
                </label><br>

                <input type="submit" name="envoyer" value="<?= $modification ? 'Modifier' : 'Ajouter' ?>"><br>
            </fieldset>
        </form>

        <a href="moviesList.php">Retour à la liste des films</a><br>
    </main>
    <footer>
        <p>&copy; 2025 Gestion de Cinéma. Tous droits réservés.</p>
    </footer>
</body>
</html>
