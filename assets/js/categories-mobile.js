
document.addEventListener('DOMContentLoaded', () => {
    // Sélectionne tous les liens (<a>) situés dans des éléments <li> dans le menu de catégorie
    document.querySelectorAll('.category-menu > ul > li > a').forEach(link => {

        // Ajoute un écouteur d'événement 'click' à chaque lien
        link.addEventListener('click', function(e) {

            // Récupère l'élément <li> parent de ce lien
            const li = this.parentElement;

            // Recherche un sous-menu (.subcategory-list) à l'intérieur de ce <li>
            const submenu = li.querySelector('.subcategory-list');

            // S'il y a un sous-menu
            if (submenu) {
                // Empêche le comportement par défaut du lien (par exemple, navigation)
                e.preventDefault();

                // Si le sous-menu est déjà visible
                if (submenu.classList.contains('visible')) {
                    // On le masque en retirant la classe 'visible'
                    submenu.classList.remove('visible');
                    return; // Arrête l'exécution ici
                }

                // Si un autre sous-menu est ouvert, on le ferme
                document.querySelectorAll('.subcategory-list.visible').forEach(openMenu => {
                    openMenu.classList.remove('visible');
                });

                // Enfin, on affiche le sous-menu en ajoutant la classe 'visible'
                submenu.classList.add('visible');
            }
        });
    });

});
