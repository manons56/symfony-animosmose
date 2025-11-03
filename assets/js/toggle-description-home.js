//On the home page, in the “About us” section, on mobile devices,
// allows to enlarge or reduce the size of descriptive text.

// Waits until the entire HTML document is fully loaded and parsed
document.addEventListener('DOMContentLoaded', () => {
    // Selects all elements with the class "toggle-arrow"
    const toggles = document.querySelectorAll('.toggle-arrow');

    // Loops through each toggle element
    toggles.forEach(toggle => {
        // Adds a click event listener to each toggle
        toggle.addEventListener('click', () => {
            // Gets the element that comes just before the toggle in the DOM
            const description = toggle.previousElementSibling;

            // Toggles the "expanded" class on the description element
            // → If it's already expanded, it collapses; if not, it expands
            description.classList.toggle('expanded');

            // Also toggles the "expanded" class on the toggle arrow itself
            // → Usually used to visually rotate or change the arrow’s appearance
            toggle.classList.toggle('expanded');
        });
    });
});
