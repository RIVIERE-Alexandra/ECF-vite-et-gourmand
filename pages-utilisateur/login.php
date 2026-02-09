<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../bd.php';

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: ../index.php');
    exit();
}

$error = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $motdepasse = $_POST['motdepasse'];


    $sql = "SELECT * FROM client WHERE Mail = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($stmt->rowCount() > 0) {

        if (password_verify($motdepasse, $user['MotDePasse'])) {

            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $user['Prénom'];
            $_SESSION['user_nom'] = $user['Nom'];
            $_SESSION['user_admin'] = false;
            $_SESSION['user_role'] = $user['Rôle'];
            $_SESSION['user_id'] = $user['id_client'];


            $id_client = $_SESSION['user_id'];
            $stmt = $pdo->prepare("SELECT SUM(quantité) AS total_menus 
                           FROM ligne_commandes 
                           INNER JOIN commandes ON commandes.id_commandes = ligne_commandes.id_commandes 
                           WHERE commandes.id_client = ? AND commandes.statut = 'panier'");
            $stmt->execute([$id_client]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $nb_menus = $result['total_menus'] ?? 0;


            $_SESSION['nb_menus'] = $nb_menus;

            header("Location: ../index.php");
            exit();
        } else {
            $error = "Identifiants incorrects. Veuillez réessayer.";
        }
    } else {
        $sqladmin = "SELECT * FROM employés WHERE Mail = :email";
        $stmtadmin = $pdo->prepare($sqladmin);
        $stmtadmin->execute(['email' => $email]);
        $useradmin = $stmtadmin->fetch(PDO::FETCH_ASSOC);

        if ($useradmin && password_verify($motdepasse, $useradmin['MotDePasse'])) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $useradmin['Prénom'];
            $_SESSION['user_nom'] = $useradmin['Prénom'];
            $_SESSION['user_admin'] = true;
            $_SESSION['user_role'] = $useradmin['Rôle'];
            $_SESSION['user_id'] = $useradmin['id_employe'];


            $id_client = $_SESSION['user_id'];
            $stmt = $pdo->prepare("SELECT SUM(quantité) AS total_menus 
                           FROM ligne_commandes 
                           INNER JOIN commandes ON commandes.id_commandes = ligne_commandes.id_commandes 
                           WHERE commandes.id_client = ? AND commandes.statut = 'panier'");
            $stmt->execute([$id_client]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $nb_menus = $result['total_menus'] ?? 0;


            $_SESSION['nb_menus'] = $nb_menus;




            header("Location: ../pages-admin/index-admin.php");
            exit();
        } else {
            $error = "Identifiants incorrects. Veuillez réessayer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Vite et Gourmand | Accédez à votre espace client</title>
    <meta name="description"
        content="Connectez-vous à votre espace client Vite et Gourmand pour accéder à vos commandes, paiements et informations personnelles.">
    <meta name="author" content="Alexandra Riviere">

    <!-- CSS -->
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/login.css">
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

    <main id="main-connexion">
        <img id="img-connexion" src="../assets/img-accueil-connection.jpg" alt="Image de connexion">
        <h1 class="titre-centrer">Se connecter</h1>

        <!-- Formulaire de connexion -->
        <form class="form-connexion" action="" method="post">
            <fieldset>
                <legend>Connexion</legend>

                <?php if ($error): ?>
                    <p class="error-message"><?php echo $error; ?></p>
                <?php endif; ?>

                <div>
                    <label for="email">Email :</label>
                    <input class="input-connexion" id="email" type="email" name="email" required>
                </div>

                <div>
                    <label for="motdepasse">Mot de passe :</label>
                    <input class="input-connexion" id="motdepasse" type="password" name="motdepasse" required>
                    <div class="mdp-oublie">
                        <a href="reset-mdp.php">Mot de passe oublié ?</a>
                    </div>
                </div>

                <input class="btn-login" type="submit" value="Connexion">
            </fieldset>
        </form>

        <div class="inscription">
            <p>Pas encore inscrit.e ?</p>
            <a class="lien-page-inscription" href="inscription.php">Inscrivez-vous !</a>
        </div>
    </main>

    <?php include('../footer.php'); ?>
</body>

</html>