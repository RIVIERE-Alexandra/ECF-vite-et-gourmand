<?php
if (basename($_SERVER['PHP_SELF']) === 'stats.php') {
    header('Location: ../index.php');
    exit();


}
?>

<div class="chiffre-affaire" id="statistiques">

    <h2 class="titre-centrer titres-dashboard">Statistiques et Chiffre d'Affaires : </h2>

    <!-- Formulaire pour ajouter les stats a la main-->
    <button class="btn-ajouter-stats">Ajouter statistiques</button>

    <form id="formulaire-stats">
        <fieldset>
            <legend>Ajouter le chiffre d'affaires mensuel :</legend>
            <h4>Chiffres d'affaires par mois :</h4>
            <label for="annee">Année :</label>
            <input type="number" name="annee" id="annee" value="2025" min="2000" max="2100">

            <div class="inputs-mois">
                <label for="janvier">Janvier : </label>
                <input type="number" name="janvier" value="0">

                <label for="fevrier">Février : </label>
                <input type="number" name="fevrier" value="0">

                <label for="mars">Mars : </label>
                <input type="number" name="mars" value="0">

                <label for="avril">Avril : </label>
                <input type="number" name="avril" value="0">

                <label for="mai">Mai : </label>
                <input type="number" name="mai" value="0">

                <label for="juin">Juin : </label>
                <input type="number" name="juin" value="0">

                <label for="juillet">Juillet : </label>
                <input type="number" name="juillet" value="0">

                <label for="aout">Août : </label>
                <input type="number" name="aout" value="0">

                <label for="septembre">Septembre : </label>
                <input type="number" name="septembre" value="0">

                <label for="octobre">Octobre : </label>
                <input type="number" name="octobre" value="0">

                <label for="novembre">Novembre : </label>
                <input type="number" name="novembre" value="0">

                <label for="decembre">Décembre : </label>
                <input type="number" name="decembre" value="0">

            </div>
            <button type="submit" class="btn-enregistrer-stats">Enregistrer</button>
        </fieldset>
    </form>

    <div class="diagramme">

        <h3>Statistiques annuelles :</h3>

        <label for="select-annee">Choisir une année</label>
        <select id="select-annee" name="annee" class="select-annee">
            <option value="2023">2023</option>
            <option value="2024">2024</option>
            <option value="2025" selected>2025</option>
        </select>

        <canvas id="statistiques-annuelles" role="img" aria-label="Nombre de commandes par menu sur l'année" width="800"
            height="300">
        </canvas>

    </div>

    <!-- section filtres par periode ou menus -->
    <div class="filtres-menu-periode">

        <h3>Trier par :</h3>

        <form class="formulaires-dashboard">
            <fieldset>
                <legend>Filtres pour le tableau des commandes par menu</legend>

                <div class="filtres-menu-dashboard">

                    <label for="filtre-menu">Menus :</label>
                    <select id="filtre-menu" name="filtre-menu">
                        <option value="tout">Tous</option>
                        <!-- autres options dynamiques via back : menu 1, menu2, menu3... -->
                    </select>

                    <label for="filtre-periode">Période :</label>
                    <select id="filtre-periode" name="filtre-periode">
                        <option value="1mois">1 mois</option>
                        <option value="3mois">3 mois</option>
                        <option value="1an">1 an</option>
                    </select>

                    <button class="btn-appliquer" type="submit">Appliquer</button>
                </div>

            </fieldset>
        </form>
    </div>

    <div class="commandes-par-menus">
        <h3>Commandes par type de menu :</h3>
        <table class="fond-tableaux" id="tableau-recettes-mois">

            <thead class="entete-tableaux">
                <tr>
                    <th>Menu</th>
                    <th>Nombre de Commandes</th>
                    <th>Total des recettes</th>
                </tr>
            </thead>

            <tbody>
                <?php
                // 1. Récupérer toutes les commandes terminées
                $stmt = $pdo->prepare("SELECT * FROM commandes WHERE statut = 'terminee'");
                $stmt->execute();
                $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 2. Créer un tableau pour accumuler les stats par menu
                $statsMenus = [];

                // 3. Parcourir toutes les commandes
                foreach ($commandes as $commande) {
                    $id_commande = $commande['id_commandes'];

                    // Récupérer les lignes de commandes pour chaque commande
                    $stmt = $pdo->prepare("
        SELECT 
            lc.id_menus,
            lc.quantité,
            m.PrixParPersonne,
            m.Plat AS nomMenu
        FROM ligne_commandes lc
        JOIN menus m ON lc.id_menus = m.id_menus
        WHERE lc.id_commandes = ?
    ");
                    $stmt->execute([$id_commande]);
                    $lignes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Accumuler les stats
                    foreach ($lignes as $ligne) {
                        $nomMenu = $ligne['nomMenu'];
                        $quantite = (int) $ligne['quantité'];
                        $prixTotal = $quantite * (float) $ligne['PrixParPersonne'];

                        if (!isset($statsMenus[$nomMenu])) {
                            $statsMenus[$nomMenu] = [
                                'nombre_commandes' => 0,
                                'total_recettes' => 0
                            ];
                        }

                        $statsMenus[$nomMenu]['nombre_commandes'] += $quantite;
                        $statsMenus[$nomMenu]['total_recettes'] += $prixTotal;
                    }
                }

                // 4. Afficher les stats correctement : 1 ligne par menu
                foreach ($statsMenus as $nomMenu => $stats) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($nomMenu) . "</td>";
                    echo "<td>" . $stats['nombre_commandes'] . "</td>";
                    echo "<td>" . number_format($stats['total_recettes'], 2, ',', ' ') . "€</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>

        </table>
    </div>
</div>