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
    <title>Mentions légales - Vite et Gourmand | Site officiel</title>
    <meta name="description"
        content="Consultez les mentions légales du site Vite et Gourmand, traiteur à Bordeaux. Informations sur l’éditeur, l’hébergement et les droits d’utilisation.">
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
        <h1 class="titre-centrer">Mentions Légales - Vite et Gourmand</h1>

        <h2>Éditeur du site</h2>
        <ul class="taille-police-liste-liens-légaux">
            <li>Le site “Vite et Gourmand” est édité par :</li>
            <li>Nom de l’entreprise : Vite et Gourmand SARL</li>
            <li>Adresse : 12 rue des Gourmets, 33000 Bordeaux, France</li>
            <li>Téléphone : 06 12 34 56 78</li>
            <li>Email : contact@viteetgourmand.fr</li>
            <li>Directeur de la publication : [Nom du gérant]</li>
        </ul>

        <h2>Hébergement</h2>
        <ul class="taille-police-liste-liens-légaux">
            <li>Le site est hébergé par :</li>
            <li>Nom de l’hébergeur : [Nom de l’hébergeur]</li>
            <li>Adresse : [Adresse de l’hébergeur]</li>
            <li>Téléphone : [Téléphone de l’hébergeur]</li>
            <li>Site web : [URL de l’hébergeur]</li>
        </ul>
        </p>

        <h2>Propriété intellectuelle</h2>
        <p>
            L’ensemble du contenu présent sur ce site, incluant textes, images, logos, vidéos, icônes et graphismes, est
            la propriété exclusive de Vite et Gourmand, sauf mention contraire.
            Toute reproduction, représentation, modification, publication, adaptation ou exploitation, en tout ou en
            partie, est strictement interdite sans autorisation préalable écrite de Vite et Gourmand.
        </p>

        <h2>Responsabilité</h2>
        <p>
            Vite et Gourmand met tout en œuvre pour assurer l’exactitude et la mise à jour des informations présentes
            sur ce site. Cependant, l’éditeur ne peut être tenu responsable des erreurs, omissions ou d’éventuelles
            interruptions du site.
            L’utilisateur est responsable de l’utilisation qu’il fait des informations présentes sur le site.
        </p>
        <h2>Protection des données personnelles</h2>
        <p>
            Les informations recueillies via les formulaires du site (inscription, contact, commande) sont utilisées
            uniquement pour le traitement de vos demandes et commandes.
            Conformément à la réglementation en vigueur (RGPD), vous disposez d’un droit d’accès, de modification et de
            suppression de vos données personnelles.
            Pour exercer ce droit, contactez-nous à contact@viteetgourmand.fr.
        </p>
        <h2>Cookies</h2>
        <p>
            Le site peut utiliser des cookies pour améliorer l’expérience utilisateur et faciliter la navigation.
            Vous pouvez configurer votre navigateur pour refuser les cookies ou être averti de leur utilisation.
        </p>

    </main>
<?php include('../footer.php'); ?>

</body>



</html>