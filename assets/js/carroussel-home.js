document.addEventListener('DOMContentLoaded', () => {
    const track = document.querySelector('.carousel-track');
    const prevBtn = document.querySelector('.carousel-btn.prev');
    const nextBtn = document.querySelector('.carousel-btn.next');
    const items = Array.from(track.children);

    let index = 0;

    // Nombre d’items visibles
    function itemsPerView() {
        return window.innerWidth >= 768 ? 3 : 1; // desktop → 3, mobile → 1
    }

    // Met à jour le déplacement du carrousel
    function updateCarousel() {
        const perView = itemsPerView();
        track.style.transform = `translateX(-${(100 / perView) * index}%)`;
    }

    // Suivant
    nextBtn.addEventListener('click', () => {
        const perView = itemsPerView();
        if (index < items.length - perView) index++;
        updateCarousel();
    });

    // Précédent
    prevBtn.addEventListener('click', () => {
        if (index > 0) index--;
        updateCarousel();
    });

    //Réinitialisation lors d’un redimensionnement
    window.addEventListener('resize', () => {
        index = 0;
        updateCarousel();
    });

    // Initialisation
    updateCarousel();
});
