<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../bd.php';

$reqMenus = $pdo->query("SELECT * FROM menus WHERE Disponibilité = 1 AND CacheAdmin = 0");
$menus = $reqMenus->fetchAll(PDO::FETCH_ASSOC);

// ===============================
// TRAITEMENT COMMANDE (combiner en 1 seule commande par client)
// ===============================
$commande_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commander_menu']) && $_SESSION['user_admin'] === false) {

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    $id_client = $_SESSION['user_id'];
    $id_menus = intval($_POST['id_menus']);
    $quantite = intval($_POST['quantite']);
    $pret_materiel = isset($_POST['pret_materiel']) ? 1 : 0;
    $selected_entree = $_POST['selected_entree'] ?? null;
    $selected_plat = $_POST['selected_plat'] ?? null;
    $selected_dessert = $_POST['selected_dessert'] ?? null;


    // Vérifier le stock
    $stmt = $pdo->prepare("SELECT QuantitéRestante, PrixParPersonne FROM menus WHERE id_menus = ?");
    $stmt->execute([$id_menus]);
    $menuStock = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$menuStock) {
        $commande_message = "Menu introuvable.";
    } elseif ($menuStock['QuantitéRestante'] < $quantite) {
        $commande_message = "Stock insuffisant pour ce menu.";
    } else {
        $prix_total = $menuStock['PrixParPersonne'] * $quantite;

        try {
            $pdo->beginTransaction();

            // Vérifier si le client a déjà une commande en attente
            $stmt = $pdo->prepare("SELECT id_commandes, Prix_total FROM commandes WHERE id_client = ? AND statut = 'panier'");
            $stmt->execute([$id_client]);
            $commandeExistante = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($commandeExistante) {
                $id_commande = $commandeExistante['id_commandes'];

                // Vérifier si le menu existe déjà avec la même composition
                $stmt = $pdo->prepare("
        SELECT id_ligne_cmd, quantité 
        FROM ligne_commandes 
        WHERE id_commandes = ? 
          AND id_menus = ? 
          AND Entrée = ? 
          AND Plat = ? 
          AND Dessert = ?
    ");
                $stmt->execute([$id_commande, $id_menus, $selected_entree, $selected_plat, $selected_dessert]);
                $ligneExistante = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($ligneExistante) {
                    // Si la ligne existe, on ajoute la quantité
                    $nouvelle_quantite = $ligneExistante['quantité'] + $quantite;
                    $stmt = $pdo->prepare("
            UPDATE ligne_commandes 
            SET quantité = ?, pret_de_materiel = ? 
            WHERE id_ligne_cmd = ?
        ");
                    $stmt->execute([$nouvelle_quantite, $pret_materiel, $ligneExistante['id_ligne_cmd']]);
                } else {
                    // Sinon, nouvelle ligne
                    $stmt = $pdo->prepare("
            INSERT INTO ligne_commandes 
            (id_commandes, id_menus, quantité, pret_de_materiel, restitution_de_materiel, Entrée, Plat, Dessert)
            VALUES (?, ?, ?, ?, 0, ?, ?, ?)
        ");
                    $stmt->execute([$id_commande, $id_menus, $quantite, $pret_materiel, $selected_entree, $selected_plat, $selected_dessert]);
                }

                // Mettre à jour le prix total
                $nouveau_prix_total = $commandeExistante['Prix_total'] + $prix_total;
                $stmt = $pdo->prepare("UPDATE commandes SET Prix_total = ? WHERE id_commandes = ?");
                $stmt->execute([$nouveau_prix_total, $id_commande]);

            } else {
                // Créer une nouvelle commande
                $stmt = $pdo->prepare("
        INSERT INTO commandes (Prix_total, Date_de_commande, statut, id_client)
        VALUES (?, NOW(), 'panier', ?)
    ");
                $stmt->execute([$prix_total, $id_client]);

                $id_commande = $pdo->lastInsertId();

                // Ajouter la ligne de commande
                $stmt = $pdo->prepare("
        INSERT INTO ligne_commandes 
        (id_commandes, id_menus, quantité, pret_de_materiel, restitution_de_materiel, Entrée, Plat, Dessert)
        VALUES (?, ?, ?, ?, 0, ?, ?, ?)
    ");
                $stmt->execute([$id_commande, $id_menus, $quantite, $pret_materiel, $selected_entree, $selected_plat, $selected_dessert]);
            }

            // Mettre à jour le nombre total de menus
            $stmt = $pdo->prepare("SELECT SUM(quantité) as total_menus FROM ligne_commandes lc JOIN commandes c ON lc.id_commandes = c.id_commandes WHERE c.id_client = ? AND c.statut = 'panier'");
            $stmt->execute([$id_client]);
            $total_menus = $stmt->fetch(PDO::FETCH_ASSOC)['total_menus'];
            $_SESSION['nb_menus'] = $total_menus ?: 0;

            $pdo->commit();
            $commande_message = "Votre commande a été ajoutée avec succès !";

        } catch (Exception $e) {
            $pdo->rollBack();
            $commande_message = "Erreur lors de la commande : " . $e->getMessage();
        }
    }
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos plats - Vite et Gourmand | Saveurs locales et gourmandes</title>
    <meta name="description"
        content="Découvrez nos plats faits maison chez Vite et Gourmand : recettes locales, saveurs de saison et formules gourmandes à emporter ou à livrer.">
    <meta name="author" content="Alexandra Riviere">

    <!-- CSS -->
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/menus.css">
    <link rel="stylesheet" href="../CSS/responsive.css">

    <!-- script JS -->
    <script src="../JavaScript/responsive.js" defer></script>
    <script src="../JavaScript/menu-script.js" defer></script>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">

</head>

<body>

    <?php include('../header.php'); ?>



    <main class="main-menus">

        <?php if (!empty($commande_message)): ?>
            <p class="delai-commande" style="color:green;"><?= htmlspecialchars($commande_message) ?></p>
        <?php endif; ?>
        <section>
            <h1 class="titre-encadrer titre--nos-formules">Nos formules</h1>
            <p class="accroche-menu">Découvrez nos formules gourmandes, préparées avec des produits frais et locaux.</p>
        </section>

        <!-- section filtres -->
        <div class="filtres-global">
            <button class="btn-afficher-filtres">Filtres ▼</button>
            <section class="filtres">

                <!-- Tri par prix (checkbox) -->
                <div class="filtre-tri-prix liste-filtres">
                    <button class="btn-filtres">Trier par prix</button>
                    <fieldset>
                        <legend>Choisir l’ordre du prix</legend>
                        <div class="option-selection">
                            <input type="checkbox" name="triPrix" id="tri-prix-asc" value="asc">
                            <label for="tri-prix-asc">Prix croissant</label>
                        </div>
                        <div class="option-selection">
                            <input type="checkbox" name="triPrix" id="tri-prix-desc" value="desc">
                            <label for="tri-prix-desc">Prix décroissant</label>
                        </div>
                    </fieldset>
                </div>

                <!-- Régime -->
                <div class="filtre-regime liste-filtres">
                    <button class="btn-filtres">Régime</button>
                    <fieldset>
                        <legend>Choisir un régime alimentaire</legend>
                        <div class="option-selection">
                            <input type="checkbox" name="regime" id="regime-classique" value="1">
                            <label for="regime-classique">Classique</label>
                        </div>
                        <div class="option-selection">
                            <input type="checkbox" name="regime" id="regime-vegetarien" value="2">
                            <label for="regime-vegetarien">Végétarien</label>
                        </div>
                        <div class="option-selection">
                            <input type="checkbox" name="regime" id="regime-vegan" value="3">
                            <label for="regime-vegan">Vegan</label>
                        </div>
                    </fieldset>
                </div>

                <!-- Thème -->
                <div class="filtre-theme liste-filtres">
                    <button class="btn-filtres">Thème</button>
                    <fieldset>
                        <legend>Choisir le thème du menu</legend>
                        <div class="option-selection">
                            <input type="checkbox" name="theme" id="theme-traditionnel" value="traditionnel">
                            <label for="theme-traditionnel">Menu traditionnel</label>
                        </div>
                        <div class="option-selection">
                            <input type="checkbox" name="theme" id="theme-saisonnier" value="saisonnier">
                            <label for="theme-saisonnier">Menu saisonnier</label>
                        </div>
                        <div class="option-selection">
                            <input type="checkbox" name="theme" id="theme-evenement" value="événement">
                            <label for="theme-evenement">Menu événement</label>
                        </div>
                    </fieldset>
                </div>

                <!-- Prix -->
                <div class="filtre-fouchette-prix liste-filtres">
                    <button class="btn-filtres">Fourchette de prix</button>
                    <fieldset>
                        <legend>Choisir une fourchette de prix</legend>
                        <input type="range" name="fourchette-prix" id="fourchette-prix" min="25" max="40" step="1"
                            value="40">
                        <label for="fourchette-prix"><span id="prix-valeur">40</span> €</label>
                    </fieldset>
                </div>

                <!-- Nombre minimal de personnes -->
                <div class="nb-personne-mini liste-filtres">
                    <button class="btn-filtres">Nombre minimal de personnes</button>
                    <fieldset>
                        <legend>Choisir le nombre minimal de personnes</legend>
                        <div class="option-selection">
                            <input type="number" name="nbPersonnes" id="nb-personnes" min="1" max="20" value="1" step=1>

                            <label for="nb-personnes">Personnes</label>
                        </div>
                    </fieldset>
                </div>

            </section>
        </div>

        <p class="delai-commande">
            Pour toute commande, il est impératif de commander vos menus au moins 3 jours à l’avance !
        </p>

        <!-- section contenant les menus -->
        <section id="types-menus" class="types-menus">
            <?php foreach ($menus as $menu): ?>
                <section class="menus-complet-type">
                    <div class="carte-visible">
                        <div class="img-carte">
                            <img src="data:image/jpeg;base64,<?= base64_encode($menu['Photo']) ?>"
                                alt="Menu <?= htmlspecialchars($menu['Plat']) ?>">
                        </div>

                        <div class="texte-carte">
                            <h2><?= htmlspecialchars($menu['Plat']) ?></h2>
                            <p><?= htmlspecialchars($menu['Description']) ?></p>
                            <p><strong>A partir de </strong> 1 personne</p>
                            <p><strong>Stock :</strong> <?= $menu['QuantitéRestante'] ?></p>
                            <p><strong><?= number_format($menu['PrixParPersonne'], 2) ?> €</strong> / personne</p>
                            <button class="bouton-detail-menu">Détail menu ▼</button>
                        </div>
                    </div>

                    <div class="detail-menu-caché">
                        <div class="galerie-photos">
                            <?php
                            // Récupérer toutes les photos pour le menu
                            $sql = 'SELECT * FROM galerie_photos WHERE id_menu = :id_menu';
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute(['id_menu' => $menu['id_menus']]);
                            $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <div class="fleche-defilement-gauche">◀</div>

                            <div class="bloc-photos-menu">
                                <?php foreach ($photos as $photo): ?>
                                    <img class="img-galerie-photos"
                                        src="data:image/jpeg;base64,<?= base64_encode($photo['chemin_photo']) ?>"
                                        alt="Photo <?= htmlspecialchars($photo['id_galerie']) ?>">
                                <?php endforeach; ?>
                            </div>

                            <div class="fleche-defilement-droite">▶</div>
                        </div>


                        <div class="texte-menus">
                            <p><strong>Disponibilité :</strong>
                                <?= $menu['QuantitéRestante'] > 0 ? 'Disponible' : 'Indisponible' ?></p>
                            <?php
                            $reqCompo = $pdo->prepare("
    SELECT *
    FROM composition_menus c
    LEFT JOIN régime r ON c.id_regime = r.id_regime
    WHERE c.id_menus = ?
    ORDER BY c.type
");
                            $reqCompo->execute([$menu['id_menus']]);
                            $compos = $reqCompo->fetchAll(PDO::FETCH_ASSOC);

                            // Grouper par type
                            $groupedCompos = [];
                            foreach ($compos as $c) {
                                $key = strtolower($c['type']);
                                $groupedCompos[$key][] = $c;
                            }

                            // Affichage
                            foreach (['entrée' => 'Entrée', 'plat' => 'Plat', 'dessert' => 'Dessert'] as $type => $titre) {
                                if (!empty($groupedCompos[$type])) {
                                    echo "<h3 class='titres-choix-menu'>$titre</h3><fieldset>";
                                    foreach ($groupedCompos[$type] as $item) {
                                        echo "<div class='option-selection'>
                <input type='radio' name='{$type}_{$menu['id_menus']}' value='" . htmlspecialchars($item['id_compo_menus']) . "' required>
                <label>" . htmlspecialchars($item['libellé']) . " (" . htmlspecialchars($item['nom_regime'] ?? 'Aucun') . ")</label>
              </div>";
                                    }
                                    echo "</fieldset>";
                                }
                            }
                            ?>



                            <p class="texte-allergens">Contient éventuellement : gluten, noix, arachides, lait, œufs et
                                soja. Merci de nous prévenir en
                                cas d’allergie.</p>

                            <p class="phrase-italique">Une réduction de 10% est appliquée pour toutes commandes ayant 5
                                personnes de plus que le nombre de personnes minimum indiqué dans le menu</p>

                            <div class="ajustement-quantite-menus" data-stock="<?= $menu['QuantitéRestante'] ?>">

                                <form class="bordures-formulaire-invisible" method="post">
                                    <div class="checkbox-location">
                                        <label>
                                            <input type="checkbox" name="pret_materiel" value="1">
                                            Je souhaite louer le matériel nécessaire pour ce menu
                                        </label>
                                        <span class="info-icon" id="infoMateriel">?</span>
                                    </div>

                                    <input type="hidden" name="id_menus" value="<?= $menu['id_menus'] ?>">
                                    <input type="hidden" name="quantite" class="input-quantite" value="1">
                                    <input type="hidden" name="selected_entree" value="">
                                    <input type="hidden" name="selected_plat" value="">
                                    <input type="hidden" name="selected_dessert" value="">

                                    <div class="boutons-ajout-retrait">
                                        <button class="btn-retirer" type="button">-</button>
                                        <output class="quantite">1</output>
                                        <button class="btn-ajouter" type="button">+</button>
                                    </div>

                                    <button class="btn-commander" type="submit" name="commander_menu">Commander</button>

                                </form>
                            </div>

                        </div>
                    </div>
                </section>
            <?php endforeach; ?>
        </section>

    </main>
    <?php include('../footer.php'); ?>
</body>

</html>