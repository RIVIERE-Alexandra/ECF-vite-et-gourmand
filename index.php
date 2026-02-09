<!DOCTYPE html>
<html lang="fr">
<?php
 
require_once 'bd.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // Démarre la session si elle n'est pas déjà active
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Vite et Gourmand | Traiteur à Bordeaux, plats faits maison</title>
    <meta name="description"
        content="Vite et Gourmand, traiteur à Bordeaux depuis 25 ans. Commandez vos plats faits maison en ligne pour tous vos événements.">
    <meta name="author" content="Alexandra Riviere">

    <!-- CSS -->
    <link rel="stylesheet" href="CSS/styles.css">
    <link rel="stylesheet" href="CSS/index.css">
    <link rel="stylesheet" href="CSS/responsive.css">
    
    <!-- script JS -->
    <script src="./JavaScript/responsive.js" defer></script>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php include('header.php'); ?>

    <main class="marges-specifique">
        <section class="presentation">
            <img id="photo-accueil" src="assets/photo-page-accueil.jpg" alt="photo page d'accueil">
            <div class="texte-accueil">
                <h1>Vite & Gourmand</h1>
                <p>«Installés à Bordeaux depuis 25 ans, nous proposons des menus de saison pour tous vos événements —
                    repas de famille, fêtes et réceptions.<br><br>
                    Julie et José mettent leur savoir-faire au service de plats faits maison, faciles à commander en
                    ligne.»
                </p>
                <a href="pages-utilisateur/menus.php" class="btn-menu">Voir Menus</a>
            </div>
        </section>

        <section class="avis">

            <h2>Nos Avis</h2>


            <?php
            $sql = "
                SELECT note, description
                FROM avis_clients
                WHERE statut = 1
                ORDER BY date_publication DESC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div id="liste-avis">
                <div class="avis-container">
                    <div class="avis-slider">
                        <?php foreach ($avis as $a): ?>
                            <div class="avis-item">
                                ⭐️ <?= $a['note'] ?>/5 – <?= htmlspecialchars($a['description']) ?>
                            </div>
                        <?php endforeach; ?>

                        <!-- On duplique le contenu pour l'effet infini -->
                        <?php foreach ($avis as $a): ?>
                            <div class="avis-item">
                                ⭐️ <?= $a['note'] ?>/5 – <?= htmlspecialchars($a['description']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <p>Et vous, qu’avez-vous pensé de Vite et Gourmand ?</p>
            <a href="pages-utilisateur/avis.php" class="btn-avis">Laisser un avis</a>

        </section>
    </main>

    <?php include('footer.php'); ?>

</body>

</html>