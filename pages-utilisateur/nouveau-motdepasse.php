<!DOCTYPE html>
<html lang="fr">
<?php

require_once '../bd.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (isset($_GET['mdpTemp'])) {
    $mdpTemp = $_GET['mdpTemp'];
    date_default_timezone_set('Europe/Paris');
    try {

        $stmt = $pdo->prepare("
    SELECT *
    FROM resetmdp
    WHERE MDP_temp = :mdpTemp
    AND temp > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    LIMIT 1
");

        $stmt->execute(['mdpTemp' => $mdpTemp]);

        $resetMDP = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resetMDP) {

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $newPassword = $_POST['motdepasse'];
                $confirmPassword = $_POST['motdepasse-confirm'];

                if ($newPassword === $confirmPassword) {

                    if (strlen($newPassword) >= 10 && preg_match('/[A-Z]/', $newPassword) && preg_match('/[a-z]/', $newPassword) && preg_match('/[0-9]/', $newPassword) && preg_match('/[\W_]/', $newPassword)) {
                        
                        // Mettre à jour le mot de passe dans la base de données
                        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE client SET `MotDePasse` = :motdepasse WHERE id_client = :user_id");
                        $stmt->execute([':motdepasse' => $hashedPassword, ':user_id' => $resetMDP['id_client']]);

                        // Supprimer après utilisation
                        $stmt = $pdo->prepare("DELETE FROM resetmdp WHERE MDP_temp = :mdpTemp");
                        $stmt->execute(['mdpTemp' => $mdpTemp]);

                        echo "<p class='success-message'>Votre mot de passe a été réinitialisé avec succès.</p>";


                        header("Location: login.php");
                        exit();
                    } else {
                        echo "<p class='error-message'>Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.</p>";
                    }
                } else {
                    echo "<p class='error-message'>Les mots de passe ne correspondent pas.</p>";
                }
            }
        } else {
            echo "<p class='error-message'>Le jeton est invalide ou expiré.</p><a href='/ECF-vite-et-gourmand/'>Accueil</a>";
            exit;
        }
    } catch (PDOException $e) {
        echo "<p class='error-message'>Erreur : " . $e->getMessage() . "</p>";
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe - Vite et Gourmand</title>
    <meta name="description"
        content="Vite et Gourmand, traiteur à Bordeaux depuis 25 ans. Créer votre nouveau mot de passe">
    <meta name="author" content="Alexandra Riviere">

    <!-- CSS -->
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/nouveau-motdepasse.css">
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

    <main class="marges-specifique">
        <h1 class="titre-centrer">Réinitialisation de votre mot de passe</h1>

        <form action="" method="POST" class="reinitialiser-mdp">
            <fieldset>
                <legend>Créer un nouveau mot de passe</legend>

                <div>
                    <label for="nouveau-motdepasse">Mot de passe :</label>
                    <input id="nouveau-motdepasse" type="password" name="motdepasse" aria-describedby="infoMotDePasse"
                        required>
                </div>
                <small id="infoMotDePasse">
                    Le mot de passe doit contenir au moins 10 caractères, dont une majuscule, une minuscule, un
                    chiffre et un caractère spécial.
                </small>

                <div>
                    <label for="nouveau-motdepasse-confirm">Confirmez votre mot de passe :</label>
                    <input id="nouveau-motdepasse-confirm" type="password" name="motdepasse-confirm"
                        aria-describedby="infoMotDePasse" required>
                </div>
                <div>
                    <button type="submit" class="btn-nv-mdp">Valider</button>
                </div>
            </fieldset>
        </form>
    </main>

    <?php include('../footer.php'); ?>
</body>

</html>