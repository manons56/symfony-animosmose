// ===========================================================
// HAMBURGER MENU TOGGLE SCRIPT
// ===========================================================
// This script handles the opening and closing of a hamburger-style
// navigation menu. It updates ARIA attributes for accessibility,
// toggles CSS classes for animation, and controls the menu visibility.

// -----------------------------------------------------------
// SELECT DOM ELEMENTS
// -----------------------------------------------------------
// Get the hamburger icon element (the button with three bars)
const hamburger = document.getElementById('hamburger');

// Get the menu list element (the dropdown or nav links container)
const menu = document.getElementById('menu-list');

// -----------------------------------------------------------
// EVENT LISTENER: Toggle menu on click
// -----------------------------------------------------------
// When the user clicks the hamburger icon:
hamburger.addEventListener('click', () => {

    // -----------------------------------------------------------
    // ACCESSIBILITY: Determine current state
    // -----------------------------------------------------------
    // Check the current 'aria-expanded' attribute to know if the menu is open
    // 'aria-expanded' is 'true' when menu is open, 'false' when closed
    const expanded = hamburger.getAttribute('aria-expanded') === 'true' || false;

    // Update 'aria-expanded' with the opposite value
    // This ensures assistive technologies know the menu's current state
    hamburger.setAttribute('aria-expanded', !expanded);

    // -----------------------------------------------------------
    // CSS ANIMATIONS: Toggle classes
    // -----------------------------------------------------------
    // Toggle the 'open' class on the hamburger button itself
    // Useful for animating the icon (e.g., changing bars to an X)
    hamburger.classList.toggle('open');

    // Toggle the 'show' class on the menu container
    // This typically controls visibility via CSS (e.g., display or opacity)
    menu.classList.toggle('show');
});
