<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_logged_in'], $_SESSION['user_admin']) && $_SESSION['user_admin'] === false) {
    header('Location: ../index.php');
    exit();
}
require_once '../bd.php';

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord admin - Vite et Gourmand</title>
    <meta name="description"
        content="Accédez à votre tableau de bord administrateur Vite et Gourmand pour gérer les menus, commandes et comptes clients en toute simplicité.">
    <meta name="author" content="Alexandra Riviere">

    <!-- CSS -->
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/dashboard-admin.css">
    <link rel="stylesheet" href="../CSS/dashboard-responsive.css">


    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">

    <!-- Librairie Plotly (responsive) -->
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

    <!-- script JS -->
    <script src="../JavaScript/responsive.js" defer></script>
    <script src="../JavaScript/admin.js" defer></script>
</head>

<body>
    <?php include('../header.php'); ?>


    <div class="barre-laterale-et-dashboard">

        <!--BARRE LATERALE-->

        <section class="barre-laterale">
            <nav aria-label="Navigation du tableau de bord">
                <a href="#cmd-recue">Commandes reçues</a>
                <a href="#menus">Gestion des Menus</a>
                <a href="#horaires">Gestion des Horaires</a>

                <?php if ($_SESSION['user_role'] === "Admin"): ?>
                    <a href="#validation-avis">Gestion des Avis</a>
                    <a href="#parametrage-employe">Gestion des Employés</a>
                    <a href="#statistiques">Statistiques et Chiffre d'Affaires</a>
                <?php endif; ?>
            </nav>
        </section>

        <main class="dashboard">
            <h1 class="titre-centrer">Tableau de bord</h1>

            <!--CONTENU PRINCIPAL-->

            <section class="fond-général-page-pro">

                <!-- Gestion Commandes-->
                <?php include('commandes.php'); ?>

                <!-- Gestion Menus-->
                <?php include('menus.php'); ?>

                <?php if ($_SESSION['user_role'] === "Admin"): ?>
                    <!-- Gestion Horaires -->
                    <?php include('horaires.php'); ?>

                    <!-- Gestion Avis Clients -->
                    <?php include('avis.php'); ?>

                    <!-- Gestion Employés -->
                    <?php include('employes.php'); ?>

                    <!-- Statistiques et Chiffre d'Affaires -->
                    <?php include('stats.php'); ?>
                <?php endif; ?>

            </section>

        </main>
    </div>

</body>

</html>