document.addEventListener('DOMContentLoaded', () => {
    const mainImage = document.getElementById('main-product-image');
    const thumbnails = document.querySelectorAll('.thumbnail');
    const prevBtn = document.getElementById('prev-image');
    const nextBtn = document.getElementById('next-image');

    let currentIndex = 0;

    // Mettre à jour l'image principale et la miniature active
    function updateMainImage(index) {
        currentIndex = index;
        mainImage.src = thumbnails[index].src;

        thumbnails.forEach(t => t.classList.remove('active'));
        thumbnails[index].classList.add('active');
    }

    // Initialisation
    if (thumbnails.length > 0) updateMainImage(0);

    // Click sur miniatures
    thumbnails.forEach((thumb, i) => {
        thumb.addEventListener('click', () => updateMainImage(i));
    });

    // Flèches
    prevBtn.addEventListener('click', () => {
        let newIndex = currentIndex - 1;
        if (newIndex < 0) newIndex = thumbnails.length - 1;
        updateMainImage(newIndex);
    });

    nextBtn.addEventListener('click', () => {
        let newIndex = currentIndex + 1;
        if (newIndex >= thumbnails.length) newIndex = 0;
        updateMainImage(newIndex);
    });
});
