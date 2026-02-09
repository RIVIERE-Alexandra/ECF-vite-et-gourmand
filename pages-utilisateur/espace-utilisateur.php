<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === false) {
    header('Location: ../login.php');
    exit();
}
require_once '../bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modif'])) {
    $stmt = $pdo->prepare("
        UPDATE client 
        SET Nom = ?, Prénom = ?, Mail = ?, Téléphone = ?, RUE = ?, CP = ?, Ville = ?, Pays = ?
        WHERE id_client = ?
    ");
    $stmt->execute([
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['email'],
        $_POST['telephone'],
        $_POST['rue'],
        $_POST['cp'],
        $_POST['ville'],
        $_POST['pays'],
        $_SESSION['user_id']
    ]);

    header("Location: espace-utilisateur.php");
    exit();
}
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['suppression'])) {
    $id_commandes = (int) $_POST['id_commandes'];

        //Supprimer les lignes de commande associées
        $stmt = $pdo->prepare("DELETE FROM ligne_commandes WHERE id_commandes = ?");
        $stmt->execute([$id_commandes]);

        //Supprimer la commande
        $stmt = $pdo->prepare("DELETE FROM commandes WHERE id_commandes = ?");
        $stmt->execute([$id_commandes]);
    

    header("Location: espace-utilisateur.php");
    exit();
}


$stmt = $pdo->prepare("
    SELECT *
    FROM client
    WHERE id_client = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>

<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace personnel - Vite et Gourmand</title>
    <meta name="description"
        content="Vite et Gourmand, traiteur à Bordeaux depuis 25 ans. votre espace personnel pour gérer vos commandes et informations.">
    <meta name="author" content="Alexandra Riviere">

    <!-- CSS -->
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/utilisateur.css">
        <link rel="stylesheet" href="../CSS/responsive.css">


    <!-- script JS -->
     <script src="../JavaScript/responsive.js" defer></script>
    <script src="../JavaScript/espace-utilisateur.js" defer></script>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php include('../header.php'); ?>

    <main>

        <h1 class="titre-encadrer espace-perso">Mon espace personnel</h1>

        <h2 class="titre-centrer">Bienvenue <?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></h2><br>

        <p class="titre-centrer"><i>Gérez vos informations personnelles et consultez vos commandes ici</i></p><br>

        <section class="user-card">

            <!-- Mode de lecture -->
            <div class="infos-perso-visibles">

                <form class="formulaire-info-utilisateur" action="" method="post">
                    <h3>Vos informations personnelles :</h3>
                    <p><strong>Nom :</strong> <?= htmlspecialchars($user['Nom']) ?></p>
                    <p><strong>Prénom :</strong> <?= htmlspecialchars($user['Prénom']) ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($user['Mail']) ?></p>
                    <p><strong>Téléphone :</strong> <?= htmlspecialchars($user['Téléphone']) ?></p>
                    <p><strong>Adresse:</strong> <?= htmlspecialchars($user['RUE']) ?>, <?= htmlspecialchars($user['CP']) ?> <?= htmlspecialchars($user['Ville']) ?>, <?= htmlspecialchars($user['Pays'] ?? 'France') ?></p>


                    <button class="btn-modif-infos-user" type="button">Modifier mes informations</button>
                </form>

            </div>

            <!-- Mode édition -->
            <div class="carte-mode-edition">

                <form class="formulaire-info-personnelle" action="" method="post">
                    <h3>Modifier vos informations personnelles :</h3>
                    <fieldset>
                        <legend>Vos informations personnelles :</legend>

                        <div>
                            <label for="nom">Nom :</label>
                            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['Nom']) ?>" required>
                        </div>

                        <div>
                            <label for="prenom">Prénom :</label>
                            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['Prénom']) ?>" required>
                        </div>

                        <div>
                            <label for="email">Email :</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['Mail']) ?>" required>
                        </div>

                        <div>
                            <label for="telephone">Téléphone :</label>
                            <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($user['Téléphone']) ?>" maxlength="10" required>
                        </div>

                        <div>
                            <label for="rue">Rue :</label>
                            <input id="rue" type="text" name="rue" value="<?= htmlspecialchars($user['RUE']) ?>" required>
                        </div>

                        <div>
                            <label for="cp">Code Postal :</label>
                            <input id="cp" type="text" name="cp" value="<?= htmlspecialchars($user['CP']) ?>" maxlength="5"
                                required>
                        </div>

                        <div>
                            <label for="ville">Ville :</label>
                            <input id="ville" type="text" name="ville" value="<?= htmlspecialchars($user['Ville']) ?>" required>
                        </div>

                        <div>
                            <label for="pays">Pays :</label>
                            <input id="pays" type="text" name="pays" value="<?= htmlspecialchars($user['Pays'] ?? 'France') ?>"
                                required>
                        </div>

                        <button class="btn-enregistrer-modifications" name="modif" type="submit">Enregistrer les
                            modifications</button>
                    </fieldset>
                </form>
            </div>

        </section>

        <section class="reset-mdp">
            <h2>Cliquez ci-dessous pour réinitialiser votre mot de passe :</h2>
            <a href="reset-mdp.php">Réinitialiser mon mot de passe</a>
        </section>

        <section class="centrer-contenu-espace-utilisateur">

            <h2>Mes commandes :</h2>

            <section class="tableau-recap-cmd-utilisateur">
                <!-- Tableau récapitulatif des commandes -->
                <table class="fond-tableaux">
                    <thead class="entete-tableaux">
                        <tr>
                            <th>Numéro de commande</th>
                            <th>Date</th>
                            <th>Récapitulatif</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Suppression</th>
                        </tr>
                    </thead>
                    <tbody>


                        <?php
                        if (isset($_SESSION['user_id'])) {
                            $id_client = $_SESSION['user_id'];
                            $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id_client = ? AND statut != 'panier'");
                            $stmt->execute([$id_client]);
                            $commandes = $stmt->fetchall(PDO::FETCH_ASSOC);
                            foreach ($commandes as $commande) {
                                $id_commande = $commande['id_commandes'];
                                $stmt = $pdo->prepare("
                                                        SELECT 
                                                            lc.id_ligne_cmd,
                                                            lc.id_menus,
                                                            lc.quantité,
                                                            lc.pret_de_materiel,
                                                            lc.Entrée,
                                                            lc.Plat,
                                                            lc.Dessert,
                                                            m.PrixParPersonne,
                                                            m.Plat AS nomMenu
                                                        FROM ligne_commandes lc
                                                        JOIN menus m ON lc.id_menus = m.id_menus
                                                        WHERE lc.id_commandes = ?
                                                    ");

                                $stmt->execute([$id_commande]);
                                $lignes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($lignes as $ligne) {

                                    //numéro de la commande
                                    echo "<tr><td>" . $commande['id_commandes'] . "</td>";

                                    //date commande
                                    echo "<td>" . date('d/m/Y', strtotime($commande['Date_de_commande'])) . "</td>";


                                    echo "<td><strong>" . htmlspecialchars($ligne['nomMenu']) . "</strong><br>";

                                    // Choix Entrée, Plat, Dessert
                                    $choix_ids = [];
                                    if (!empty($ligne['Entrée']))
                                        $choix_ids['Entrée'] = $ligne['Entrée'];
                                    if (!empty($ligne['Plat']))
                                        $choix_ids['Plat'] = $ligne['Plat'];
                                    if (!empty($ligne['Dessert']))
                                        $choix_ids['Dessert'] = $ligne['Dessert'];

                                    foreach ($choix_ids as $type => $id_compo) {
                                        $stmt2 = $pdo->prepare("SELECT libellé FROM composition_menus WHERE id_compo_menus = ?");
                                        $stmt2->execute([$id_compo]);
                                        $libelle = $stmt2->fetchColumn();

                                        echo htmlspecialchars($type . " : " . $libelle) . "<br>";
                                    }
                                }

                                echo "</td>";

                                //prix de la commande
                                echo "<td>" . $commande['Prix_total'] . "€</td>";

                                //statut commande
                                $statuts = [
                                    'accepte' => 'Acceptée',
                                    'en_attente' => 'En Attente',
                                    'en_cours' => 'En Cours',
                                    'en_livraison' => 'En Livraison',
                                    'refuser' => 'Refusée',
                                    'terminee' => 'Terminée'
                                ];

                                echo "<td>" . ($statuts[$commande['statut']] ?? $commande['statut']) . "</td>";

                                //-- Bouton de suppression de la commande tant que la commande n'est pas validée sur le tableau de bord admin
                                echo "<td>";
                                if ($commande['statut']=== 'en_attente') {
                                    echo '<form class="bordures-formulaire-invisible" method="post" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer cette commande ?\');">';
                                        echo '<input type="hidden" name="id_commandes" value="' . $commande['id_commandes'] . '">';
                                        echo '<button type="submit" class="btn-supprimer-commande" name="suppression">Supprimer</button>';
                                    echo '</form>';
                                } else {
                                    echo "Suppression non disponible";
                                }
                                echo "</td></tr>";

                            }

                        }
                        ?>
                    </tbody>
                </table>
            </section>

        </section>
    </main>

    <?php include('../footer.php'); ?>

</body>

</html>