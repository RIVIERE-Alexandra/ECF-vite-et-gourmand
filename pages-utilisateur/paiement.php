<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] === false) {
    header('Location: ../index.php');
    exit();
}

require_once '../bd.php';
$adresse_livraison = $_GET['adresse'] ?? null;

/* ======================
   TRAITEMENT DU PAIEMENT
   ====================== */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['btn-payer'], $_POST['id_commande'])
    && isset($_SESSION['user_id'])
) {
    
//appel a la banque et si paiement ok alors :
    $id_commande = (int) $_POST['id_commande'];
    $id_client = (int) $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        UPDATE commandes
        SET statut = 'en_attente'
        WHERE id_commandes = ?
        AND id_client = ?
        AND statut = 'panier'
    ");
    $stmt->execute([$id_commande, $id_client]);

    $_SESSION['nb_menus'] = "";

    header('Location: menus.php');
    exit;
}

/* ======================
   RÉCUPÉRATION DU PANIER
   ====================== */
$stmt = $pdo->prepare("
    SELECT *
    FROM commandes
    WHERE id_client = ?
    AND statut = 'panier'
");
$stmt->execute([$_SESSION['user_id']]);
$panier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$panier) {
    header('Location: menus.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement sécurisé - Vite et Gourmand | Commandes en ligne simplifiées</title>
    <meta name="description"
        content="Finalisez votre commande Vite et Gourmand en toute sécurité. Paiement rapide et protégé pour vos repas faits maison à Bordeaux.">
    <meta name="author" content="Alexandra Riviere">

    <!-- CSS -->
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/paiement.css">
    <link rel="stylesheet" href="../CSS/responsive.css">

    <!-- script JS -->
     <script src="../JavaScript/responsive.js" defer></script>
     <script src="../JavaScript/paiement.js" defer></script>


    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php include('../header.php'); ?>

    <main class="main-paiement">
        <h1 class="titre-encadrer titre-paiement">Paiement sécurisé</h1>
        <p class="phrase-italique">Finalisez votre commande en toute sécurité.</p>

        <div class="recap-commande-paiement">
            <h2>Récapitulatif de votre commande</h2>

            <?php if ($panier): ?>
                <p><strong>Total à payer :</strong> <?= number_format($panier['Prix_total'], 2, ',', ' ') ?> €</p>
                <p>
                    <strong>Date et heure de livraison :</strong>
                    <?= date('d/m/Y', strtotime($panier['date_de_livraison']))  ?>
                    - <?= date('H:i', strtotime($panier['heure_de_livraison'])) ?>
                </p>
                <p><strong>Adresse de livraison :</strong> <?= htmlspecialchars($adresse_livraison) ?></p>

            <?php else: ?>
                <p>Aucune commande en cours.</p>
            <?php endif; ?>
        </div>


        <form class="formulaire-paiement" action="" method="post">
            <?php if ($panier): ?>
                <input type="hidden" name="id_commande" value="<?= $panier['id_commandes'] ?>">
            <?php endif; ?>

            <fieldset>
                <legend>Paiement</legend> <!-- à cacher plus tard en CSS -->

                <div class="bloc-info-paiement">
                    <label for="titulaire">Nom du titulaire de la carte :</label>
                    <input id="titulaire" name="titulaire" type="text" required>
                </div>

                <div class="bloc-info-paiement">
                    <label for="numero-carte">Numéro de la carte :</label>
                    <input id="numero-carte" name="numero" type="text" inputmode="numeric" pattern="[0-9]{16}"
                        maxlength="16" required>
                </div>

                <section class="infos-cb">
                    <div>
                        <label for="expiration">Date d'expiration :</label>
                        <input id="expiration" name="expiration" type="month" required>
                    </div>

                    <div>
                        <label for="cvv">CVV :</label>
                        <div class="cvv-et-help-ligne">
                            <input id="cvv" name="cvv" type="text" pattern="[0-9]{3}" maxlength="3"
                                aria-describedby="cvvHelp" required>
                            <small id="cvvHelp">3 chiffres au dos de votre carte</small>
                        </div>
                    </div>
                </section>
                <button class="btn-payer" name="btn-payer" type="submit">Confirmer et payer</button>
            </fieldset>
        </form>
        </div>
    </main>

    <?php include('../footer.php'); ?>
</body>



</html>