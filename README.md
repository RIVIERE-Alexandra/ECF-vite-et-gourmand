READ ME :

Vite & Gourmand -- Application web

Description

Vite & Gourmand est une application web qui permet de visualiser les
menus proposés, passer des commandes et gérer les espaces utilisateur,
employé et administrateur. Le site est conçu pour être fonctionnel et
facile à utiliser, même pour les personnes non technophiles.

Prérequis

Pour déployer l'application en local, vous aurez besoin de :

-   WampServer ou un autre serveur local compatible PHP
-   PHP (version compatible avec votre WampServer)
-   MySQL / PhpMyAdmin
-   Navigateur récent (Chrome, Firefox, Edge...) -- je précise que
    n'utilisant aucun matériel Apple je ne sais pas si le site aura le
    bon rendu sur safari.

Installation / Déploiement

1.  Créez un dossier ENV sur votre disque dur C: ou dans un emplacement
    de votre choix. A partir du dossier ENV, y installer votre wamp
    serveur. Apres quoi vous pourrez y insérer le dossier « vite et
    gourmand » dans le sous dossier www. (vous pouvez aussi faire un
    alias sur un autre dossier selon votre préférence.)
2.  Copiez le dossier du projet ECF-VITE-ET-GOURMAND dans ce dossier.
3.  Démarrez WampServer et assurez-vous que le serveur Apache et MySQL
    sont en ligne.
4.  Importez la base de données via PhpMyAdmin depuis le fichier
    vite-et-gourmand-BDD.sql fourni.
5.  Vérifiez le fichier vite-et-gourmand-BDD.php et mettez à jour les
    identifiants de connexion si nécessaire (utilisateur, mot de passe,
    nom de la base).
6.  Ouvrez le projet dans votre navigateur en accédant à
    [*http://localhost/ECF-VITE-ET-GOURMAND/index.php*](http://localhost/ECF-VITE-ET-GOURMAND/index.php).

Structure des dossiers

-   assets/ → contient les images utilisées dans le site
-   CSS/ → feuilles de style CSS
-   JavaScript/ → scripts JS
-   pages-utilisateur/ → pages accessibles aux utilisateurs
-   pages-admin/ → pages accessibles aux administrateurs
-   header.php, footer.php → parties communes aux pages
-   bd.php → fichier de connexion à la base de données
-   index.php → page d'accueil du site
-   deconnexion.php → fichier pour déconnexion des utilisateurs

**Gestion du code / Git**

Le projet a été développé sur mon environnement local sans branches Git
spécifiques, car je travaillais seule et toutes les modifications
étaient directement testées sur le code principal. J'ai compris les
bonnes pratiques Git et j'ai créer une branche secondaire pour la
gestion du code sur une branche de développement. Cependant, ayant vu
les cours Git à la fin de mon ECF, je n'avais pas intégré toutes les
informations à la première lecture et je me suis concentrée sur le code
lui-même avant de penser aux pratiques Git demandées. Par conséquent,
rien n'a été développé via GitHub et l'usage de Git s'est limité à
l'export final du projet.

Informations complémentaires

Le site utilise HTML, CSS, JavaScript et PHP pour la gestion dynamique
et la connexion à la base de données. La version exacte de PHP dépend de
votre WampServer. Les fonctionnalités sont conçues pour fonctionner en
local et certaines fonctionnalités avancées, comme le paiement en ligne
sécurisé, ne sont pas connectées.

Auteurs / Contact

-   Alexandra Riviere
