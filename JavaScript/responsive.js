/* =======================
    MENU BURGER + RESPONSIVE
    ======================= */
document.addEventListener("DOMContentLoaded", () => {
    (() => {
        const burger = document.querySelector('.burger');
        const menu = document.querySelector('.menu-principal');
        if (!burger || !menu) return;

        // Ouverture / fermeture au clic
        burger.addEventListener('click', () => {
            menu.classList.toggle('menu-ouvert');
            burger.classList.toggle('ouvert');
        });

        // Fermeture automatique si la fenêtre est agrandie
        window.addEventListener('resize', () => {
            if (window.innerWidth > 900) {
                menu.classList.remove('menu-ouvert');
                burger.classList.remove('ouvert');
            }
        });
    })();
});