document.addEventListener('DOMContentLoaded', () => {

    /* =======================
       CONFIG / DEBUG
    ======================= */
    const debug = true;

    /* =======================
       FONCTIONS UTILITAIRES
    ======================= */

    function getSelectedOptions(menuId) {
        const types = ['entrée', 'plat', 'dessert'];
        const selectedOptions = {};

        types.forEach(type => {
            const radioName = `${type}_${menuId}`;
            const checkedRadio = document.querySelector(`input[name="${radioName}"]:checked`);
            selectedOptions[type] = checkedRadio ? checkedRadio.value : null;
        });

        return selectedOptions;
    }

    function attachDetailButtons() {
        document.querySelectorAll('.bouton-detail-menu').forEach(btn => {
            btn.addEventListener('click', () => {
                const detail = btn.closest('.menus-complet-type').querySelector('.detail-menu-caché');
                if (detail) {
                    detail.classList.toggle('visible');
                    btn.textContent = detail.classList.contains('visible')
                        ? 'Détail menu ▲'
                        : 'Détail menu ▼';
                }
            });
        });
    }

    function initCarrousels() {
        const blocsPhotos = document.querySelectorAll('.bloc-photos-menu');

        blocsPhotos.forEach(blocPhotos => {
            if (blocPhotos.dataset.init) return;
            blocPhotos.dataset.init = true;

            const photosOriginales = Array.from(blocPhotos.children);

            photosOriginales.forEach(photo => {
                const clone = photo.cloneNode(true);
                blocPhotos.appendChild(clone);
            });

            let isPaused = false;
            const speed = 0.3;
            let posX = 0;

            function defilementContinu() {
                if (!isPaused) {
                    posX += speed;
                    const totalWidth = blocPhotos.scrollWidth / 2;
                    if (posX >= totalWidth) posX = 0;
                    blocPhotos.style.transform = `translateX(-${posX}px)`;
                }
                requestAnimationFrame(defilementContinu);
            }
            requestAnimationFrame(defilementContinu);

            const flecheGauche = blocPhotos.parentElement.querySelector('.fleche-defilement-gauche');
            const flecheDroite = blocPhotos.parentElement.querySelector('.fleche-defilement-droite');

            if (flecheDroite) flecheDroite.addEventListener('click', () => posX += 300);
            if (flecheGauche) {
                flecheGauche.addEventListener('click', () => {
                    posX -= 300;
                    if (posX < 0) posX += blocPhotos.scrollWidth / 2;
                });
            }

            blocPhotos.addEventListener('mouseenter', () => isPaused = true);
            blocPhotos.addEventListener('mouseleave', () => isPaused = false);
        });
    }

    function debounce(fn, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    /* =======================
       VALIDATION MENU (COMMANDER)
    ======================= */

    document.querySelectorAll('.btn-commander').forEach(btn => {
        btn.addEventListener('click', e => {
            const form = btn.closest('form');
            const menuIdInput = form.querySelector('input[name="id_menus"]');
            if (!menuIdInput) return;

            const menuId = menuIdInput.value;
            const selected = getSelectedOptions(menuId);

            if (debug) console.log('Choix avant envoi:', selected);

            if (Object.values(selected).some(v => v === null)) {
                e.preventDefault();
                alert('Veuillez sélectionner toutes les options (entrée, plat, dessert).');
                return;
            }

            form.querySelector('input[name="selected_entree"]').value = selected.entrée;
            form.querySelector('input[name="selected_plat"]').value = selected.plat;
            form.querySelector('input[name="selected_dessert"]').value = selected.dessert;
        });
    });

    /* =======================
       AJUSTEMENT QUANTITÉ MENUS
    ======================= */

    (() => {
        document.querySelectorAll('.ajustement-quantite-menus').forEach(container => {
            const btnAdd = container.querySelector('.btn-ajouter');
            const btnRemove = container.querySelector('.btn-retirer');
            const output = container.querySelector('.quantite');
            const inputHidden = container.querySelector('.input-quantite');
            const stock = parseInt(container.dataset.stock, 10);
            if (!btnAdd || !btnRemove || !output) return;

            const MIN = 1, MAX = stock;
            let current = parseInt(output.textContent, 10) || MIN;

            output.textContent = current;
            inputHidden.value = current;

            btnAdd.addEventListener('click', () => {
                if (current < MAX) {
                    current++;
                    output.textContent = current;
                    inputHidden.value = current;
                }
            });

            btnRemove.addEventListener('click', () => {
                if (current > MIN) {
                    current--;
                    output.textContent = current;
                    inputHidden.value = current;
                }
            });
        });
    })();

    /* =======================
       POPUP MATERIEL
    ======================= */

    (() => {
        const infoMateriel = document.getElementById('infoMateriel');
        if (!infoMateriel) return;

        infoMateriel.addEventListener('click', event => {
            event.stopPropagation();

            const existingPopup = document.querySelector('.popup-materiel');
            if (existingPopup) {
                existingPopup.remove();
                document.body.style.overflow = "";
                return;
            }

            const popup = document.createElement('div');
            popup.classList.add('popup-materiel');
            popup.setAttribute("tabindex", "-1");
            popup.innerHTML = `
                <button class="popup-fermer" aria-label="Fermer la popup">✖</button>
                <p>
                    • Le matériel doit être restitué sous 10 jours ouvrés.<br>
                    • En cas de non-restitution, des frais peuvent être appliqués.<br>
                    • Contactez l’entreprise directement pour organiser le retour.
                </p>
            `;
            document.body.appendChild(popup);
            document.body.style.overflow = "hidden";

            const rect = infoMateriel.getBoundingClientRect();
            const popupHeight = popup.offsetHeight;
            const popupWidth = popup.offsetWidth;
            const margin = 5;

            let topPosition = rect.bottom + window.scrollY + margin;
            if (topPosition + popupHeight > window.scrollY + window.innerHeight) {
                topPosition = rect.top + window.scrollY - popupHeight - margin;
            }

            let leftPosition = rect.left + window.scrollX;
            if (leftPosition + popupWidth > window.innerWidth) {
                leftPosition = window.innerWidth - popupWidth - margin;
            }

            popup.style.top = `${topPosition}px`;
            popup.style.left = `${leftPosition}px`;

            setTimeout(() => popup.classList.add('show'), 10);
            popup.focus();

            popup.querySelector('.popup-fermer').addEventListener('click', () => {
                popup.remove();
                document.body.style.overflow = "";
            });
        });

        document.addEventListener('click', () => {
            const popup = document.querySelector('.popup-materiel');
            if (popup) {
                popup.remove();
                document.body.style.overflow = "";
            }
        });

        document.body.addEventListener('click', event => {
            const popup = document.querySelector('.popup-materiel');
            if (popup && popup.contains(event.target)) event.stopPropagation();
        });
    })();

    /* =======================
       FILTRAGE MENUS AJAX
    ======================= */

    const prixRange = document.getElementById('fourchette-prix');
    const nbPersonnes = document.getElementById('nb-personnes');
    const prixValeur = document.getElementById('prix-valeur');

    function filtrerMenus() {
        const data = new FormData();

        const triPrix = document.querySelector('input[name="triPrix"]:checked');
        if (triPrix) data.append('triPrix', triPrix.value);

        const tritheme = document.querySelector('input[name="theme"]:checked');
        if (tritheme) data.append('theme', tritheme.value);

        const triregime = document.querySelector('input[name="regime"]:checked');
        if (triregime) data.append('regime', triregime.value);

        if (prixRange) {
            data.append('prixMax', prixRange.value);
            prixValeur.textContent = prixRange.value;
        }

        if (nbPersonnes) data.append('nbPersonnes', nbPersonnes.value);

        fetch('filtrer_menus.php', { method: 'POST', body: data })
            .then(res => res.text())
            .then(html => {
                document.getElementById('types-menus').innerHTML = html;
                attachDetailButtons();
                initCarrousels();
            });
    }

    const filtrerMenusDebounced = debounce(filtrerMenus, 300);

    if (prixRange) prixRange.addEventListener('input', filtrerMenusDebounced);
    if (nbPersonnes) nbPersonnes.addEventListener('input', filtrerMenusDebounced);

    /* =======================
       FILTRES UI
    ======================= */

    const btnAfficherFiltres = document.querySelector('.btn-afficher-filtres');
    const sectionFiltres = document.querySelector('.filtres');

    if (btnAfficherFiltres && sectionFiltres) {
        btnAfficherFiltres.addEventListener('click', () => {
            sectionFiltres.classList.toggle('active');
            btnAfficherFiltres.textContent = sectionFiltres.classList.contains('active')
                ? 'Filtres ▲'
                : 'Filtres ▼';
        });

        document.querySelectorAll('.btn-filtres').forEach(btn => {
            btn.addEventListener('click', () => {
                const fieldset = btn.nextElementSibling;
                if (!fieldset) return;

                fieldset.classList.toggle('active');
                document.querySelectorAll('.btn-filtres').forEach(other => {
                    if (other.nextElementSibling !== fieldset) {
                        other.nextElementSibling.classList.remove('active');
                    }
                });
            });
        });
    }

    ['triPrix', 'regime', 'theme'].forEach(groupName => {
        const checkboxes = document.querySelectorAll(`input[name="${groupName}"]`);
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                // décocher les autres
                if (checkbox.checked) {
                    checkboxes.forEach(other => {
                        if (other !== checkbox) other.checked = false;
                    });
                }

                // filtrer menus après mise à jour des cases
                filtrerMenus();
            });
        });
    });






    /* =======================
       INIT AU CHARGEMENT
    ======================= */

    attachDetailButtons();
    initCarrousels();

});