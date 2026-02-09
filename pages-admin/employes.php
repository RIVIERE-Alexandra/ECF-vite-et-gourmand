<?php

if (basename($_SERVER['PHP_SELF']) === 'employes.php') {
    header('Location: ../index.php');
    exit();
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creer_employe'])) {

    $prenom = $_POST['prenom-employe'];
    $email = trim($_POST['e-mail-employe']);
    $password = $_POST['mot-de-passe-employe'];
    $role = 'Employé';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Adresse e-mail invalide !";
    } else {


        $checkStmt = $pdo->prepare("SELECT id_employe FROM employés WHERE Mail = :mail");
        $checkStmt->execute([':mail' => $email]);

        if ($checkStmt->fetch()) {
            $message = "Un compte avec cet e-mail existe déjà !";
        } else {

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = $pdo->prepare("
                INSERT INTO employés (Mail, MotDePasse, Rôle,Prénom)
                VALUES (:mail, :password, :role, :prenom)
            ");

            $insertStmt->execute([
                ':mail' => $email,
                ':password' => $passwordHash,
                ':role' => $role,
                ':prenom' => $prenom
            ]);

            $message = "";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if (isset($_POST['id_employe'])) {

        $id = (int) $_POST['id_employe'];

        // SUPPRIMER
        if ($_POST['action'] === 'supprimer') {
            $stmt = $pdo->prepare("DELETE FROM employés WHERE id_employe = ?");
            $stmt->execute([$id]);
        }

        // MODIFIER
        if ($_POST['action'] === 'modifier') {
            $nom = trim($_POST['nom_employe']);
            $email = trim($_POST['mail_employe']);

            $stmt = $pdo->prepare("
                UPDATE employés 
                SET Prénom = ?, Mail = ?
                WHERE id_employe = ?
            ");
            $stmt->execute([$nom, $email, $id]);
        }

    }

}
$stmt = $pdo->query("SELECT * FROM employés ORDER BY Prénom");
$employes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="gestion-employe" id="parametrage-employe">

                    <h2 class="titre-centrer">Gestion des Employés</h2>

                    <div class="formulaires-dashboard">
                        <?php if (!empty($message)): ?>
                            <p class="message-info"><?= htmlspecialchars($message) ?></p>
                        <?php endif; ?>

                        <button class="btn-ajout-employe" type="button">Ajouter un employé</button>

                        <div class="formulaires-dashboard-employe-cacher">

                            <h3>Créer un compte employé :</h3>

                            <form class="formulaire-employes" method="post">
                                <label for="prenom-employe">Nom - Prénom :</label>
                                <input type="text" name="prenom-employe" id="prenom-employe" required>

                                <label for="e-mail-employe">E-mail :</label>
                                <input type="email" name="e-mail-employe" id="e-mail-employe" required>

                                <label for="mot-de-passe-employe">Mot de passe :</label>
                                <input type="password" name="mot-de-passe-employe" id="mot-de-passe-employe" required>

                                <button class="btn-creer-employe" type="submit" name="creer_employe">Créer</button>
                            </form>
                        </div>
                    </div>

                    <div class="liste-employe">

                        <h3>Liste des employés :</h3>

                        <table class="fond-tableaux" id="tableau-employes">
                            <thead class="entete-tableaux">
                                <tr>
                                    <th>Nom / Prénom</th>
                                    <th>Email</th>
                                    <th>Modifier</th>
                                    <th>Supprimer</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($employes as $emp): ?>
                                    <tr>
                                        <form method="POST">

                                            <td>
                                                <input type="text" name="nom_employe"
                                                    value="<?= htmlspecialchars($emp['Prénom']) ?>">
                                            </td>

                                            <td>
                                                <input type="email" name="mail_employe"
                                                    value="<?= htmlspecialchars($emp['Mail']) ?>">
                                            </td>

                                            <td>
                                                <input type="hidden" name="id_employe" value="<?= $emp['id_employe'] ?>">
                                                <input type="hidden" name="action" value="modifier">
                                                <button class="btn-valider-cmd" type="submit">
                                                    Modifier
                                                </button>
                                            </td>

                                            <td>
                                                <button class="btn-valider-cmd" type="submit" name="action"
                                                    value="supprimer" onclick="return confirm('Supprimer cet employé ?');">
                                                    Supprimer
                                                </button>
                                            </td>

                                        </form>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>
                </div>