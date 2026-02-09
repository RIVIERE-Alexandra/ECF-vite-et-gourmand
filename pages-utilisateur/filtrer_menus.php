<?php
require_once '../bd.php';

$regimes = $_POST['regime'] ?? '';
$theme = $_POST['theme'] ?? '';
$prixMax = $_POST['prixMax'] ?? 1000;
$nbPersonnes = $_POST['nbPersonnes'] ?? 1;
$triPrix = $_POST['triPrix'] ?? '';

$query = "
SELECT *
FROM menus
WHERE Disponibilité = 1
AND CacheAdmin = 0
  AND PrixParPersonne <= :prixMax
  AND QuantitéRestante >= :nbPersonnes
";

$params = [
    ':prixMax' => (float) $prixMax,
    ':nbPersonnes' => (int) $nbPersonnes
];

/* ===== Filtre régimes ===== */
if (!empty($regimes)) {
    $stmt = $pdo->prepare("SELECT * from composition_menus JOIN régime ON régime.id_regime = composition_menus.id_regime WHERE régime.id_regime = :regime");
    $stmt->execute([':regime' => $regimes]);
    $regime_exist = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($regime_exist) > 0) {
        $query .= " AND id_menus IN (
        SELECT DISTINCT id_menus
        FROM composition_menus
        JOIN régime ON régime.id_regime = composition_menus.id_regime
        WHERE régime.id_regime = :regime
    )";
        $params[':regime'] = $regimes;
    } else {
        // Si le régime n'existe pas, on ajoute une condition impossible pour ne retourner aucun menu
        $query .= " AND 1=0";
    }
}
/* ===== Filtre thème ===== */
if (!empty($theme)) {
    $query .= " AND Thème LIKE :theme";
    $params[':theme'] = "%$theme%";

}

// Tri par prix
if ($triPrix === 'asc')
    $query .= " ORDER BY PrixParPersonne ASC";
elseif ($triPrix === 'desc')
    $query .= " ORDER BY PrixParPersonne DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
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
                        personnes de plus que le nombre
                        de personnes minimum indiqué dans le menu</p>

                    <div class="ajustement-quantite-menus" data-stock="<?= $menu['QuantitéRestante'] ?>">

                        <form method="post">
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



                            <div>
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