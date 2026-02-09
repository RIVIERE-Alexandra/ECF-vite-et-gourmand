document.addEventListener('DOMContentLoaded', () => {
    /* --- PAGE AVIS UTILISATEUR --- */

    const form = document.querySelector('.form-avis');
    if (!form) return;
    const nomAvis = document.querySelector('.nom-avis');
    const avis = document.querySelector('#avis');
    const radios = document.querySelectorAll('.notation .radio');
    const stars = document.querySelectorAll('.notation label');
    const button = document.querySelector('.btn-avis');
    if (!nomAvis || !avis || !button) return;

    let noteSelectionnee = 0;
    const updateStars = (note) => {
        stars.forEach(star => {
            const value = star.getAttribute('for').replace('star', '');
            star.style.color = (value <= note) ? 'var(--couleur-secondaire)' : 'var(--couleur-primaire)';
        });
    };
    stars.forEach(star => {
        star.addEventListener('mouseenter', () => updateStars(star.getAttribute('for').replace('star', '')));
        star.addEventListener('mouseleave', () => updateStars(noteSelectionnee));
        star.addEventListener('click', () => { noteSelectionnee = star.getAttribute('for').replace('star', ''); updateStars(noteSelectionnee); });
    });
    radios.forEach(radio => radio.addEventListener('change', () => { noteSelectionnee = radio.value; updateStars(noteSelectionnee); }));
    form.addEventListener('submit', e => {
        if (!document.querySelector('.radio:checked')) { e.preventDefault(); alert("Merci de selectionner une note."); return; }
        if (!nomAvis.value.trim() || nomAvis.value.trim().length < 2) { e.preventDefault(); alert("Merci d’indiquer votre nom et prenom."); return; }
        if (!avis.value.trim() || avis.value.trim().length < 10) { e.preventDefault(); alert("Merci d’écrire votre avis détaillé."); return; }
    });

    // Partie reliée au back end pour la soumission de l'avis

});