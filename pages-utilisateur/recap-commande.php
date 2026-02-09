<!DOCTYPE html>
<html lang="fr">
<?php
require_once '../bd.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$nom = "";
$prenom = "";
$mail = "";
$tel = "";
$rue = "";
$cp = "";
$ville = "";
date_default_timezone_set('Europe/Paris');
$date_livraison = date("Y-m-d", strtotime("+3 days"));
$heure_livraison = date("H:i", time());


if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true && $_SESSION['user_admin'] === false) {
    $sql = "SELECT * FROM client WHERE id_client = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $nom = $user['Nom'];
    $prenom = $user['Prénom'];
    $mail = $user['Mail'];
    $tel = $user['Téléphone'];
    $rue = $user['RUE'];
    $cp = $user['CP'];
    $ville = $user['Ville'];
    $pays = $user['Pays'];

}

if (isset($_POST['valider_commande']) && isset($_SESSION['user_id'])) {
    if (
        empty($_POST['date-livraison']) ||
        empty($_POST['heure-livraison']) ||
        $_POST['heure-livraison'] === '00:00:00' ||
        $_POST['date-livraison'] === '00:00:00'
    ) {
        $erreurs = "Date ou heure de livraison manquante";
    }
else {
    $adresse_livraison = sprintf(
                "%s, %s %s",
                ($_POST['rue-livraison'] ?? $rue),
                ($_POST['cp-livraison'] ?? $cp),
                ($_POST['ville-livraison'] ?? $ville)
            );
        if (strtolower($_POST['ville-livraison']) !== "bordeaux") {

            $adresseDepart = "Pl. Pey Berland, 33000 Bordeaux France";
            $adresseArrivee = $adresse_livraison;

            // --- 1) Géocodage avec Nominatim ---
            function geocode($adresse)
            {
                $url = "https://nominatim.openstreetmap.org/search?" . http_build_query([
                    "q" => $adresse,
                    "format" => "json",
                    "limit" => 1
                ]);

                $opts = [
                    "http" => [
                        "header" => "User-Agent: ViteEtGourmand/1.0\r\n"
                    ]
                ];

                $result = file_get_contents($url, false, stream_context_create($opts));
                $data = json_decode($result, true);

                if (!$data)
                    return null;

                return [
                    "lat" => $data[0]["lat"],
                    "lon" => $data[0]["lon"]
                ];
            }

            $start = geocode($adresseDepart);
            $end = geocode($adresseArrivee);
            if (!$start || !$end) {
                die("Erreur d'adresse");
            }

            // --- 2) Distance routière avec OSRM ---
            $osrmUrl = "https://router.project-osrm.org/route/v1/driving/"
                . "{$start['lon']},{$start['lat']};{$end['lon']},{$end['lat']}"
                . "?overview=false";

            $response = file_get_contents($osrmUrl);
            $data = json_decode($response, true);

            if ($data["code"] !== "Ok") {
                die("Erreur OSRM");
            }

            // Distance en kilomètres
            $distanceKm = $data["routes"][0]["distance"] / 1000;
            $distanceKm = $distanceKm * 0.59; // (majoré de 59 centimes par kilomètre parcouru) si la livraison n’est pas dans la ville de Bordeaux. 
        } else {
            $distanceKm = 0.00;
        }
      $id_client = $_SESSION['user_id'];

        $stmt = $pdo->prepare("
        SELECT id_commandes
        FROM commandes
        WHERE id_client = ?
        AND statut = 'panier'
    ");
        $stmt->execute([$id_client]);
        $id_commande = $stmt->fetchColumn();

        if ($id_commande) {

            $stmt = $pdo->prepare("
            SELECT SUM(lc.quantité * m.PrixParPersonne)
            FROM ligne_commandes lc
            JOIN menus m ON lc.id_menus = m.id_menus
            WHERE lc.id_commandes = ?
        ");
            $stmt->execute([$id_commande]);
            $sous_total = (float) $stmt->fetchColumn();

            $prix_livraison = (strtolower($_POST['ville-livraison']) === 'bordeaux') ? 0.00 : $distanceKm + 5.00;

            $prix_total = $sous_total + $prix_livraison;

            

            $stmt = $pdo->prepare("
            UPDATE commandes
            SET
                Prix_total = ?,
                Adresse_Livraison = ?,
                date_de_livraison = ?,
                heure_de_livraison = ?,
                prix_livraison = ?
            WHERE id_commandes = ?
            AND id_client = ?
        ");

            $stmt->execute([
                $prix_total,
                $adresse_livraison,
                $_POST['date-livraison'],
                $_POST['heure-livraison'],
                $prix_livraison,
                $id_commande,
                $id_client
            ]);

            header('Location: paiement.php?adresse=' . urlencode($adresse_livraison) );
            exit;
        }
    }
}


if (isset($_POST['supprimer_id_ligne']) && isset($_SESSION['user_id'])) {
    $id_ligne_cmd = intval($_POST['supprimer_id_ligne']);
    $id_client = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        SELECT c.id_commandes
        FROM ligne_commandes lc
        JOIN commandes c ON lc.id_commandes = c.id_commandes
        WHERE lc.id_ligne_cmd = ?
        AND c.id_client = ?
        AND c.statut = 'panier'
    ");
    $stmt->execute([$id_ligne_cmd, $id_client]);
    $id_commande = $stmt->fetchColumn();

    if ($id_commande) {

        $stmt = $pdo->prepare("
            DELETE FROM ligne_commandes
            WHERE id_ligne_cmd = ?
        ");
        $stmt->execute([$id_ligne_cmd]);

        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM ligne_commandes 
            WHERE id_commandes = ?
        ");
        $stmt->execute([$id_commande]);
        $nb_lignes = $stmt->fetchColumn();

        if ($nb_lignes == 0) {

            $stmt = $pdo->prepare("
                DELETE FROM commandes
                WHERE id_commandes = ?
            ");
            $stmt->execute([$id_commande]);

            $_SESSION['nb_menus'] = 0;

        } else {

            $stmt = $pdo->prepare("
                SELECT SUM(quantité)
                FROM ligne_commandes
                WHERE id_commandes = ?
            ");
            $stmt->execute([$id_commande]);
            $_SESSION['nb_menus'] = (int) $stmt->fetchColumn();
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}




?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif de commande - Vite et Gourmand | Vérifiez vos plats avant paiement</title>
    <meta name="description"
        content="Consultez le récapitulatif de votre commande Vite et Gourmand avant validation. Vérifiez vos plats, quantités et total avant le paiement sécurisé.">
    <meta name="author" content="Alexandra Riviere">

    <!-- CSS -->
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/recap-cmd.css">
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

    <main class="centrer-page-recap-cmd">
        <?php if (!empty($erreurs)): ?>
            <p class="delai-commande" style="color:green;"><?= htmlspecialchars($erreurs) ?></p>
        <?php endif; ?>
        <h1 class="titre-centrer titre-principal-cmd">Récapitulatif de votre commande</h1>

        <!-- Informations personnelles -->
        <section class="infos-personnelles">
            <h2 class="titre-encadrer titre1-cmd">Informations personnelles</h2>
            <form class="formulaire-info-prestation" action="" method="post">
                <fieldset>
                    <legend>Informations du client pour la prestation</legend>

                    <div class="form-block">
                        <label for="nom">Nom :</label>
                        <input id="nom" type="text" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
                    </div>

                    <div class="form-block">
                        <label for="prenom">Prénom :</label>
                        <input id="prenom" type="text" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>
                    </div>

                    <div class="form-block">
                        <label for="email">Email :</label>
                        <input id="email" type="email" name="email" placeholder="exemple@email.com"
                            value="<?= htmlspecialchars($mail) ?>" required>
                    </div>

                    <div class="form-block">
                        <label for="telephone">Téléphone :</label>
                        <input id="telephone" type="tel" name="telephone" placeholder="0601020304"
                            value="<?= htmlspecialchars($tel) ?> " required>
                    </div>

                    <div class="form-block">
                        <label for="rue">Rue :</label>
                        <input id="rue" type="text" name="rue" value="<?= htmlspecialchars($rue) ?>" required>
                    </div>

                    <div class="form-block">
                        <label for="code-postal">Code Postal :</label>
                        <input id="code-postal" type="text" name="code-postal" placeholder="33000"
                            value="<?= htmlspecialchars($cp) ?>" required>
                    </div>

                    <div class="form-block">
                        <label for="ville">Ville :</label>
                        <input id="ville" type="text" name="ville" placeholder="Bordeaux"
                            value="<?= htmlspecialchars($ville) ?>" required>
                    </div>

                    <div class="form-block">
                        <label for="date-livraison">Date de livraison :</label>
                        <input id="date-livraison" type="date" name="date-livraison"
                            value="<?= htmlspecialchars($date_livraison) ?>" required>
                    </div>

                    <div class="form-block">
                        <label for="heure-livraison">Heure de livraison :</label>
                        <input id="heure-livraison" type="time" name="heure-livraison"
                            value="<?= htmlspecialchars($heure_livraison) ?>" required>
                    </div>

                    <span class="info-date-livraison">
                        Nous vous rappelons que le délai minimum pour commander vos menus
                        est de 3 jours après le jour de votre commande !
                    </span>
                </fieldset>

                <p class="taille-police-facturation">
                    Facturation de 5 euros (majoré de 59 centimes par kilomètre parcouru)
                    si la livraison n’est pas dans la ville de Bordeaux.
                </p>
            </form>

        </section>

        <!-- Récapitulatif commande -->
        <section class="recap-commande">
            <h2 class="titre-encadrer titre2-cmd">Détails de votre commande</h2>
            <table class="fond-tableaux">
                <thead class="entete-tableaux">
                    <tr>
                        <th scope="col">Menu / Détails</th>
                        <th scope="col">Quantité</th>
                        <th scope="col">Supprimer</th>
                        <th scope="col">Prix</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_SESSION['user_id']) && $_SESSION['user_admin'] === false) {
                        $id_client = $_SESSION['user_id'];

                        // Récupérer la commande en attente
                        $stmt = $pdo->prepare("SELECT id_commandes, Prix_total FROM commandes WHERE id_client = ? AND statut = 'panier'");
                        $stmt->execute([$id_client]);
                        $commande = $stmt->fetch(PDO::FETCH_ASSOC);

                        $sous_total = 0;

                        if ($commande) {
                            $id_commande = $commande['id_commandes'];

                            // Récupérer les lignes de commande avec les menus
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
                                $total_ligne = $ligne['PrixParPersonne'] * $ligne['quantité'];
                                $sous_total += $total_ligne;

                                echo "<tr>";
                                echo "<td><strong>" . htmlspecialchars($ligne['nomMenu']) . "</strong><br>";

                                // Tableau Entrée, Plat, Dessert
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

                                echo "</td>";
                                echo "<td><div class='ajustement-quantite'><span class='quantite'>" . $ligne['quantité'] . "</span></div></td>";
                                echo "<td>
    <form class='bordures-formulaire-invisible' method='post' onsubmit=\"return confirm('Supprimer ce menu ?');\">
        <input type='hidden' name='supprimer_id_ligne' value='" . $ligne['id_ligne_cmd'] . "'>
        <button class='btn-supprimer' type='submit'>Supprimer</button>
    </form>
</td>";



                                echo "<td>" . number_format($total_ligne, 2, ',', ' ') . " €</td>";
                                echo "</tr>";
                            }

                        } else {
                            echo "<tr><td colspan='4'>Vous n'avez pas encore de commande en attente.</td></tr>";
                        }

                        // Calcul frais livraison
                        $ville_client = $ville ?? '';
                        $frais_livraison = (strtolower(trim($ville_client)) === 'bordeaux') ? 0.00 : 5.00;


                        echo "<tr><td class='ligne-total-cmd' colspan='3'>Sous-total :</td><td id='sous-total'>" . number_format($sous_total, 2, ',', ' ') . "</td></tr>";
                        echo "<tr><td class='ligne-total-cmd' colspan='3'>Frais de livraison :</td><td id='frais-livraison'>" . number_format($frais_livraison, 2, ',', ' ') . "</td></tr>";
                        echo "<tr><td class='ligne-total-cmd' colspan='3'>Total :</td><td id='total'>" . number_format($sous_total + $frais_livraison, 2, ',', ' ') . "</td></tr>";

                    } else {
                        if (isset($_SESSION['user_id']) && $_SESSION['user_admin'] === true) {
                            echo "<tr><td colspan='4'>Vous ne pouvez pas commander avec un compte administrateur.</td></tr>";
                        } else {
                            echo "<tr><td colspan='4'>Veuillez vous connecter pour voir vos commandes.</td></tr>";
                        }

                    }
                    ?>
                </tbody>
            </table>
        </section>
        <form class="bordures-formulaire-invisible" name="form-validation" action="" method="post">
            <input type="hidden" name="valider_commande" value="1">
            <input type="hidden" name="date-livraison" id="hidden-date"
                value="<?= htmlspecialchars($date_livraison) ?>">
            <input type="hidden" name="heure-livraison" id="hidden-heure"
                value="<?= htmlspecialchars($heure_livraison) ?>">
                <input type="hidden" name="ville-livraison" id="hidden-ville"
                value="<?= htmlspecialchars($ville_client) ?>">
                <input type="hidden" name="rue-livraison" id="hidden-rue"
                value="<?= htmlspecialchars($rue) ?>">
                <input type="hidden" name="cp-livraison" id="hidden-cp"
                value="<?= htmlspecialchars($cp) ?>">
            <button class="btn-valider-cmd" type="submit">Valider la commande</button>
            <!-- redirige vers page paiement.html -->
        </form>
    </main>

    <?php include('../footer.php'); ?>
    <script src="../JavaScript/recap-cmd.js" defer></script>




</body>

</html>