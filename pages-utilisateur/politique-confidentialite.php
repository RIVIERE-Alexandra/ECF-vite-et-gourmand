<!DOCTYPE html>
<html lang="fr">
<?php
 if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de confidentialité - Vite et Gourmand | Protection de vos données</title>
    <meta name="description"
        content="Découvrez comment Vite et Gourmand protège vos données personnelles et respecte votre vie privée lors de vos commandes en ligne.">
    <meta name="author" content="Alexandra Riviere">

    <!-- CSS -->
    <link rel="stylesheet" href="../CSS/styles.css">
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

    <main class="marges-specifiques">
        <h1 class="titre-centrer">Politique de confidentialité – Vite et Gourmand</h1>

        <h2>1. Collecte des données personnelles</h2>
        <p>Vite et Gourmand collecte des informations personnelles lorsque vous :</p>
        <ul class="taille-police-liste-liens-légaux">
            <li>Créez un compte sur le site</li>

            <li>Passez une commande</li>

            <li>Utilisez les formulaires de contact</li>

            <li>Vous inscrivez à la newsletter (si applicable)</li>
        </ul>
        <p>Les données collectées peuvent inclure : nom, prénom, email, adresse, téléphone et informations liées à la
            commande.</p>

        <h2>2. Utilisation des données</h2>
        <p>Les données personnelles sont utilisées uniquement pour :</p>
        <ul class="taille-police-liste-liens-légaux">
            <li>Traiter vos commandes</li>
            <li>Communiquer avec vous concernant votre commande ou vos demandes</li>
            <li>Améliorer nos services et notre site</li>
        </ul>

        <h2>3. Partage des données</h2>
        <p>Vite et Gourmand ne vend pas vos données personnelles et ne les partage qu’avec des prestataires nécessaires
            au traitement de la commande (ex. : transporteurs, services de paiement).</p>

        <h2>4. Sécurité des données</h2>
        <p>Nous mettons en œuvre des mesures de sécurité appropriées pour protéger vos données contre tout accès non
            autorisé, modification, divulgation ou destruction.</p>

        <h2>5. Conservation des données</h2>
        <p>Les données sont conservées uniquement pendant la durée nécessaire au traitement de vos commandes et à la
            gestion de la relation client, ou conformément aux obligations légales.</p>

        <h2>6. Vos droits</h2>
        <p>Conformément à la réglementation en vigueur, vous disposez des droits suivants concernant vos données
            personnelles :</p>
        <ul class="taille-police-liste-liens-légaux">
            <li>D’un droit d’accès, de rectification et de suppression de vos données</li>
            <li>D’un droit de limitation ou d’opposition au traitement de vos données</li>
            <li>D’un droit à la portabilité de vos données</li>
        </ul>
        <p>Pour exercer ces droits, contactez-nous à contact@viteetgourmand.fr.</p>

        <h2>7. Cookies et traceurs</h2>
        <p>Le site utilise des cookies pour :</p>
        <ul class="taille-police-liste-liens-légaux">
            <li>Améliorer l’expérience utilisateur</li>
            <li>Analyser le trafic du site</li>
            <li>Vous pouvez configurer votre navigateur pour refuser les cookies ou être averti de leur utilisation.
            </li>
        </ul>
        <h2>8. Modifications de la politique de confidentialité</h2>
        <p>Vite et Gourmand peut modifier cette politique à tout moment. Les utilisateurs sont invités à consulter
            régulièrement cette page pour prendre connaissance des éventuelles mises à jour.</p>

        <h2>9. Contact</h2>
        <p>Pour toute question concernant vos données personnelles ou cette politique de confidentialité, contactez-nous
            à : contact@viteetgourmand.fr</p>
    </main>

<?php include('../footer.php'); ?>

</body>

</html>