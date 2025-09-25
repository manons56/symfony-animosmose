document.addEventListener('DOMContentLoaded', () => {
    // Attendre que tout le DOM soit chargé avant d'exécuter le script

    const searchInput = document.getElementById('search');
    // Récupérer le champ de recherche (input) par son ID "search"

    const productList = document.getElementById('product-list');
    // Récupérer le conteneur qui contient tous les produits

    const allProducts = Array.from(productList.querySelectorAll('.product-card'));
    // Récupérer tous les éléments produits avec la classe "product-card"
    // Convertir la NodeList en tableau pour pouvoir utiliser les méthodes tableau (filter, slice...)

    const paginationControls = document.getElementById('pagination-controls');
    // Récupérer le conteneur où on mettra les boutons de pagination

    const productsPerPage = 16;
    // Nombre de produits à afficher par page

    let filteredProducts = [...allProducts]; // commence avec tous les produits
    // Tableau dynamique des produits filtrés (initialement tous les produits)

    let currentPage = 1;
    // Variable pour garder en mémoire la page actuellement affichée

    function showPage(page) {
        currentPage = page;
        // Met à jour la page courante

        // Masquer tous les produits (on cache d'abord tout)
        allProducts.forEach(p => p.style.display = 'none');

        // Calculer l'index de début et de fin des produits à afficher selon la page
        const start = (page - 1) * productsPerPage;
        const end = start + productsPerPage;

        // Afficher uniquement les produits filtrés qui appartiennent à la page demandée
        filteredProducts.slice(start, end).forEach(p => p.style.display = 'block');

        // Mettre à jour les boutons de pagination en fonction de la page actuelle
        renderPagination();
    }

    function renderPagination() {
        paginationControls.innerHTML = '';
        // Vider le conteneur des boutons pour les recréer à chaque fois

        const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
        // Calculer le nombre total de pages en fonction des produits filtrés

        if (totalPages <= 1) return;
        // Si il n'y a qu'une page ou aucune, on n'affiche pas de pagination

        // Créer un bouton "Précédent" si on n'est pas sur la première page
        if (currentPage > 1) {
            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Précédent';
            prevBtn.classList.add('pagination-link');
            prevBtn.addEventListener('click', () => showPage(currentPage - 1));
            paginationControls.appendChild(prevBtn);
        }

        // Créer un bouton pour chaque numéro de page
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.textContent = i;
            pageBtn.classList.add('pagination-link');
            if (i === currentPage) pageBtn.disabled = true; // désactiver le bouton de la page actuelle
            pageBtn.addEventListener('click', () => showPage(i));
            paginationControls.appendChild(pageBtn);
        }

        // Créer un bouton "Suivant" si on n'est pas sur la dernière page
        if (currentPage < totalPages) {
            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Suivant';
            nextBtn.classList.add('pagination-link');
            nextBtn.addEventListener('click', () => showPage(currentPage + 1));
            paginationControls.appendChild(nextBtn);
        }
    }

    // Quand on tape dans la barre de recherche
    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        // Récupérer la valeur tapée et la mettre en minuscules pour une recherche insensible à la casse

        // Filtrer les produits en gardant seulement ceux dont le nom contient la recherche
        filteredProducts = allProducts.filter(card => {
            const name = card.querySelector('.product-name').textContent.toLowerCase();
            return name.includes(query);
        });

        currentPage = 1; // Revenir automatiquement à la première page après chaque nouvelle recherche
        showPage(currentPage); // Afficher cette première page avec les produits filtrés
    });

    // Affiche la première page au chargement initial de la page
    showPage(currentPage);
});
