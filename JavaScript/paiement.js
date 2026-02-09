document.addEventListener('DOMContentLoaded', () => {
    /* --- PAGE PAIEMENT --- */
    (() => {
        const form = document.querySelector('.formulaire-paiement');
        const titulaire = document.querySelector('#titulaire');
        const numeroCarte = document.querySelector('#numero-carte');
        const cvv = document.querySelector('#cvv');
        const expiration = document.querySelector('#expiration');
        const button = document.querySelector('.btn-payer');
        if (!form || !titulaire || !numeroCarte || !cvv || !expiration || !button) return;

        button.disabled = true;
        const checkForm = () => button.disabled = !(titulaire.value.trim() && numeroCarte.value.trim() && cvv.value.trim() && expiration.value.trim());
        [titulaire, numeroCarte, cvv, expiration].forEach(input => input.addEventListener('input', checkForm));

    })();
});