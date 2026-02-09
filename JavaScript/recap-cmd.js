    /*=======================
        GESTION DATE J+3 
    =======================*/
    /* pour bloquer si tentative de commande avant la date minimale */
document.addEventListener('DOMContentLoaded', () => {
    // Récupération du champ date
    const inputDate = document.getElementById("date-livraison");

    if (inputDate) {

        // Création du message d'erreur
        const messageErreur = document.createElement("p");
        messageErreur.classList.add("msg-erreur-date");
        messageErreur.textContent = "Il n'est pas possible de commander à cette date.";
        messageErreur.style.display = "none";
        messageErreur.style.width = "100%";
        messageErreur.style.marginTop = "5px";

        inputDate.insertAdjacentElement("afterend", messageErreur);

        // Calcul date minimum J+3
        const today = new Date();
        today.setDate(today.getDate() + 3);

        // Format YYYY-MM-DD
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, "0");
        const day = String(today.getDate()).padStart(2, "0");
        const dateMinimum = `${year}-${month}-${day}`;

        // Définition min et valeur par défaut
        inputDate.min = dateMinimum;
        if (!inputDate.value || new Date(inputDate.value) < new Date(dateMinimum)) {
            inputDate.value = dateMinimum;
        }

        let hideTimer = null;
        let removeShakeTimer = null;

        inputDate.addEventListener("input", function () {
            const chosenDate = new Date(inputDate.value + "T00:00:00");
            const minDateObj = new Date(dateMinimum + "T00:00:00");

            if (chosenDate < minDateObj) {
                // Affiche message d'erreur
                messageErreur.style.display = "block";
                messageErreur.classList.remove("fade-out");

                // Shake
                inputDate.classList.add("shake-erreur");
                clearTimeout(removeShakeTimer);
                removeShakeTimer = setTimeout(() => {
                    inputDate.classList.remove("shake-erreur");
                }, 500);

                // Remet à la date minimum
                inputDate.value = dateMinimum;

                // Masquer le message après 3 sec
                clearTimeout(hideTimer);
                hideTimer = setTimeout(() => {
                    messageErreur.classList.add("fade-out");
                    setTimeout(() => {
                        messageErreur.style.display = "none";
                        messageErreur.classList.remove("fade-out");
                    }, 600);
                }, 3000);
            } else {
                // Masquer message si tout est ok
                messageErreur.classList.add("fade-out");
                clearTimeout(hideTimer);
                setTimeout(() => {
                    messageErreur.style.display = "none";
                    messageErreur.classList.remove("fade-out");
                }, 600);
            }
        });
    }
    const villeInput = document.getElementById('ville');
            const fraisLivraisonTd = document.getElementById('frais-livraison');
            const sousTotalTd = document.getElementById('sous-total');
            const totalTd = document.getElementById('total');

            const sousTotal = parseFloat(sousTotalTd.textContent.replace(',', '.'));

            villeInput.addEventListener('input', () => {
                let fraisLivraison = 5.00;
                if (villeInput.value.trim().toLowerCase() === 'bordeaux') {
                    fraisLivraison = 0.00;
                }

                fraisLivraisonTd.textContent = fraisLivraison.toFixed(2).replace('.', ',');
                totalTd.textContent = (sousTotal + fraisLivraison).toFixed(2).replace('.', ',');
            });

            const dateInput = document.getElementById('date-livraison');
            const heureInput = document.getElementById('heure-livraison');
            const rueInput = document.getElementById('rue');
            const cpInput = document.getElementById('code-postal');

            const hiddenDate = document.getElementById('hidden-date');
            const hiddenHeure = document.getElementById('hidden-heure');
            const hiddenVille = document.getElementById('hidden-ville');
            const hiddenRue = document.getElementById('hidden-rue');
            const hiddenCp = document.getElementById('hidden-cp');

            const formValidation = document.getElementById('form-validation');

            dateInput.addEventListener('change', () => {
                hiddenDate.value = dateInput.value;
            });

            heureInput.addEventListener('change', () => {
                hiddenHeure.value = heureInput.value;
            });

            villeInput.addEventListener('change', () => {
                hiddenVille.value = villeInput.value.trim();
            });
            rueInput.addEventListener('change', () => {
                hiddenRue.value = rueInput.value.trim();
            });
            cpInput.addEventListener('change', () => {
                hiddenCp.value = cpInput.value.trim();
            });

            formValidation.addEventListener('submit', (e) => {
                const dateVal = dateInput.value.trim();
                const heureVal = heureInput.value.trim();

                if (!dateVal || !heureVal || heureVal === "00:00") {
                    e.preventDefault();
                    alert('Veuillez sélectionner une date valide et une heure différente de 00:00.');

                    if (!dateVal) dateInput.style.border = '2px solid red';
                    if (!heureVal || heureVal === "00:00") heureInput.style.border = '2px solid red';
                    return;
                }

                hiddenDate.value = dateVal;
                hiddenHeure.value = heureVal;
                hiddenVille.value = villeInput.value.trim();
                hiddenRue.value = rueInput.value.trim();
                hiddenCp.value = cpInput.value.trim();
            });
    
});
