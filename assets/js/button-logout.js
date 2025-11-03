/**
 * user-menu.js
 * --------------------------
 * This script manages the user menu dropdown (e.g., "Log out" menu).
 * It handles showing/hiding the menu when the user icon is clicked,
 * and automatically closes it when clicking outside the menu.
 *
 * Features:
 * 1. Toggles visibility of the dropdown menu on icon click.
 * 2. Closes the dropdown when clicking anywhere outside the menu or icon.
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log('hello!'); // Debug log to confirm script is loaded

    // -----------------------------------------------------------
    // SELECT DOM ELEMENTS
    // -----------------------------------------------------------
    const toggleButton = document.getElementById('user-menu-toggle'); // The user icon button
    const dropdownMenu = document.getElementById('user-menu');        // The dropdown menu container

    // -----------------------------------------------------------
    // SAFETY CHECK: Ensure elements exist
    // -----------------------------------------------------------
    if (toggleButton && dropdownMenu) {

        // -----------------------------------------------------------
        // EVENT: Toggle dropdown menu when user icon is clicked
        // -----------------------------------------------------------
        toggleButton.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent default behavior (like following a link)

            // Toggle the 'visible' class on the dropdown menu
            // - If 'visible' exists → remove it (hide menu)
            // - If 'visible' does not exist → add it (show menu)
            dropdownMenu.classList.toggle('visible');
        });

        // -----------------------------------------------------------
        // EVENT: Close dropdown menu when clicking outside
        // -----------------------------------------------------------
        document.addEventListener('click', function (e) {
            // If click target is neither the toggle button nor the dropdown itself
            if (!toggleButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                // Remove 'visible' class to hide the menu
                dropdownMenu.classList.remove('visible');
            }
        });
    }
});
