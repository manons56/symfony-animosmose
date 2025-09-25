document.addEventListener('DOMContentLoaded', () => {
    // Sélection de la piste (container) qui contient tous les éléments du carrousel
    const track = document.querySelector('.carousel-track');
    // Tableau contenant tous les éléments du carrousel
    const items = Array.from(document.querySelectorAll('.carousel-item'));
    // Boutons de navigation suivant et précédent
    const nextBtn = document.querySelector('.carousel-btn.next');
    const prevBtn = document.querySelector('.carousel-btn.prev');

    // Initialisation de l'index courant :
    // Sur mobile (<=768px), on commence par le premier item (index 0)
    // Sur desktop, on commence à l'item 2 (index 1) pour centrer un peu plus
    let currentIndex = window.innerWidth <= 768 ? 0 : 1;

    // Fonction qui met à jour la position du carrousel et l'état des items
    const updateCarousel = () => {
        // Détection si l'affichage est sur mobile (largeur écran <= 768px)
        const isMobile = window.innerWidth <= 768;

        // Largeur d'un item + marge entre les items (32px) sur desktop
        // Sur mobile, on prend juste la largeur de l'item, car il occupe 100% de la zone visible
        const itemWidth = isMobile ? items[0].offsetWidth : items[0].offsetWidth + 32;

        // Calcul du décalage horizontal pour centrer l'item actif dans la piste
        // On translate la piste vers la gauche de currentIndex fois la largeur d'un item
        // Puis on ajuste avec un offset pour centrer l'élément dans le conteneur visible
        const offset = -currentIndex * itemWidth + (track.offsetWidth - itemWidth) / 2;

        // Application de la transformation CSS pour faire défiler le carrousel
        track.style.transform = `translateX(${offset}px)`;

        // Mise à jour de la classe 'active' sur chaque item, pour agrandir l'item sélectionné
        items.forEach((item, index) => {
            item.classList.toggle('active', index === currentIndex);
        });
    };

    // Gestion du clic sur le bouton "suivant"
    nextBtn.addEventListener('click', () => {
        // On incrémente currentIndex si on n'est pas déjà sur le dernier item
        if (currentIndex < items.length - 1) {
            currentIndex++;
            updateCarousel(); // On met à jour l'affichage après le changement
        }
    });

    // Gestion du clic sur le bouton "précédent"
    prevBtn.addEventListener('click', () => {
        // On décrémente currentIndex si on n'est pas déjà sur le premier item
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel(); // Mise à jour de l'affichage
        }
    });

    // Gestion du redimensionnement de la fenêtre (responsive)
    // À chaque changement de taille, on recalculera la position et la taille des items
    window.addEventListener('resize', updateCarousel);

    // Première mise à jour du carrousel au chargement de la page
    updateCarousel();
});
