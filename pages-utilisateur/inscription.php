<?php

require_once '../bd.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!empty($_SESSION['user_logged_in'])) {
    header('Location: ../index.php');
    exit();
}


$erreurs = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $telephone = trim($_POST['telephone'] ?? '');
    $rue = trim($_POST['rue'] ?? '');
    $cp = trim($_POST['cp'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $pays = trim($_POST['pays'] ?? '');
    $motdepasse = $_POST['motdepasse'] ?? '';
    $motdepasse_confirm = $_POST['motdepasse-confirm'] ?? '';


    if (
        empty($nom) || empty($prenom) || empty($email) || empty($telephone) ||
        empty($rue) || empty($cp) || empty($ville) || empty($pays) ||
        empty($motdepasse) || empty($motdepasse_confirm)
    ) {
        $erreurs[] = "Tous les champs sont obligatoires.";
    }


    if ($motdepasse !== $motdepasse_confirm) {
        $erreurs[] = "Les mots de passe ne correspondent pas.";
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'adresse email est invalide.";
    }


    if (strlen($motdepasse) < 10) {
        $erreurs[] = "Le mot de passe doit contenir au moins 10 caractères.";
    }
    if (!preg_match('/[A-Z]/', $motdepasse)) {
        $erreurs[] = "Le mot de passe doit contenir au moins une majuscule.";
    }
    if (!preg_match('/[a-z]/', $motdepasse)) {
        $erreurs[] = "Le mot de passe doit contenir au moins une minuscule.";
    }
    if (!preg_match('/[0-9]/', $motdepasse)) {
        $erreurs[] = "Le mot de passe doit contenir au moins un chiffre.";
    }
    if (!preg_match('/[\W_]/', $motdepasse)) {
        $erreurs[] = "Le mot de passe doit contenir au moins un caractère spécial (ex: !@#$%^&*).";
    }

    $telephone = trim($_POST['telephone'] ?? '');
    $telephone_clean = preg_replace('/[^0-9]/', '', $telephone); // Supprime tout sauf les chiffres

    if (!preg_match('/^0[1-9]\d{8}$/', $telephone_clean)) {
        $erreurs[] = "Le numéro de téléphone doit être un format français valide (ex: 0612345678 ou 06.12.34.56.78).";
    }

    if (!preg_match('/^\d{5}$/', $cp)) {
        $erreurs[] = "Le code postal doit contenir 5 chiffres.";
    }


    if (empty($erreurs)) {
        try {

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM client WHERE Mail = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetchColumn() > 0) {
                $erreurs[] = "Cet email est déjà utilisé.";
            }


            if (empty($erreurs)) {
                $motdepasse_hache = password_hash($motdepasse, PASSWORD_DEFAULT);

                $sql = "INSERT INTO client (`Nom`, `Prénom`, `Mail`, `Téléphone`, `MotDePasse`, `Rôle`, `RUE`, `CP`, `Ville`, `Pays`)
                        VALUES (:nom, :prenom, :email, :telephone, :motdepasse, 'Client', :rue, :cp, :ville, :pays)";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':email' => $email,
                    ':telephone' => $telephone,
                    ':motdepasse' => $motdepasse_hache,
                    ':rue' => $rue,
                    ':cp' => $cp,
                    ':ville' => $ville,
                    ':pays' => $pays
                ]);
                $subject = "Bienvenue sur Vite Et Gourmand";
                $message = "Merci de votre inscription sur notre site Vite Et Gourmand !";
                $headers = "From: no-reply@viteetgourmand.fr\r\n";
                $headers .= "Reply-To: no-reply@viteetgourmand.fr\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                //mail($email, $subject, $message, $headers) PAS DENVOI CAR PAS DE SERVEUR MAIL
                header("Location: login.php?inscription=success");
                exit();
            }
        } catch (PDOException $e) {

            error_log("Erreur inscription : " . $e->getMessage());
            $erreurs[] = "Une erreur technique est survenue. Réessayez plus tard.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - Vite et Gourmand</title>
    <meta name="description"
        content="Créez votre compte Vite et Gourmand pour commander facilement vos plats faits maison.">

    <!-- CSS -->
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/inscription.css">
    <link rel="stylesheet" href="../CSS/responsive.css">

    <!-- script JS -->
     <script src="../JavaScript/responsive.js" defer></script>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php include('../header.php'); ?>

    <main class="main-inscription">
        <h1 class="titre-encadrer titre-inscription">Rejoignez Vite et Gourmand !</h1>
        <p class="phrase-italique">Complétez votre inscription pour pouvoir passer vos commandes facilement.</p>

        <?php if (!empty($erreurs)): ?>
            <div class="erreurs" style="background:#ffebee; color:#c62828; padding:15px; border-radius:8px; margin:20px 0;">
                <ul style="margin:0; padding-left:20px;">
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?= htmlspecialchars($erreur) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="formulaire-inscription" method="post" action="" novalidate>
            <fieldset>
                <legend>Formulaire d’inscription</legend>

                <!-- Champs du formulaire (inchangés, mais avec value pour persistance) -->
                <div>
                    <label for="nom">Nom :</label>
                    <input id="nom" type="text" name="nom" value="<?= htmlspecialchars($nom ?? '') ?>" required>
                </div>

                <div>
                    <label for="prenom">Prénom :</label>
                    <input id="prenom" type="text" name="prenom" value="<?= htmlspecialchars($prenom ?? '') ?>"
                        required>
                </div>

                <div>
                    <label for="email">Email :</label>
                    <input id="email" type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>"
                        placeholder="exemple@email.com" required>
                </div>

                <div>
                    <label for="telephone">Téléphone :</label>
                    <input id="telephone" type="tel" name="telephone" value="<?= htmlspecialchars($telephone ?? '') ?>"
                        placeholder="0601020304" required>
                </div>

                <div>
                    <label for="rue">Rue :</label>
                    <input id="rue" type="text" name="rue" value="<?= htmlspecialchars($rue ?? '') ?>" required>
                </div>

                <div>
                    <label for="cp">Code Postal :</label>
                    <input id="cp" type="text" name="cp" value="<?= htmlspecialchars($cp ?? '') ?>" maxlength="5"
                        required>
                </div>

                <div>
                    <label for="ville">Ville :</label>
                    <input id="ville" type="text" name="ville" value="<?= htmlspecialchars($ville ?? '') ?>" required>
                </div>

                <div>
                    <label for="pays">Pays :</label>
                    <input id="pays" type="text" name="pays" value="<?= htmlspecialchars($pays ?? 'France') ?>"
                        required>
                </div>

                <div class="mdp-et-phrase-informative">
                    <label for="motdepasse">Mot de passe :</label>
                    <input id="motdepasse" type="password" name="motdepasse" required>
                </div>
                <small class="infoMotDePasse">
                    Au moins 10 caractères : majuscule, minuscule, chiffre et caractère spécial (!@#$%^&* etc.).
                </small>

                <div>
                    <label for="motdepasse-confirm">Confirmez le mot de passe :</label>
                    <input id="motdepasse-confirm" type="password" name="motdepasse-confirm" required>
                </div>
            </fieldset>

            <p>Déjà inscrit.e ? <a href="login.php">Connectez-vous !</a></p>

            <input class="btn-inscription" type="submit" value="Inscription">
        </form>
    </main>

    <?php include('../footer.php'); ?>
</body>

</html>