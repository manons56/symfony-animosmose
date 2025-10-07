// Attendre que tout le DOM soit chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', () => {
    // Récupère l'image principale (celle affichée en grand)
    const mainImage = document.getElementById('main-product-image');

    // Récupère toutes les miniatures (les images cliquables en dessous)
    const thumbnails = document.querySelectorAll('.thumbnail');

    // Récupère les boutons de navigation gauche et droite (flèches)
    const prevBtn = document.getElementById('prev-image');
    const nextBtn = document.getElementById('next-image');

    // Index de l’image actuellement affichée
    let currentIndex = 0;

    /**
     * Met à jour l'image principale avec celle correspondant à l'index donné
     * @param {number} index - L'index de la miniature sélectionnée
     */
    function updateMainImage(index) {
        currentIndex = index;

        const thumbnail = thumbnails[index]; // Récupère la miniature à cet index
        if (!thumbnail) return; // Si elle n'existe pas, on sort

        // Met à jour le src de l'image principale avec celui de la miniature
        mainImage.src = thumbnail.src;

        // Retire la classe 'active' de toutes les miniatures
        thumbnails.forEach(t => t.classList.remove('active'));

        // Ajoute la classe 'active' à la miniature actuellement sélectionnée
        thumbnail.classList.add('active');
    }

    // Si on a au moins une miniature, on initialise l’image principale avec la première
    if (thumbnails.length > 0) {
        updateMainImage(0);
    }

    /**
     * Gère le clic sur une miniature : met à jour l'image principale
     */
    thumbnails.forEach((thumb, i) => {
        thumb.addEventListener('click', () => {
            updateMainImage(i);
        });
    });

    /**
     * Gère le clic sur le bouton "précédent"
     * Affiche l’image précédente dans le carrousel (ou la dernière si on est au début)
     */
    prevBtn?.addEventListener('click', () => {
        if (thumbnails.length === 0) return;

        let newIndex = currentIndex - 1;
        if (newIndex < 0) newIndex = thumbnails.length - 1;

        updateMainImage(newIndex);
    });

    /**
     * Gère le clic sur le bouton "suivant"
     * Affiche l’image suivante dans le carrousel (ou la première si on est à la fin)
     */
    nextBtn?.addEventListener('click', () => {
        if (thumbnails.length === 0) return;

        let newIndex = currentIndex + 1;
        if (newIndex >= thumbnails.length) newIndex = 0;

        updateMainImage(newIndex);
    });
});
