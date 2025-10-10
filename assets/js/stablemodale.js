// On attend que le DOM soit complètement chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', () => {

    // -----------------------------
    // Sélections des éléments DOM
    // -----------------------------

    // Sélectionne tous les boutons qui ouvrent des modales grâce à l'attribut data-modal-target
    const openModalButtons = document.querySelectorAll('[data-modal-target]');

    // Sélectionne tous les boutons qui ferment des modales grâce à l'attribut data-close-button
    const closeModalButtons = document.querySelectorAll('[data-close-button]');

    // Sélectionne l'overlay (le fond semi-transparent derrière la modale)
    const overlay = document.getElementById('overlay');

    // Sélectionne le conteneur principal du contenu pour appliquer l'effet de flou
    const contentWrapper = document.querySelector('.content-wrapper');

    // Stocke la modale actuellement ouverte pour faciliter la fermeture
    let activeModal = null;


    // -----------------------------
    // Fonctions pour ouvrir et fermer les modales
    // -----------------------------

    // Fonction pour ouvrir une modale
    function openModal(modal, triggerButton = null) {
        if (!modal) return;

        modal.classList.add('active');
        overlay.classList.add('active');
        contentWrapper.classList.add('blurred');

        modal.setAttribute('tabindex', '-1');
        modal.focus();

        activeModal = modal;
        modal.triggerButton = triggerButton;

        // écoute keydown pour focus trap et Escape
        modal._keydownHandler = function(e) {
            const focusableElements = modal.querySelectorAll(
                'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])'
            );
            const firstEl = focusableElements[0];
            const lastEl = focusableElements[focusableElements.length - 1];

            if (e.key === 'Tab') {
                if (e.shiftKey) { // Shift + Tab
                    if (document.activeElement === firstEl) {
                        e.preventDefault();
                        lastEl.focus();
                    }
                } else { // Tab
                    if (document.activeElement === lastEl) {
                        e.preventDefault();
                        firstEl.focus();
                    }
                }
            } else if (e.key === 'Escape') {
                closeModal(modal);
            }
        };

        modal.addEventListener('keydown', modal._keydownHandler);
    }

    function closeModal(modal) {
        if (!modal) return;

        modal.classList.remove('active');
        overlay.classList.remove('active');
        contentWrapper.classList.remove('blurred');

        activeModal = null;

        if (modal.triggerButton) {
            modal.triggerButton.focus();
        }

        // Supprime l'écoute keydown pour ne pas bloquer le focus après fermeture
        if (modal._keydownHandler) {
            modal.removeEventListener('keydown', modal._keydownHandler);
            delete modal._keydownHandler;
        }
    }



    // -----------------------------
    // Focus trap pour accessibilité
    // -----------------------------

    // Cette fonction empêche le focus de sortir de la modale lorsqu'on utilise TAB ou Shift+TAB
    function trapFocus(modal) {
        // Sélectionne tous les éléments focusables dans la modale
        const focusableElements = modal.querySelectorAll(
            'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])'
        );

        const firstEl = focusableElements[0];                       // premier élément focusable
        const lastEl = focusableElements[focusableElements.length - 1]; // dernier élément focusable

        // Écoute les touches pressées dans la modale
        modal.addEventListener('keydown', function(e) {
            // Si la touche pressée est Tab
            if (e.key === 'Tab') {
                if (e.shiftKey) { // Shift + Tab (tabulation inverse)
                    if (document.activeElement === firstEl) {
                        e.preventDefault(); // empêche de sortir de la modale
                        lastEl.focus();     // met le focus sur le dernier élément
                    }
                } else { // Tab (normal)
                    if (document.activeElement === lastEl) {
                        e.preventDefault(); // empêche de sortir de la modale
                        firstEl.focus();    // met le focus sur le premier élément
                    }
                }
            }
            // Si la touche pressée est Escape, on ferme la modale
            else if (e.key === 'Escape') {
                closeModal(modal);
            }
        });
    }


    // -----------------------------
    // Événements sur les boutons
    // -----------------------------

    // Boutons pour ouvrir les modales
    openModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            const modal = document.querySelector(button.dataset.modalTarget); // récupère la modale ciblée
            openModal(modal, button); // ouvre la modale
        });
    });

    // Boutons pour fermer les modales
    closeModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            const modal = button.closest('.modal'); // récupère la modale parente la plus proche
            closeModal(modal); // ferme la modale
        });
    });

    // Clique sur l'overlay ferme la modale active
    overlay.addEventListener('click', () => {
        if (activeModal) {
            closeModal(activeModal);
        }
    });

});
