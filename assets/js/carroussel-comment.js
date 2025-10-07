// On attend que le DOM soit complètement chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', () => {

    // Sélectionne le conteneur "piste" du carrousel (celui qui contient tous les items)
    const track = document.querySelector('.new-carousel-track');

    // Sélectionne le bouton "précédent" du carrousel
    const prevBtn = document.querySelector('.new-carousel-btn.prev');

    // Sélectionne le bouton "suivant" du carrousel
    const nextBtn = document.querySelector('.new-carousel-btn.next');

    // Transforme les enfants de la piste (les items) en tableau pour pouvoir les manipuler facilement
    const items = Array.from(track.children);

    // Index de départ pour suivre quelle position de carrousel est affichée
    let index = 0;

    // Fonction qui retourne le nombre d'items visibles selon la largeur de l'écran
    function itemsPerView() {
        // Si l'écran est supérieur ou égal à 768px (desktop), on affiche 2 items
        // Sinon (mobile), on affiche 1 item
        return window.innerWidth >= 768 ? 2 : 1;
    }

    // Fonction qui met à jour la position de la piste en fonction de l'index actuel
    function updateCarousel() {
        const perView = itemsPerView(); // Récupère combien d'items sont visibles
        // Décale la piste vers la gauche pour montrer l'item correspondant à l'index
        track.style.transform = `translateX(-${(100 / perView) * index}%)`;
    }

    // Quand on clique sur le bouton "suivant"
    nextBtn.addEventListener('click', () => {
        const perView = itemsPerView(); // Récupère combien d'items sont visibles
        // Si on n'est pas encore au dernier bloc visible, on incrémente l'index
        if (index < items.length - perView) index++;
        // On met à jour le carrousel avec la nouvelle position
        updateCarousel();
    });

    // Quand on clique sur le bouton "précédent"
    prevBtn.addEventListener('click', () => {
        // Si on n'est pas au début, on décrémente l'index
        if (index > 0) index--;
        // On met à jour le carrousel avec la nouvelle position
        updateCarousel();
    });

    // Quand la fenêtre est redimensionnée
    window.addEventListener('resize', () => {
        index = 0;          // On remet le carrousel au début
        updateCarousel();   // On met à jour l'affichage pour correspondre à la nouvelle largeur
    });

    // Initialisation : on affiche le carrousel à la bonne position au chargement
    updateCarousel();
});
