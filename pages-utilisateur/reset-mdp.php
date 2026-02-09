<!DOCTYPE html>
<?php

require_once '../bd.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];



    try {
        // Vérification si l'email existe dans la base de données
        $stmt = $pdo->prepare("SELECT id_client, Mail FROM client WHERE Mail = :email");
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            $mdpTemp = bin2hex(random_bytes(50)); // Crée un mdp aléatoire
            date_default_timezone_set('Europe/Paris');

            $stmt = $pdo->prepare("INSERT INTO resetmdp (id_client, MDP_temp, temp) VALUES (:id_client, :MDP_temp, NOW())");
            $stmt->execute([
                'id_client' => $user['id_client'],
                'MDP_temp' => $mdpTemp
            ]);

            // Envoyer un e-mail avec le lien de réinitialisation
            $resetLink = "http://localhost/ECF-vite-et-gourmand/pages-utilisateur/nouveau-motdepasse.php?mdpTemp=$mdpTemp"; // Remplacez par l'URL réelle de la page de réinitialisation
            $subject = "Réinitialisation de votre mot de passe";
            $message = "Cliquez sur le lien suivant pour réinitialiser votre mot de passe :\n$resetLink";
            $headers = "From: no-reply@viteetgourmand.fr\r\n";
            $headers .= "Reply-To: no-reply@viteetgourmand.fr\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            // if (mail($email, $subject, $message, $headers)) {
            //    echo "<p class='success-message'>Un lien de réinitialisation a été envoyé à votre adresse e-mail.</p>";
            //  } else {
            echo "<p class='error-message'>Une erreur est survenue lors de l'envoi de l'e-mail car utilisation de wampp et pas de serveur de mail.</p>";
            //affichage uniquement pour tester car par d'envoi de mail
            echo "<p class='error-message'>Cliquez sur le lien suivant pour réinitialiser votre mot de passe :\n<a
                    href=$resetLink>$resetLink</a></p>";
            //  }

        } else {
            echo "<p class='error-message'>Aucun compte trouvé avec cet e-mail.</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='error-message'>Erreur : " . $e->getMessage() . "</p>";
    }


}

?>

<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Vite et Gourmand | Récupérez votre accès</title>
    <meta name="description"
        content="Réinitialisez votre mot de passe Vite et Gourmand en toute sécurité et retrouvez l’accès à votre compte en quelques instants.">
    <meta name="author" content="Alexandra Riviere">
    
    <!-- CSS -->
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/reset-mdp.css">
    <link rel="stylesheet" href="../CSS/responsive.css">

    <!-- script JS-->

<script src="../JavaScript/responsive.js" defer></script>
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php include('../header.php'); ?>

    <main class="marges-specifiques">
        <h1 class="titre-centrer">Réinitialisation du mot de passe</h1>
        <p class="texte-reset-mdp">Entrez votre adresse e-mail pour recevoir un lien de réinitialisation.</p>

        <form class="form-reset-mdp" action="" method="post">
            <fieldset>
                <legend>Réinitialiser le mot de passe</legend> <!-- a cacher plus tard -->
                <div>
                    <label for="email">E-mail :</label>
                    <input class="input-reset-mdp" type="email" id="email" name="email" required>
                </div>
                <button class="btn-reset-mdp" type="submit">Envoyer</button>

            </fieldset>
        </form>

        <p class="texte-reset-mdp"><a href="login.php">Retour à la page de connexion</a></p>
    </main>

    <?php include('../footer.php'); ?>

</body>

</html>