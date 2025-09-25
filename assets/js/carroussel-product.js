document.addEventListener('DOMContentLoaded', () => {
    // Sélectionne la "piste" du carrousel (le conteneur qui contient les cartes)
    const track = document.querySelector('.carousel-track');

    // Vérifie qu'il y a bien un carrousel sur la page avant de continuer
    if (track) {

        // Récupère toutes les cartes du carrousel sous forme de tableau
        const items = Array.from(track.children);

        // Pour chaque carte, on crée une copie identique et on l'ajoute à la suite
        // Cela permet d'avoir deux séries de produits à la suite,
        // créant un effet de "boucle infinie" sans coupure visible
        items.forEach(item => {
            track.appendChild(item.cloneNode(true)); // cloneNode(true) => clone toute la carte avec son contenu
        });
    }
});

