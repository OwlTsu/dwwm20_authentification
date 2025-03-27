<?php
session_start();

require __DIR__ . "/../functions/dbConnector.php";
require __DIR__ . "/../functions/authenticator.php";

// Si les données arrivent au serveur via la méthode POST
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    /**
     * *********************************************
     * Traitement des données du formulaire
     * *********************************************
     */

    // 1. Protéger le serveur contre les failles de type csrf
    if (!array_key_exists('csrf_token', $_POST)) {
        // Effectuer une redirection vers la page de laquelle proviennent les données
        // Arrêter l'exécution du script.
        return header("Location: register.php");
    }

    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        // Effectuer une redirection vers la page de laquelle proviennent les données
        // Arrêter l'exécution du script.
        return header("Location: register.php");
    }

    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])) {
        // Effectuer une redirection vers la page de laquelle proviennent les données
        // Arrêter l'exécution du script.
        return header("Location: register.php");
    }

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        // Effectuer une redirection vers la page de laquelle proviennent les données
        // Arrêter l'exécution du script.
        return header("Location: register.php");
    }


    // 2. Protéger le serveur contre les robots spameurs
    if (! array_key_exists('honey_pot', $_POST)) {
        // Effectuer une redirection vers la page de laquelle proviennent les données
        // Arrêter l'exécution du script.
        return header("Location: register.php");
    }

    if ($_POST['honey_pot'] !== "") {
        // Effectuer une redirection vers la page de laquelle proviennent les données
        // Arrêter l'exécution du script.
        return header("Location: register.php");
    }


    // 3. Définir les contraintes de validation
    $formErrors = [];

    if (isset($_POST['firstName'])) {
        if (trim($_POST['firstName']) == "") {
            $formErrors['firstName'] = "Le prénom est obligatoire.";
        } else if (mb_strlen($_POST['firstName']) > 255) {
            $formErrors['firstName'] = "Le prénom ne doit pas dépasser 255 caractères.";
        } else if (!preg_match("/^[0-9A-Za-zÀ-ÖØ-öø-ÿ' _-]+$/u", $_POST['firstName'])) {
            $formErrors['firstName'] = "Le prénom ne peut contenir que des chiffres, des lettres, le tiret du mieu et l'undescore.";
        }
    }

    if (isset($_POST['lastName'])) {
        if (trim($_POST['lastName']) == "") {
            $formErrors['lastName'] = "Le nom est obligatoire.";
        } else if (mb_strlen($_POST['lastName']) > 255) {
            $formErrors['lastName'] = "Le nom ne doit pas dépasser 255 caractères.";
        } else if (!preg_match("/^[0-9A-Za-zÀ-ÖØ-öø-ÿ' _-]+$/u", $_POST['lastName'])) {
            $formErrors['lastName'] = "Le nom ne peut contenir que des chiffres, des lettres, le tiret du mieu et l'undescore.";
        }
    }

    if (isset($_POST['email'])) {
        if (trim($_POST['email']) == "") {
            $formErrors['email'] = "L'email est obligatoire.";
        } else if (mb_strlen($_POST['email']) > 255) {
            $formErrors['email'] = "L'email ne doit pas dépasser 255 caractères.";
        } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) // Permet de valider le format de l'email
        {
            $formErrors['email'] = "Le format de l'email est invalide.";
        } else if (already_exists($_POST['email'], $db)) {
            $formErrors['email'] = "Impossible de créer un compte avec cet email.";
        }
    }

    var_dump($formErrors);
    die();



    // 4. Si le formulaire est soumis mais non valide
    // Effectuer une redirection vers la page de laquelle proviennent les données
    // Arrêter l'exécution du script.

    // Dans le contraire
    // 5. Etablir une connexion avec la base de données

    // 6. Effectuer la requête du nouvel utilisateur dans la base de données.

    // 7. Générer le message flash de succès

    // 8. Rediriger l'utilisateur vers la page de connexion
    // Arrêter l'exécution du script.
}

// Générons notre jéton de sécurité pour la clé csrf.
$_SESSION['csrf_token'] = bin2hex(random_bytes(10));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="bg-light">

    <!-- Main -->
    <main>
        <div class="container my-5">
            <div class="row">
                <div class="col-md-6">
                    <h1 class="text-center my-3 display-5">Inscription</h1>

                    <!-- Form -->
                    <form method="post">
                        <div class="mb-3">
                            <input type="text" name="firstName" placeholder="Votre prénom" class="form-control" autofocus>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="lastName" placeholder="Votre nom" class="form-control">
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" placeholder="Votre email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" placeholder="Votre mot de passe" class="form-control">
                        </div>
                        <div class="mb-3">
                            <input type="password" name="confirmPassword" placeholder="Confirmation de votre mot de passe" class="form-control">
                        </div>
                        <div>
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        </div>
                        <div>
                            <input type="hidden" name="honey_pot" value="">
                        </div>
                        <div>
                            <input type="submit" class="btn btn-primary w-100" value="S'inscrire">
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <p>Vous avez déjà un compte? <a href="/login.php">Connectez-vous</a></p>
                        <a href="/index.php">Revenir à l'accueil</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Image -->
                    <img src="/images/register.png" class="img-fluid" alt="">
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>