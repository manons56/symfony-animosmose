// On attend que le DOM soit complètement chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', () => {

    // Sélectionne tous les boutons qui ouvrent des modales
    const openModalButtons = document.querySelectorAll('[data-modal-target]');

    // Sélectionne tous les boutons qui ferment des modales
    const closeModalButtons = document.querySelectorAll('[data-close-button]');

    // Sélectionne l'overlay (le fond semi-transparent derrière la modale)
    const overlay = document.getElementById('overlay');

    // Sélectionne le conteneur principal du contenu, pour appliquer l'effet flou
    const contentWrapper = document.querySelector('.content-wrapper');

    // Ajoute un événement "click" à chaque bouton d'ouverture de modale
    openModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Récupère la modale ciblée par l'attribut data-modal-target
            const modal = document.querySelector(button.dataset.modalTarget);
            if (modal) {
                openModal(modal); // ouvre la modale
            }
        });
    });

    // Ajoute un événement "click" à chaque bouton de fermeture de modale
    closeModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Trouve la modale parente la plus proche du bouton
            const modal = button.closest('.modal');
            if (modal) {
                closeModal(modal); // ferme la modale
            }
        });
    });

    // Ferme toutes les modales si on clique sur l'overlay
    overlay.addEventListener('click', () => {
        // Sélectionne toutes les modales actives
        const modals = document.querySelectorAll('.modal.active');
        modals.forEach(modal => {
            closeModal(modal); // ferme chaque modale
        });
    });

    // Fonction pour ouvrir une modale
    function openModal(modal) {
        modal.classList.add('active');          // rend la modale visible
        overlay.classList.add('active');        // affiche l'overlay
        contentWrapper.classList.add('blurred');// floute le contenu derrière
    }

    // Fonction pour fermer une modale
    function closeModal(modal) {
        modal.classList.remove('active');          // cache la modale
        overlay.classList.remove('active');        // cache l'overlay
        contentWrapper.classList.remove('blurred');// enlève le flou du contenu
    }

});
