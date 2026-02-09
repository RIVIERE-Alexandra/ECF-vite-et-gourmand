<?php

require_once 'bd.php';

try {

    $sql = "SELECT jour, heure_ouverture, heure_fermeture FROM horaires ORDER BY FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $horaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de récupération des horaires : " . $e->getMessage();
    $horaires = [];
}
?>

<footer>
    <p class="vert">“Vite et Gourmand – Des plats faits maison, livrés chez vous !”</p>

    <section class="infos-footer">

        <div class="infos-contact">
            <p>Adresse : Pl. Pey Berland, 33000 Bordeaux France</p>
            <p>Téléphone : 06 12 34 56 78</p>
            <p>Mail : contact@viteetgourmand.fr</p>
        </div>

        <div class="infos-contact">
            <p>Horaires :</p>
            <ul class="horaires-list">
                <?php
                if (!empty($horaires)) {
                    foreach ($horaires as $horaire) {
                        $ouverture = substr($horaire['heure_ouverture'], 0, 5);
                        $fermeture = substr($horaire['heure_fermeture'], 0, 5);

                        echo "<li>{$horaire['jour']} : {$ouverture} - {$fermeture}</li>";
                    }
                } else {
                    echo "<li>Aucun horaire disponible.</li>";
                }

                ?>
            </ul>
        </div>
        

        <nav aria-label="Liens légaux du site">
            <ul class="liens-legaux-espacement">
                <li><a href="/ECF-vite-et-gourmand/pages-utilisateur/mentions-legales.php">Mentions légales</a></li>
                <li><a href="/ECF-vite-et-gourmand/pages-utilisateur/conditions-generales.php">Conditions générales de
                        vente</a></li>
                <li><a href="/ECF-vite-et-gourmand/pages-utilisateur/politique-confidentialite.php">Politique de
                        confidentialité</a></li>
            </ul>
        </nav>

    </section>
</footer>