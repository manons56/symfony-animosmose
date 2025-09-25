// Attend que toute la page (DOM) soit entièrement chargée avant d'exécuter le script
document.addEventListener('DOMContentLoaded', function () {

    console.log('hello!');
    // On récupère le bouton (icône user) qui servira à afficher/masquer le menu
    const toggleButton = document.getElementById('user-menu-toggle');

    // On récupère le menu déroulant (le bloc "Se déconnecter")
    const dropdownMenu = document.getElementById('user-menu');

    // On vérifie que les deux éléments existent dans le HTML avant de continuer
    if (toggleButton && dropdownMenu) {

        // Quand on clique sur l'icône user
        toggleButton.addEventListener('click', function (e) {
            e.preventDefault(); //  Empêche un comportement par défaut (ex: rechargement d'une page ou d'un lien)

            //  Alterne l'affichage du menu :
            // Si la classe 'visible' est présente → on la retire (cache le menu)
            // Si elle n'est pas présente → on l'ajoute (affiche le menu)
            dropdownMenu.classList.toggle('visible');
        });

        // Quand on clique n'importe où dans la page
        document.addEventListener('click', function (e) {
            //  Si le clic ne vient NI du bouton NI du menu
            if (!toggleButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                //  On ferme le menu en supprimant la classe 'visible'
                dropdownMenu.classList.remove('visible');
            }
        });
    }
});
