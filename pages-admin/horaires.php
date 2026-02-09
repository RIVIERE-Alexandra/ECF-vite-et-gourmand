<?php

if (basename($_SERVER['PHP_SELF']) === 'horaires.php') {
    header('Location: ../index.php');
    exit();
}

$jours = [
    'lundi' => 'Lundi',
    'mardi' => 'Mardi',
    'mercredi' => 'Mercredi',
    'jeudi' => 'Jeudi',
    'vendredi' => 'Vendredi',
    'samedi' => 'Samedi',
    'dimanche' => 'Dimanche'
];

// =======================
// TRAITEMENT DU FORMULAIRE
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enregistrer_horaires'])) {

    $checkStmt = $pdo->prepare("SELECT id_horaires FROM horaires WHERE jour = :jour");
    $insertStmt = $pdo->prepare("
        INSERT INTO horaires (jour, heure_ouverture, heure_fermeture)
        VALUES (:jour, :ouverture, :fermeture)
    ");
    $updateStmt = $pdo->prepare("
        UPDATE horaires
        SET heure_ouverture = :ouverture, heure_fermeture = :fermeture
        WHERE jour = :jour
    ");
    $deleteStmt = $pdo->prepare("
        DELETE FROM horaires WHERE jour = :jour
    ");

    foreach ($jours as $key => $label) {

        $jourNom = $label; // Lundi, Mardi…
        $ferme = isset($_POST["fermeture_$key"]);

        $checkStmt->execute([":jour" => $jourNom]);
        $existe = $checkStmt->fetchColumn();

        if ($ferme) {
            if ($existe) {
                $deleteStmt->execute([":jour" => $jourNom]);
            }
            continue;
        }

        // Jour ouvert
        $ouverture = $_POST["$key-ouverture"];
        $fermeture = $_POST["$key-fermeture"];

        if ($existe) {
            $updateStmt->execute([
                ":jour" => $jourNom,
                ":ouverture" => $ouverture,
                ":fermeture" => $fermeture
            ]);
        } else {
            $insertStmt->execute([
                ":jour" => $jourNom,
                ":ouverture" => $ouverture,
                ":fermeture" => $fermeture
            ]);
        }
    }
}

// =======================
// RÉCUPÉRATION POUR AFFICHAGE
// =======================
$stmt = $pdo->query("SELECT jour, heure_ouverture, heure_fermeture FROM horaires");

$horaires = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $horaires[strtolower($row['jour'])] = $row;
}
?>

<h2 class="titre-centrer titres-dashboard" id="horaires">Gestion des Horaires</h2>

<div class="gestion-horaires">
    <form method="POST">

        <?php foreach ($jours as $key => $label):
            $existe = isset($horaires[$key]);
            $ouverture = $existe ? substr($horaires[$key]['heure_ouverture'], 0, 5) : '11:00';
            $fermeture = $existe ? substr($horaires[$key]['heure_fermeture'], 0, 5) : '22:00';
            ?>

            <label><?= $label ?> :</label>
            <div class="alignement-horaires">

                <input type="time" name="<?= $key ?>-ouverture" value="<?= $ouverture ?>">

                <span>à</span>

                <input type="time" name="<?= $key ?>-fermeture" value="<?= $fermeture ?>">

                <label>
                    <input type="checkbox" name="fermeture_<?= $key ?>" <?= !$existe ? 'checked' : '' ?>>
                    Fermer
                </label>
            </div>

        <?php endforeach; ?>

        <button class="btn-enregistrer" type="submit" name="enregistrer_horaires">
            Enregistrer les horaires
        </button>
    </form>
</div>