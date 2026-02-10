<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../bd.php';
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Vérifie si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user_admin'] === false) {
    
    $nom = trim($_POST['nom'] ?? '');
    $avis = trim($_POST['avis'] ?? '');
    $note = intval($_POST['note'] ?? 0);
    $id_client = $_SESSION['user_id'];

    if ($nom && $avis && $note >= 1 && $note <= 5) {
        $sql = "INSERT INTO avis_clients (note, description, statut, id_client, date_publication) 
                VALUES (:note, :description, 0, :id_client, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':note' => $note,
            ':description' => $avis,
            ':id_client' => $id_client
        ]);

        $success_message = "Merci ! Votre avis a été pris en compte !";
    } else {
        $error_message = "Veuillez remplir tous les champs correctement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laisser un avis - Vite et Gourmand</title>
    <meta name="description"
        content="Vite et Gourmand, traiteur à Bordeaux depuis 25 ans. Laissez nous un avis sur votre expérience culinaire et partagez votre satisfaction.">
    <meta name="author" content="Alexandra Riviere">
    <!-- CSS -->
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/avis.css">
    <link rel="stylesheet" href="../CSS/responsive.css">

    <!-- script JS -->
     <script src="../JavaScript/responsive.js" defer></script>
    <script src="../JavaScript/avis.js" defer></script>
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php include('../header.php'); ?>

    <main class="marges-specifiques">
        <h1 class="titre-centre">Laissez un avis</h1>
        <form class="form-avis" action="" method="post">
            <fieldset>

                <div>
                    <label for="nom">Prénom :</label>
                    <input class="nom-avis" id="nom" type="text" name="nom" placeholder="EX : Dupont Bernard"
                        value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>">

                </div><br>

                <div> <!-- remplissage des étoiles à la notation -->
                    <label for="avis">Votre avis :</label>
                    <textarea id="avis" name="avis" rows="6" placeholder="Rédigez votre avis ici"></textarea>
                </div><br>
                <div class="notation">
                    <input class="radio" type="radio" id="star5" name="note" value="5">
                    <label for="star5" title="5 étoiles">★</label>

                    <input class="radio" type="radio" id="star4" name="note" value="4">
                    <label for="star4" title="4 étoiles">★</label>

                    <input class="radio" type="radio" id="star3" name="note" value="3">
                    <label for="star3" title="3 étoiles">★</label>

                    <input class="radio" type="radio" id="star2" name="note" value="2">
                    <label for="star2" title="2 étoiles">★</label>

                    <input class="radio" type="radio" id="star1" name="note" value="1">
                    <label for="star1" title="1 étoile">★</label>
                </div><br>
                <?php if (!empty($success_message)): ?>
                    <p style="color:green;">
                        <?= htmlspecialchars($success_message) ?>
                    </p>
                <?php elseif (!empty($error_message)): ?>
                    <p style="color:red;">
                        <?= htmlspecialchars($error_message) ?>
                    </p>
                <?php endif; ?>

                <input class="btn-avis" type="submit" value="Envoyer">
            </fieldset>
        </form>
    </main>

    <?php include('../footer.php'); ?>

</body>

</html>