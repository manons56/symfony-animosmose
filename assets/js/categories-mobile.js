/**
 * category-menu-toggle.js
 * --------------------------
 * This script handles the interactive opening and closing of submenus in a category menu.
 *
 * Key functionalities:
 * 1. Detects clicks on top-level category links in a menu.
 * 2. Toggles the visibility of their associated subcategory lists.
 * 3. Ensures only one submenu is open at a time.
 * 4. Prevents default link navigation when toggling submenus.
 */

document.addEventListener('DOMContentLoaded', () => {
    // -----------------------------------------------------------
    // SELECT ALL CATEGORY MENU LINKS
    // -----------------------------------------------------------
    // Select all <a> elements that are direct children of <li> elements inside the category menu
    document.querySelectorAll('.category-menu > ul > li > a').forEach(link => {

        // -----------------------------------------------------------
        // ADD CLICK EVENT LISTENER TO EACH LINK
        // -----------------------------------------------------------
        link.addEventListener('click', function(e) {
            // Get the parent <li> element of the clicked link
            const li = this.parentElement;

            // Search for a submenu inside this <li> with class .subcategory-list
            const submenu = li.querySelector('.subcategory-list');

            // If a submenu exists
            if (submenu) {
                // Prevent the default action (navigation) of the link
                e.preventDefault();

                // If the submenu is already visible
                if (submenu.classList.contains('visible')) {
                    // Hide it by removing the 'visible' class
                    submenu.classList.remove('visible');
                    return; // Stop further execution
                }

                // Close any other currently open submenus
                document.querySelectorAll('.subcategory-list.visible').forEach(openMenu => {
                    openMenu.classList.remove('visible');
                });

                // Show the clicked submenu by adding the 'visible' class
                submenu.classList.add('visible');
            }
        });
    });
});
