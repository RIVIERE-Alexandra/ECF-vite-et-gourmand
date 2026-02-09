<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../bd.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user_admin'] === false) {
    // Récupérer et sécuriser les données
    $nom = trim(htmlspecialchars($_POST['nom']));
    $email = trim(htmlspecialchars($_POST['email']));
    $titre = trim(htmlspecialchars($_POST['titre']));
    $message = trim(htmlspecialchars($_POST['message']));

    // Vérifier que les champs ne sont pas vides
    if (!empty($nom) && !empty($email) && !empty($titre) && !empty($message)) {
        // Vérifier que l'email est valide
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Préparer le mail
            $to = "contact@viteetgourmand.fr"; // Remplace par ton email réel
            $subject = "Formulaire de contact : " . $titre;
            $body = "Nom : $nom\n";
            $body .= "Email : $email\n\n";
            $body .= "Message :\n$message\n";
            $headers = "From: contact@viteetgourmand.fr\r\n";
            $headers .= "Reply-To: contact@viteetgourmand.fr\r\n";

            // Envoyer le mail
            // if (mail($to, $subject, $body, $headers)) {
            $success = "Votre message a bien été envoyé ! (Note:utilisation de wamp donc pas de serveur de mail)";
            // } else {
            //    $error = "Une erreur est survenue. Veuillez réessayer plus tard.";
            //  }

        } else {
            $error = "Veuillez entrer un email valide.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Vite et Gourmand | Nous sommes à votre écoute</title>
    <meta name="description"
        content="Besoin d’un devis ou d’un renseignement ? Contactez Vite et Gourmand, traiteur à Bordeaux, à votre écoute pour tous vos événements gourmands.">
    <meta name="author" content="Alexandra Riviere">

    <!-- CSS -->
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/nous-contacter.css">
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

    <main id="main-contact">
        <h1 class="titre-centrer">Nous contacter</h1>

        <img src="../assets/photo-couple-page-contacts.jpg"
            alt="Photo d'un couple souriant en train de cuisiner ensemble" id="photo-couple">

        <p class="phrase-italique">Vous souhaitez nous contacter ? Remplissez le formulaire ci-dessous et nous vous
            répondrons rapidement.</p><br>


        <!-- Messages d'erreur / succès -->
        <?php if ($success): ?>
            <p class="success-msg"><?= $success ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="error-msg"><?= $error ?></p>
        <?php endif; ?>

        <form class="form-contact" action="" method="post">
            <fieldset>
                <legend>Vos coordonnées et votre message</legend>
                <div>
                    <label for="nom">Nom complet :</label>
                    <input class="input-contact" id="nom" type="text" name="nom" placeholder="EX : Dupont Bernard"
                        value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name'] . " " . $_SESSION['user_nom']) : ''; ?>"
                        required>
                </div>
                <div>
                    <label for="email">Email :</label>
                    <input class="input-contact" id="email" type="email" name="email" placeholder="exemple@email.com"
                        value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>"
                        required>
                </div>
                <div>
                    <label for="titre">Titre :</label>
                    <input class="input-contact" id="titre" type="text" name="titre"
                        placeholder="Sujet de votre demande" required>
                </div>
                <div>
                    <label for="message">Message :</label>
                    <textarea id="message" name="message" rows="15" placeholder="Votre message ici" required></textarea>
                </div>
            </fieldset><br>

            <button class="btn-contact" type="submit">Envoyer</button>
        </form>

        <p class="form-contact-info phrase-italique">Après envoi, votre demande sera transmise par mail à notre
            entreprise, et nous vous
            répondrons dans les plus brefs délais.</p>
    </main>

    <?php include('../footer.php'); ?>
</body>

</html>