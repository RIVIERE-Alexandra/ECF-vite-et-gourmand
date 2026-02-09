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
    <title>Conditions générales - Vite et Gourmand | Commandes et services</title>
    <meta name="description"
        content="Lisez les conditions générales de vente de Vite et Gourmand : modalités de commande, paiement, livraison et politique de remboursement.">
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
        <h1 class="titre-centrer">Conditions Générales de Vente - Vite et Gourmand</h1>

        <h2>1. Objet</h2>
        <p>Les présentes conditions définissent les règles applicables à la vente des produits proposés par Vite et
            Gourmand sur son site internet. Toute commande implique l’acceptation sans réserve de ces conditions.</p>
        <h2>2. Produits</h2>
        <p>Vite et Gourmand propose des plats faits maison, livrés à domicile.</p>
        <ul class="taille-police-liste-liens-légaux">
            <li>Les descriptions des produits sont fournies à titre indicatif.</li>
            <li>Les photos sont représentatives et ne peuvent engager la responsabilité de l’entreprise.</li>
        </ul>

        <h2>3. Prix</h2>
        <ul class="taille-police-liste-liens-légaux">
            <li>Les prix sont indiqués en euros, toutes taxes comprises (TTC).</li>
            <li>Les frais de livraison sont précisés au moment de la commande et s’ajoutent au total.</li>
            <li>Vite et Gourmand se réserve le droit de modifier ses prix à tout moment, mais les produits sont facturés
                sur la base des tarifs en vigueur lors de la validation de la commande.</li>
        </ul>

        <h2>4. Commande</h2>
        <ul class="taille-police-liste-liens-légaux">
            <li>Les commandes se font uniquement via le site internet.</li>
            <li>La validation de la commande implique la création d’un compte utilisateur ou la connexion à un compte
                existant.</li>
            <li>Une confirmation par email est envoyée pour chaque commande.</li>
        </ul>

        <h2>5. Paiement</h2>
        <ul class="taille-police-liste-liens-légaux">
            <li>Le paiement s’effectue en ligne par carte bancaire ou via un service de paiement sécurisé.</li>
            <li>La commande est considérée comme validée une fois le paiement accepté.</li>
        </ul>

        <h2>6. Livraison</h2>
        <ul class="taille-police-liste-liens-légaux">
            <li>Les livraisons sont effectuées aux adresses indiquées par le client lors de la commande.</li>
            <li>Les délais de livraison sont indiqués à titre indicatif. Vite et Gourmand ne peut être tenu responsable
                des retards indépendants de sa volonté.</li>
        </ul>

        <h2>7. Retour du matériel prêté</h2>
        <p>Lorsqu’un matériel a été prêté au client, celui-ci doit le restituer dans les délais impartis. Dès que le
            statut « En attente du retour de matériel » est atteint, un e-mail est automatiquement envoyé au client pour
            le notifier. Si le matériel n’est pas restitué sous 10 jours ouvrés, le client s’expose à des frais de
            600 €, conformément aux conditions générales de vente. Pour procéder à la restitution, le client doit
            prendre contact directement avec notre société afin de convenir des modalités de retour.</p>

        <h2>8. Droit de rétractation</h2>
        <p>Conformément à la législation française, le droit de rétractation ne s’applique pas aux produits alimentaires
            périssables ou préparés sur mesure.</p>

        <h2>9. Responsabilité</h2>
        <ul class="taille-police-liste-liens-légaux">
            <li>Vite et Gourmand s’engage à préparer et livrer des produits conformes aux règles d’hygiène et de
                sécurité.</li>
            <li>La responsabilité de l’entreprise ne peut être engagée en cas de non-respect des conditions de
                conservation ou de consommation des produits par le client.</li>
        </ul>

        <h2>10. Propriété intellectuelle</h2>
        <ul class="taille-police-liste-liens-légaux">
            <li>Les textes, images, logos et contenus du site sont protégés par la législation sur la propriété
                intellectuelle.</li>
            <li>Toute reproduction ou utilisation non autorisée est interdite.</li>
        </ul>

        <h2>11. Données personnelles</h2>
        <ul class="taille-police-liste-liens-légaux">
            <li>Les informations recueillies sont utilisées uniquement pour le traitement des commandes et la relation
                client.</li>
            <li>Conformément au RGPD, le client dispose d’un droit d’accès, de modification et de suppression de ses
                données.</li>
        </ul>
        <h2>12. Litiges et droits applicables</h2>
        <ul class="taille-police-liste-liens-légaux">
            <li>Les présentes conditions sont régies par le droit français.</li>
            <li>Tout litige relatif à l’interprétation ou à l’exécution des CGV sera soumis aux tribunaux compétents de
                Bordeaux.</li>
        </ul>
    </main>


    <?php include('../footer.php'); ?>

</body>



</html>