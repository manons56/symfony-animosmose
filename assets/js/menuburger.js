// Récupère l'élément avec l'ID 'hamburger' (l'icône de menu, trois barres)
const hamburger = document.getElementById('hamburger');

// Récupère l'élément avec l'ID 'menu-list' (le menu déroulant ou la liste des liens de navigation)
const menu = document.getElementById('menu-list');

// Ajoute un écouteur d'événement sur le clic du bouton hamburger
hamburger.addEventListener('click', () => {
    // Vérifie l'état actuel de l'attribut 'aria-expanded' pour savoir si le menu est ouvert
    // Si l'attribut est égal à 'true', alors expanded vaut true, sinon false
    const expanded = hamburger.getAttribute('aria-expanded') === 'true' || false;

    // Met à jour l'attribut 'aria-expanded' avec la valeur opposée (true devient false, et inversement)
    hamburger.setAttribute('aria-expanded', !expanded);

    // Bascule la classe CSS 'open' sur l'élément hamburger (utile pour les animations ou les styles d'état ouvert)
    hamburger.classList.toggle('open');

    // Bascule la classe CSS 'show' sur le menu, pour l'afficher ou le cacher (souvent via `display: none/block` ou `opacity`)
    menu.classList.toggle('show');
});
