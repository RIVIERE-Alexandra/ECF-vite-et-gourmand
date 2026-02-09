<?php
if (basename($_SERVER['PHP_SELF']) === 'commandes.php') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_commandes'], $_POST['statut'])) {

    $id_commandes = (int) $_POST['id_commandes'];
    $nouveau_statut = $_POST['statut'];

    //Ancien statut
    $stmt = $pdo->prepare("SELECT statut FROM commandes WHERE id_commandes = ?");
    $stmt->execute([$id_commandes]);
    $ancien_statut = $stmt->fetchColumn();

    if ($nouveau_statut === 'accepte' && $ancien_statut !== 'accepte') {

        $stmt = $pdo->prepare("
            SELECT lc.id_menus, lc.quantité, m.QuantitéRestante
            FROM ligne_commandes lc
            JOIN menus m ON lc.id_menus = m.id_menus
            WHERE lc.id_commandes = ?
        ");
        $stmt->execute([$id_commandes]);
        $lignes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($lignes as $ligne) {
            if ($ligne['QuantitéRestante'] < $ligne['quantité']) {
                echo "<p style='color:red;'>Stock insuffisant pour un menu.</p>";
                exit;
            }
        }

        $updateStock = $pdo->prepare("
            UPDATE menus
            SET QuantitéRestante = QuantitéRestante - ?
            WHERE id_menus = ?
        ");

        foreach ($lignes as $ligne) {
            $updateStock->execute([
                (int) $ligne['quantité'],
                (int) $ligne['id_menus']
            ]);
        }
        $subject = "Acceptation de votre commande Vite Et Gourmand";
        $message = "Merci de votre commande sur notre site Vite Et Gourmand !\nNous vous informons que votre commande est en cours de préparation !";
        $headers = "From: no-reply@viteetgourmand.fr\r\n";
        $headers .= "Reply-To: no-reply@viteetgourmand.fr\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        //mail($email, $subject, $message, $headers) PAS DENVOI CAR PAS DE SERVEUR MAIL
    }
    if ($nouveau_statut === 'terminée') {
        $subject = "Merci de votre commande Vite Et Gourmand";
        $message = "Merci de votre commande sur notre site Vite Et Gourmand !\nVous pouvez vous connecter à votre compte pour nous laisser un avis !";
        $headers = "From: no-reply@viteetgourmand.fr\r\n";
        $headers .= "Reply-To: no-reply@viteetgourmand.fr\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        //mail($email, $subject, $message, $headers) PAS DENVOI CAR PAS DE SERVEUR MAIL
    }



    $stmt = $pdo->prepare("UPDATE commandes SET statut = ? WHERE id_commandes = ?");
    $stmt->execute([$nouveau_statut, $id_commandes]);

}
?>
<div class="commandes-recues" id="cmd-recue">

    <h2 class="titre-centrer">Commandes reçues</h2>

    <table class="fond-tableaux" id="tableau-commandes">
        <thead class="entete-tableaux">
            <tr>
                <th>N° commande</th>
                <th>Client</th>
                <th>Informations Menu</th>
                <th>Date commande Adresse</th>
                <th>Statut commande</th>
                <th> Boutons Actions</th>
            </tr>
        </thead>
        <tbody class="texte-corps-tableaux">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM commandes WHERE statut != 'panier' AND statut != 'refus' AND statut != 'terminee' ORDER BY Date_de_commande DESC");
            $stmt->execute();
            $commandes = $stmt->fetchall(PDO::FETCH_ASSOC);
            foreach ($commandes as $commande) {
                $stmtclient = $pdo->prepare("SELECT * FROM client WHERE id_client = ?");
                $stmtclient->execute([$commande["id_client"]]);
                $client = $stmtclient->fetch(PDO::FETCH_ASSOC);
                echo "<tr><td>" . $commande['id_commandes'] . "</td>";
                echo "<td class='nom-prenom-client'>" . $client["Nom"] . "<br>" . $client["Prénom"] . "</td>";


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
                echo "<td>";
                if (!empty($lignes[0]['pret_de_materiel'])){
                    echo "<em>Prêt de matériel demandé</em><br><br>";
                }else{
                    echo "<em>Pas de prêt de matériel</em><br><br>";
                }
                  
                foreach ($lignes as $ligne) {

                    echo "<strong>" . htmlspecialchars($ligne['nomMenu']) . " x" . htmlspecialchars($ligne['quantité']) . "</strong><br>";

                    // Colone recap choix menus
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
                echo "<td>" . date('d/m/Y', strtotime($commande['Date_de_commande'])) . "<br>" . htmlspecialchars($commande['Adresse_Livraison']) . "</td>";

                $statuts = [
                    'en_attente' => 'En Attente',
                    'accepter' => 'Acceptée',
                    'en_cours' => 'En Cours',
                    'en_livraison' => 'En Livraison',
                    'refuser' => 'Refusée',
                    'terminee' => 'Terminée'
                ];

                echo "<td>" . ($statuts[$commande['statut']] ?? $commande['statut']) . "</td>";

                echo "<td class='boutons-actions-cmd'>
        <form class=\"bordures-formulaire-invisible\" method='POST' class='formulaire-info-prestation'>
            <input type='hidden' name='id_commandes' value='" . $commande['id_commandes'] . "'>
            <button type='submit' name='statut' value='accepter' class='btn-valider-cmd'>Accepter</button>
            <button type='submit' name='statut' value='refuser' class='btn-valider-cmd'>Refuser</button>
            <button type='submit' name='statut' value='en_cours' class='btn-valider-cmd'>En Cours</button>
            <button type='submit' name='statut' value='en_livraison' class='btn-valider-cmd'>En Livraison</button>
            <button type='submit' name='statut' value='terminee' class='btn-valider-cmd'>Terminer</button>
        </form>
      </td>";

                echo "</tr>";

            }
            ?>

        </tbody>
    </table>
</div>