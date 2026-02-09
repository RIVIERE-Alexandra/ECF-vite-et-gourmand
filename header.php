<?php


$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
$role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
$nb_menus = isset($_SESSION['nb_menus']) ? $_SESSION['nb_menus'] : '';
?>

<header>
    <div class="infos-header">
        <a href="/ECF-vite-et-gourmand/">
            <img id="logo-site" src="/ECF-vite-et-gourmand/assets/logo.png" alt="Logo de Vite et Gourmand">
        </a>
        <nav aria-label="Menu principal">
            <ul class="menu-principal">
                <li><a href="/ECF-vite-et-gourmand/">Accueil</a></li>
                <li><a href="/ECF-vite-et-gourmand/pages-utilisateur/nous-contacter.php">Contact</a></li>
                <li><a href="/ECF-vite-et-gourmand/pages-utilisateur/menus.php">Menus</a></li>
                <li>
                    <a href="/ECF-vite-et-gourmand/pages-utilisateur/recap-commande.php">
                        Ma Commande
                        <?php if ($nb_menus > 0): ?>
                            <sup style="font-size: 0.7em; color: red;"><?= $nb_menus ?></sup>
                        <?php endif; ?>
                    </a>
                </li>

                <li>
                    <!-- Gestion de la connexion utilisateur -->
                    <?php if ($is_logged_in):
                        // Lien selon le rôle
                        if ($role === 'Admin' || $role === 'Employé') {
                            $profile_link = '/ECF-vite-et-gourmand/pages-admin/index-admin.php';
                        } else {
                            $profile_link = '/ECF-vite-et-gourmand/pages-utilisateur/espace-utilisateur.php';
                        }
                        ?>

                        <a href="<?= htmlspecialchars($profile_link) ?>" style="color: black; margin-right: 25px;">
                            <?= htmlspecialchars($user_name) ?>
                        </a>


                        <a href="/ECF-vite-et-gourmand/deconnexion.php">Déconnexion</a> <!-- Lien vers la déconnexion -->
                    <?php else: ?>
                        <a href="/ECF-vite-et-gourmand/pages-utilisateur/login.php">Connexion</a>
                    <?php endif; ?>
                </li>
            </ul>
            <div class="burger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </div>
</header>