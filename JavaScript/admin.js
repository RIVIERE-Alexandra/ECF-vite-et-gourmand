document.addEventListener('DOMContentLoaded', () => {

    /*=======================
  AJOUT D'UN MENU AU CLIC
    ADMIN DASHBOARD 
=======================*/

    // Afficher ou masquer l'ajout de menu au clic sur le bouton d'ajout

    const btnAjouterMenu = document.querySelector('.btn-ajout-menu');
    const formulaireMenu = document.querySelector('.formulaires-dashboard-cacher');


    if (formulaireMenu) {
        formulaireMenu.style.display = 'none';
    }
    if (btnAjouterMenu) {
        btnAjouterMenu.addEventListener('click', () => {
            if (formulaireMenu.style.display === 'none') {
                formulaireMenu.style.display = 'flex';
            } else {
                formulaireMenu.style.display = 'none';
            }
        });
    }

    /*=======================
      AJOUT D'UN EMPLOYÉ AU CLIC
        ADMIN DASHBOARD 
    =======================*/
    const btnAjouterEmploye = document.querySelector('.btn-ajout-employe');
    const formulaireEmploye = document.querySelector('.formulaires-dashboard-employe-cacher');


    if (btnAjouterEmploye) {
        formulaireEmploye.style.display = 'none';
    }
    if (btnAjouterEmploye) {
        btnAjouterEmploye.addEventListener('click', () => {
            if (formulaireEmploye.style.display === 'none') {
                formulaireEmploye.style.display = 'flex';
            } else {
                formulaireEmploye.style.display = 'none';
            }
        });
    }


    /*=======================
      STATS ADMIN DASHBOARD 
    =======================*/

    /*diagramme fictif pour l'année precedente */

    // Exemple de chiffres mensuels (en euros)
    const ventesPotentielles = [15000, 18000, 12000, 16000, 14000, 19000, 20000, 17000, 16000, 15000, 18000, 20000];

    // Canvas
    const canvas = document.getElementById('statistiques-annuelles');
    let ctx = null;
    if (canvas) {
        ctx = canvas.getContext('2d');
    }

    function dessinerDiagramme(data) {
        const largeur = canvas.width;
        const hauteur = canvas.height;
        const padding = 50;

        // Effacer le canvas
        ctx.clearRect(0, 0, largeur, hauteur);

        // Dessiner axes
        ctx.beginPath();
        ctx.moveTo(padding, padding);
        ctx.lineTo(padding, hauteur - padding);
        ctx.lineTo(largeur - padding, hauteur - padding);
        ctx.strokeStyle = "#000000";
        ctx.lineWidth = 1;
        ctx.stroke();

        // Valeurs max pour le ratio
        const maxVente = Math.max(...data);

        // Largeur d’une barre
        const barreWidth = (largeur - 2 * padding) / data.length - 10;

        // Dessiner les barres
        data.forEach((valeur, i) => {
            const x = padding + i * (barreWidth + 10) + 5;
            const y = hauteur - padding;
            const h = (valeur / maxVente) * (hauteur - 2 * padding);

            ctx.fillStyle = "#660000";
            ctx.fillRect(x, y - h, barreWidth, h);

            // Label du mois
            ctx.fillStyle = "#000";
            ctx.font = "12px Arial";
            const mois = ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Aoû", "Sep", "Oct", "Nov", "Déc"];
            ctx.fillText(mois[i], x, y + 15);

            // Valeur en haut de la barre
            ctx.fillText(valeur, x, y - h - 5);
        });
    }


    if (canvas) {

        dessinerDiagramme(ventesPotentielles);
    }

    // Initialisation



    // Partie back end pour le calcul des statistiques et l'affichage des graphiques


    /* afficher ou masquer l'ajout de statistiques au clic sur le bouton d'ajout */

    // Récupération des éléments
    const btnAjouterStats = document.querySelector('.btn-ajouter-stats');
    const formulaireStats = document.getElementById('formulaire-stats');

    if (canvas) {
        const ctx = canvas.getContext('2d');
        dessinerDiagramme(ventesPotentielles);
    }
    if (formulaireStats) {
        formulaireStats.style.display = 'none';
    }

    if (btnAjouterStats) {
        // Toggle affichage du formulaire au clic
        btnAjouterStats.addEventListener('click', () => {
            if (formulaireStats.style.display === 'none') {
                formulaireStats.style.display = 'flex';
            } else {
                formulaireStats.style.display = 'none';
            }
        });
    }

    if (formulaireStats) {
        // Récupérer les valeurs du formulaire à la soumission
        formulaireStats.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(formulaireStats);
            const chiffresMensuels = Array.from(formData.values()).map(Number);

            console.log("Chiffres mensuels :", chiffresMensuels);

            // Ici plus tard : appeler la fonction pour mettre à jour le diagramme
        });

    }

});