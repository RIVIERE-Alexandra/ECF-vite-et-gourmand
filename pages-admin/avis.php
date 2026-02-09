<?php

if (basename($_SERVER['PHP_SELF']) === 'avis.php') {
    header('Location: ../index.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id_avis'])) {

    $id_avis = intval($_POST['id_avis']);

    if ($_POST['action'] === 'valider') {
        // Met le statut à 1 (validé)
        $stmt = $pdo->prepare("UPDATE avis_clients SET statut = 1 WHERE id_avis = :id");
        $stmt->execute([':id' => $id_avis]);
    } elseif ($_POST['action'] === 'supprimer') {
        // Supprime l'avis
        $stmt = $pdo->prepare("DELETE FROM avis_clients WHERE id_avis = :id");
        $stmt->execute([':id' => $id_avis]);
    }

}
$stmt = $pdo->query("SELECT a.id_avis, a.note, a.description, a.statut, a.date_publication,  CONCAT(c.prénom, ' ', c.nom) AS client_nom
                     FROM avis_clients a 
                     LEFT JOIN client c ON a.id_client = c.id_client
                     ORDER BY a.date_publication DESC");
$avis = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="gestion-avis" id="validation-avis">
    <h2 class="titre-centrer titres-dashboard">Gestion des Avis Clients</h2>

    <table class="fond-tableaux">
        <thead class="entete-tableaux">
            <tr>
                <th>Client</th>
                <th>Note</th>
                <th>Avis</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($avis as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['client_nom'] ?? 'Inconnu') ?></td>
                    <td><?= $a['note'] ?>/5</td>
                    <td><?= htmlspecialchars($a['description']) ?></td>
                    <td>
                        <?= $a['statut'] == 1 ? 'Validé' : 'En attente' ?>
                    </td>
                    <td><?= $a['date_publication'] ?></td>
                    <td>
                        <div class="alignement-bouton-actions-avis">
                            <form class="bordures-formulaire-invisible" method="post" style="display:inline">
                                <input type="hidden" name="id_avis" value="<?= $a['id_avis'] ?>">
                                <button class="btn-valider-supprimer-avis" type="submit" name="action"
                                    value="valider">Valider</button>
                            </form>
                            <form class="bordures-formulaire-invisible" method="post" style="display:inline">
                                <input type="hidden" name="id_avis" value="<?= $a['id_avis'] ?>">
                                <button class="btn-valider-supprimer-avis" type="submit" name="action"
                                    value="supprimer">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>