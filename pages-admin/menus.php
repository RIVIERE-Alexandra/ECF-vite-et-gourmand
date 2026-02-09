<?php
if (basename($_SERVER['PHP_SELF']) === 'menus.php') {
    header('Location: ../index.php');
    exit();

}

//Modification du menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'modifier_menu') {
    $id_menu = (int) $_POST['id_menu'];
    $prix = (float) $_POST['prix'];
    $disponibilite = (bool) $_POST['disponibilite'];
    $theme = (string) $_POST['theme'];
    $stock = (int) $_POST['stock'];

    $entrees_regimes = [
    $_POST['regime-entree-1'] ?? null,
    $_POST['regime-entree-2'] ?? null,
    $_POST['regime-entree-3'] ?? null,
];

$plats_regimes = [
    $_POST['regime-plat-1'] ?? null,
    $_POST['regime-plat-2'] ?? null,
    $_POST['regime-plat-3'] ?? null,
];

$desserts_regimes = [
    $_POST['regime-dessert-1'] ?? null,
    $_POST['regime-dessert-2'] ?? null,
    $_POST['regime-dessert-3'] ?? null,
];


    // ----- ENTREES -----
    $entrees = [];

    if (!empty($_POST['entree1'])) {
        $entrees[] = trim($_POST['entree1']);
    }

    if (!empty($_POST['entree2'])) {
        $entrees[] = trim($_POST['entree2']);
    }

    if (!empty($_POST['entree3'])) {
        $entrees[] = trim($_POST['entree3']);
    }

    // ----- PLATS -----
    $plats = [];

    if (!empty($_POST['plat1'])) {
        $plats[] = trim($_POST['plat1']);
    }

    if (!empty($_POST['plat2'])) {
        $plats[] = trim($_POST['plat2']);
    }

    if (!empty($_POST['plat3'])) {
        $plats[] = trim($_POST['plat3']);
    }

    // ----- DESSERTS -----
    $desserts = [];

    if (!empty($_POST['dessert1'])) {
        $desserts[] = trim($_POST['dessert1']);
    }

    if (!empty($_POST['dessert2'])) {
        $desserts[] = trim($_POST['dessert2']);
    }

    if (!empty($_POST['dessert3'])) {
        $desserts[] = trim($_POST['dessert3']);
    }

    // Image principale
    $photoPrincipale = null;
    if (!empty($_FILES['images-menu-principale']['tmp_name'])) {
        $photoPrincipale = file_get_contents($_FILES['images-menu-principale']['tmp_name']);
    }

    // Update menu
    $sql = "UPDATE menus SET PrixParPersonne = ?, Disponibilité = ?, QuantitéRestante = ?, Thème = ?"
        . ($photoPrincipale ? ", Photo = ?" : "")
        . " WHERE id_menus = ?";
    $params = [$prix, $disponibilite, $stock, $theme];
    if ($photoPrincipale)
        $params[] = $photoPrincipale;
    $params[] = $id_menu;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Supprimer ancienne composition
    $pdo->prepare("DELETE FROM composition_menus WHERE id_menus = ?")->execute([$id_menu]);

    // Réinsertion
    $insert = $pdo->prepare("INSERT INTO composition_menus (id_menus, type, libellé, id_regime) VALUES (?, ?, ?, ?)");

// Entrées
foreach ($entrees as $i => $e) {
    $idRegime = $entrees_regimes[$i] ?? null;
    $insert->execute([$id_menu, 'entrée', $e, $idRegime]);
}

// Plats
foreach ($plats as $i => $p) {
    $idRegime = $plats_regimes[$i] ?? null;
    $insert->execute([$id_menu, 'plat', $p, $idRegime]);
}

// Desserts
foreach ($desserts as $i => $d) {
    $idRegime = $desserts_regimes[$i] ?? null;
    $insert->execute([$id_menu, 'dessert', $d, $idRegime]);
}


    // Galerie
    if (!empty($_FILES['images-menu-galerie']['tmp_name'])) {
        foreach ($_FILES['images-menu-galerie']['tmp_name'] as $tmpName) {
            if (!empty($tmpName)) {
                $photoGalerie = file_get_contents($tmpName);
                $pdo->prepare("INSERT INTO galerie_photos (id_menu, chemin_photo) VALUES (?, ?)")->execute([$id_menu, $photoGalerie]);
            }
        }
    }

}

//Ajout du menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom-menu'], $_POST['prix-menu'], $_POST['disponibilite-menu'])) {

    $nom_menu = trim($_POST['nom-menu']);
    $stock_menu = (int) trim($_POST['stock-menu']);
    $description_menu = trim($_POST['description-menu']);
    $prix = (float) $_POST['prix-menu'];
    $disponibilite = (bool) $_POST['disponibilite-menu'];
    $theme = (string) $_POST['theme-menu'];

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ---------- ENTREES ----------
    $entrees = [];

    if (!empty($_POST['entree-1'])) {
        $entrees[] = trim($_POST['entree-1']);
    }

    if (!empty($_POST['entree-2'])) {
        $entrees[] = trim($_POST['entree-2']);
    }

    if (!empty($_POST['entree-3'])) {
        $entrees[] = trim($_POST['entree-3']);
    }


    // ---------- PLATS ----------
    $plats = [];

    if (!empty($_POST['plat-1'])) {
        $plats[] = trim($_POST['plat-1']);
    }

    if (!empty($_POST['plat-2'])) {
        $plats[] = trim($_POST['plat-2']);
    }

    if (!empty($_POST['plat-3'])) {
        $plats[] = trim($_POST['plat-3']);
    }


    // ---------- DESSERTS ----------
    $desserts = [];

    if (!empty($_POST['dessert-1'])) {
        $desserts[] = trim($_POST['dessert-1']);
    }

    if (!empty($_POST['dessert-2'])) {
        $desserts[] = trim($_POST['dessert-2']);
    }

    if (!empty($_POST['dessert-3'])) {
        $desserts[] = trim($_POST['dessert-3']);
    }


    $erreurs = [];

    if (empty($nom_menu)) {
        $erreurs[] = "Le nom du menu est obligatoire.";
    }
    if (empty($entrees)) {
        $erreurs[] = "Au moins une entrée est obligatoire.";
    }
    if (empty($plats)) {
        $erreurs[] = "Au moins un plat est obligatoire.";
    }
    if (empty($desserts)) {
        $erreurs[] = "Au moins un dessert est obligatoire.";
    }

    if (empty($erreurs)) {


        $photo = null;
        if (!empty($_FILES['images-menu-general']['tmp_name'])) {
            $photo = file_get_contents($_FILES['images-menu-general']['tmp_name']);
        }

        $stmt = $pdo->prepare("INSERT INTO menus (Plat, PrixParPersonne, Disponibilité, Photo, QuantitéRestante, Description, Thème) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom_menu, $prix, $disponibilite, $photo, $stock_menu, $description_menu, $theme]);

        $id_menu = $pdo->lastInsertId();

        // TRAITEMENT DYNAMIQUE DES COMPOSITIONS (Entrées, Plats, Desserts)
    $categories = ['entree' => 'entrée', 'plat' => 'plat', 'dessert' => 'dessert'];
    
    foreach ($categories as $postKey => $dbType) {
        for ($i = 1; $i <= 3; $i++) {
            $libelleField = $postKey . "-" . $i;
            $regimeField = "regime-" . $postKey . "-" . $i;

            if (!empty($_POST[$libelleField])) {
                $libelle = trim($_POST[$libelleField]);
                
                $id_regime_plat = !empty($_POST[$regimeField]) ? (int)$_POST[$regimeField] : null;

                $stmtComp = $pdo->prepare("INSERT INTO composition_menus (id_menus, type, libellé, id_regime) VALUES (?, ?, ?, ?)");
                $stmtComp->execute([$id_menu, $dbType, $libelle, $id_regime_plat]);
            }
        }
    }

        // Insertion de toutes les images 
        if (!empty($_FILES['images-menu-ajout']['tmp_name'])) {
            foreach ($_FILES['images-menu-ajout']['tmp_name'] as $index => $tmpName) {
                if (!empty($tmpName)) {
                    $photo = file_get_contents($tmpName);
                    $stmt = $pdo->prepare("INSERT INTO galerie_photos (id_menu, chemin_photo) VALUES (?, ?)");
                    $stmt->execute([$id_menu, $photo]);
                }
            }
        }
    }

}
// Cache du menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer_menu') {
    $id_menu = (int) ($_POST['id_menu'] ?? 0);
    if ($id_menu > 0) {

        $pdo->prepare("UPDATE menus SET CacheAdmin = 1 WHERE id_menus = ?")->execute([$id_menu]);
    }
}

// Suppression de la galerie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer_img_galerie') {
    $id_img = (int) ($_POST['id_img'] ?? 0);
    if ($id_img > 0) {
        $pdo->prepare("DELETE FROM galerie_photos WHERE id_galerie = ?")->execute([$id_img]);
    }
}


$menus = $pdo->query("
    SELECT *
    FROM menus
    ORDER BY Plat DESC
")->fetchAll(PDO::FETCH_ASSOC);

$menus_existant = $pdo->query("
    SELECT m.*, 
           GROUP_CONCAT(CASE WHEN c.type='entrée' THEN c.libellé END SEPARATOR '||') AS entrees,
           GROUP_CONCAT(CASE WHEN c.type='plat' THEN c.libellé END SEPARATOR '||') AS plats,
           GROUP_CONCAT(CASE WHEN c.type='dessert' THEN c.libellé END SEPARATOR '||') AS desserts
    FROM menus m
    LEFT JOIN composition_menus c ON m.id_menus = c.id_menus
    WHERE m.CacheAdmin = 0
    GROUP BY m.id_menus
")->fetchAll(PDO::FETCH_ASSOC);


$regimes = $pdo->query("SELECT * FROM régime")->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="gestion-menus" id="menus">

    <h2 class="titre-centrer titres-dashboard">Gestion des Menus</h2>
    <button class="btn-ajout-menu" type="button">Ajouter un Menu</button>
    <?php
    if (!empty($erreurs)) {

        foreach ($erreurs as $err) {
            echo "<p style='color:red;'>$err</p>";
        }
    } ?>


    <!-- Formulaire de creation de menu (invisible avant le clic sur le bouton "ajout d'un menu")-->

    <form class="formulaires-dashboard" method="post" enctype="multipart/form-data">


        <div class="formulaires-dashboard-cacher">

            <div class="taille-textarea-ajout-menu">
                <label>Nom du menu :</label>
                <input type="text" name="nom-menu">
            </div>

                <label class="label-fichier" for="images-menu-general">Importer une image de menu</label>
                <input type="file" name="images-menu-general" id="images-menu-general" accept="image/*">

            <div class="taille-textarea-ajout-menu">
                <label>Description :</label>
                <textarea name="description-menu" rows="6"></textarea>
            </div>

            <?php
            $sections = [
                'entree' => 'Entrée',
                'plat'   => 'Plat',
                'dessert' => 'Dessert'
            ];

            foreach ($sections as $key => $label): ?>

                <h3 class="titre-form-section"><?= $label ?>s</h3>

                <?php for ($i = 1; $i <= 3; $i++): ?>

                    <div >
                        <div class="libele-et-textarea-colone">
                        <label ><?= $label ?> <?= $i ?> :</label>
                        <textarea name="<?= $key ?>-<?= $i ?>"></textarea>
                        </div>
                        
                        <div class="design-select">
                            <select name="regime-<?= $key ?>-<?= $i ?>">
                                <?php foreach ($regimes as $regime): ?>
                                    <option value="<?= htmlspecialchars($regime['id_regime']) ?>">
                                        <?= htmlspecialchars($regime['nom_regime']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                <?php endfor; ?>
            <?php endforeach; ?>

            <label>Prix :</label>
            <input type="number" step="0.01" name="prix-menu">

            <section class="espacement-select">

                <div class="design-select">
                    <label>Disponibilité :</label>
                    <select name="disponibilite-menu">
                        <option value="1">Disponible</option>
                        <option value="0">Indisponible</option>
                    </select>
                </div>

                <div class="design-select">
                    <label>Thème :</label>
                    <select name="theme-menu">
                        <option value="Menu traditionnel">Menu traditionnel</option>
                        <option value="Menu saisonnier">Menu saisonnier</option>
                        <option value="Menu événement">Menu événement</option>
                    </select>
                </div>

            </section>

            <label>Stock :</label>
            <input type="number" step="1" name="stock-menu">

            <label class="label-fichier" for="images-menu-ajout">Importer des images de galerie</label>
            <input type="file" name="images-menu-ajout[]" id="images-menu-ajout" accept="image/*" multiple>

            <button type="submit" class="btn-creer-menu">Créer</button>
        </div>

    </form>

    <!-- Section des menus existants -->
    <div class="modification-menu-existant">

        <h3 class="titre-centrer titre-menus-existants">MENUS CRÉÉS</h3>

        <?php if (empty($menus_existant)): ?>
            <p>Aucun menu créé pour le moment.</p>
        <?php else: ?>

            <?php foreach ($menus_existant as $menu): ?>
                <div class="carte-menu-admin">
                    <p class="vert"><?= htmlspecialchars($menu['Plat']) ?></p><br>

                    <?php

                        $entrees = explode('||', $menu['entrees']);
                        $plats = explode('||', $menu['plats']);
                        $desserts = explode('||', $menu['desserts']);
                        $entrees_regimes = $pdo->query("
                            SELECT * FROM composition_menus c LEFT JOIN régime r ON c.id_regime = r.id_regime WHERE c.id_menus = " 
                            . (int)$menu['id_menus'] . " AND c.type = 'entrée'
                            ORDER BY c.id_compo_menus ASC
                        ")->fetchAll(PDO::FETCH_ASSOC);
                                            $plats_regimes = $pdo->query("
                            SELECT * FROM composition_menus c LEFT JOIN régime r ON c.id_regime = r.id_regime WHERE c.id_menus = " 
                            . (int)$menu['id_menus'] . " AND c.type = 'plat'
                            ORDER BY c.id_compo_menus ASC
                        ")->fetchAll(PDO::FETCH_ASSOC);
                                            $desserts_regimes = $pdo->query("
                            SELECT * FROM composition_menus c LEFT JOIN régime r ON c.id_regime = r.id_regime WHERE c.id_menus = " 
                            . (int)$menu['id_menus'] . " AND c.type = 'dessert'
                            ORDER BY c.id_compo_menus ASC
                        ")->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <!-- Formulaire pour modifier les éléments directement -->

                    <form  method="POST" enctype="multipart/form-data" style="margin-bottom: 10px;">

                        <input type="hidden" name="id_menu" value="<?= $menu['id_menus'] ?>">
                        <input type="hidden" name="action" value="modifier_menu">

                        <!-- Image principale -->
                        <div class="alignement-bouton-img-menu">
                            <?php if (!empty($menu['Photo'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($menu['Photo']) ?>"
                                    alt="Menu <?= htmlspecialchars($menu['Plat']) ?>" class="photo-principale-menu">
                            <?php endif; ?>
                            
                            <div class="bouton-modif-colone">
                                <!-- Modifier l'image principale -->
                                <label class="btn-modif-img-menu" for="images-menu-principale-<?= $menu['id_menus'] ?>">Modifier l'image du menu</label>
                                <input type="file" name="images-menu-principale" id="images-menu-principale-<?= $menu['id_menus'] ?>" accept="image/*">

                                <!-- Ajouter plusieurs images pour alimenter la galerie -->
                                <label class="btn-modif-galerie" for="images-menu-galerie-<?= $menu['id_menus'] ?>">Ajouter des images dans la galerie</label>
                                <input type="file" name="images-menu-galerie[]" id="images-menu-galerie-<?= $menu['id_menus'] ?>" accept="image/*" multiple>
                            </div>

                        </div>

                        <!-- Liste des elements du menu (entrées / plats / desserts) -->
                        <section class="liste-elements-menu">
                                <ul>
                                    <!-- Entrées -->
                                    <li>
                                        <strong>Entrées :</strong><br>
                                        <input type="text" name="entree1" value="<?= htmlspecialchars($entrees[0] ?? '') ?>"
                                            placeholder="Entrée 1"><select name="regime-entree-1">
                                <?php foreach ($regimes as $reg): ?>
                                    <option value="<?= $reg['id_regime'] ?>" 
                                        <?= (isset($entrees_regimes[0])&&$entrees_regimes[0]["id_regime"] == $reg['id_regime']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($reg['nom_regime']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select><br>
                                        <input type="text" name="entree2" value="<?= htmlspecialchars($entrees[1] ?? '') ?>"
                                            placeholder="Entrée 2"><select name="regime-entree-2">
                                <?php foreach ($regimes as $reg): ?>
                                    <option value="<?= $reg['id_regime'] ?>" 
                                        <?= (isset($entrees_regimes[1])&&$entrees_regimes[1]["id_regime"] == $reg['id_regime']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($reg['nom_regime']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select><br>
                                        <input type="text" name="entree3" value="<?= htmlspecialchars($entrees[2] ?? '') ?>"
                                            placeholder="Entrée 3"><select name="regime-entree-3">
                                <?php foreach ($regimes as $reg): ?>
                                    <option value="<?= $reg['id_regime'] ?>" 
                                        <?= (isset($entrees_regimes[2])&&$entrees_regimes[2]["id_regime"] == $reg['id_regime']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($reg['nom_regime']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select><br>
                                    </li>

                                    <!-- Plats -->
                                    <li>
                                        <strong>Plats :</strong><br>
                                        <input type="text" name="plat1" value="<?= htmlspecialchars($plats[0] ?? '') ?>"
                                            placeholder="Plat 1"><select name="regime-plat-1">
                                <?php foreach ($regimes as $reg): ?>
                                    <option value="<?= $reg['id_regime'] ?>" 
                                        <?= (isset($plats_regimes[0])&&$plats_regimes[0]["id_regime"] == $reg['id_regime']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($reg['nom_regime']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select><br>
                                        <input type="text" name="plat2" value="<?= htmlspecialchars($plats[1] ?? '') ?>"
                                            placeholder="Plat 2"><select name="regime-plat-2">
                                <?php foreach ($regimes as $reg): ?>
                                    <option value="<?= $reg['id_regime'] ?>" 
                                        <?= (isset($plats_regimes[1])&&$plats_regimes[1]["id_regime"] == $reg['id_regime']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($reg['nom_regime']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select><br>
                                        <input type="text" name="plat3" value="<?= htmlspecialchars($plats[2] ?? '') ?>"
                                            placeholder="Plat 3"><select name="regime-plat-3">
                                <?php foreach ($regimes as $reg): ?>
                                    <option value="<?= $reg['id_regime'] ?>" 
                                        <?= (isset($plats_regimes[2])&&$plats_regimes[2]["id_regime"] == $reg['id_regime']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($reg['nom_regime']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                                    </li>

                                    <!-- Desserts -->
                                    <li>
                                        <strong>Desserts :</strong><br>
                                        <input type="text" name="dessert1" value="<?= htmlspecialchars($desserts[0] ?? '') ?>"
                                            placeholder="Dessert 1">
                                            <select name="regime-dessert-1">
                                <?php foreach ($regimes as $reg): ?>
                                    <option value="<?= $reg['id_regime'] ?>" 
                                        <?= (isset($desserts_regimes[0])&&$desserts_regimes[0]["id_regime"] == $reg['id_regime']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($reg['nom_regime']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select><br>
                                        <input type="text" name="dessert2" value="<?= htmlspecialchars($desserts[1] ?? '') ?>"
                                            placeholder="Dessert 2"><select name="regime-dessert-2">
                                <?php foreach ($regimes as $reg): ?>
                                    <option value="<?= $reg['id_regime'] ?>" 
                                        <?= (isset($desserts_regimes[1])&&$desserts_regimes[1]["id_regime"] == $reg['id_regime']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($reg['nom_regime']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select><br>
                                        <input type="text" name="dessert3" value="<?= htmlspecialchars($desserts[2] ?? '') ?>"
                                            placeholder="Dessert 3"><select name="regime-dessert-3">
                                <?php foreach ($regimes as $reg): ?>
                                    <option value="<?= $reg['id_regime'] ?>" 
                                        <?= (isset($desserts_regimes[2])&&$desserts_regimes[2]["id_regime"] == $reg['id_regime']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($reg['nom_regime']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                                    </li>
                                </ul>
                        </section>

                        <!-- Prix, disponibilité, thème, stock -->

                        <section class="espacement-select">
                            <div class="design-select">
                                <p>
                                    <strong>Prix :</strong>
                                    <input type="number" step="0.01" name="prix"
                                        value="<?= htmlspecialchars($menu['PrixParPersonne']) ?>">
                                </p>
                            </div>

                            <div class="design-select">
                                <p>
                                    <strong>Disponibilité :</strong>
                                    <select name="disponibilite">
                                        <option value=1 <?= ($menu['Disponibilité'] == 1) ? 'selected' : '' ?>>Disponible
                                        </option>
                                        <option value=0 <?= ($menu['Disponibilité'] == 0) ? 'selected' : '' ?>>Indisponible
                                        </option>
                                    </select>
                                </p>
                            </div>

                            <div class="design-select">
                                <p>
                                    <strong>Thème :</strong>
                                    <select name="theme">
                                        <option value="Menu traditionnel" <?= ($menu['Thème'] == "Menu traditionnel") ? 'selected' : '' ?>>
                                            Menu traditionnel
                                        </option>
                                        <option value="Menu saisonnier" <?= ($menu['Thème'] == "Menu saisonnier") ? 'selected' : '' ?>>
                                            Menu saisonnier
                                        </option>
                                        <option value="Menu événement" <?= ($menu['Thème'] == "Menu événement") ? 'selected' : '' ?>>
                                            Menu événement
                                        </option>
                                    </select>
                                </p>
                            </div>
                            <div class="design-select">
                                <p>
                                    <strong>Stock :</strong>
                                    <input type="number" step="1" name="stock"
                                        value="<?= htmlspecialchars($menu['QuantitéRestante']) ?>">
                                </p>
                            </div>
                        </section>

                        <button type="submit" class="btn-enregistrer-menu">Enregistrer les modifications</button>

                    </form>

                    <!-- Galerie de photos -->

                    <div class="alignement-img-galerie">
                                <?php
                                $galerieStmt = $pdo->prepare("SELECT * FROM galerie_photos WHERE id_menu = ? ORDER BY id_galerie ASC");
                                $galerieStmt->execute([$menu['id_menus']]);
                                $galeriePhotos = $galerieStmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($galeriePhotos as $photoGalerie):
                                ?>
                                    <form class="bordures-formulaire-invisible" method="POST" onsubmit="return confirm('Supprimer cette image ?');">
                                        <img class="galerie-photo-dashboard" src="data:image/jpeg;base64,<?= base64_encode($photoGalerie["chemin_photo"]) ?>"
                                            alt="Galerie <?= htmlspecialchars($menu['Plat']) ?>">
                                        <input type="hidden" name="id_img" value="<?= $photoGalerie['id_galerie'] ?>">
                                        <input type="hidden" name="action" value="supprimer_img_galerie">
                                        <button class="btn-supprimer-galerie" type="submit">Supprimer l'image</button>
                                    </form>
                                <?php endforeach; ?>
                    </div>

                    <!-- Supprimer -->
                    <form class="bordures-formulaire-invisible" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce menu ?');">
                        <input type="hidden" name="id_menu" value="<?= $menu['id_menus'] ?>">
                        <input type="hidden" name="action" value="supprimer_menu">
                        <button class="btn-supprimer-menu" type="submit">Supprimer le menu</button>
                    </form>
                </div>
            <?php endforeach; ?>


        <?php endif; ?>

    </div>

</div>