document.addEventListener('DOMContentLoaded', () => {
const btnModif = document.querySelector('.btn-modif-infos-user');
        const modeEdition = document.querySelector('.carte-mode-edition');

        btnModif.addEventListener('click', function() {
            if (modeEdition.style.display === 'flex') {
                modeEdition.style.display = 'none';
            } else {
                modeEdition.style.display = 'flex';
            }
        });
});