/**
 * carousel-clone.js
 * --------------------------
 * This script enables an "infinite scroll" effect for a carousel by duplicating
 * all carousel items and appending the clones at the end of the track.
 * This allows the carousel to loop seamlessly without any visible gaps.
 *
 * Key functionalities:
 * 1. Selects the carousel track (the container holding all items/cards).
 * 2. Duplicates each carousel item and appends it to the track.
 * 3. Hides the cloned items from screen readers (aria-hidden).
 * 4. Disables keyboard focus on cloned items to prevent navigation issues.
 */

document.addEventListener('DOMContentLoaded', () => {
    // -----------------------------------------------------------
    // SELECT THE CAROUSEL TRACK
    // -----------------------------------------------------------
    // The track is the container that holds all carousel items
    const track = document.querySelector('.carousel-track');

    // Only proceed if a carousel exists on the page
    if (track) {

        // -----------------------------------------------------------
        // GET ALL CAROUSEL ITEMS
        // -----------------------------------------------------------
        // Convert the children NodeList into an array for easy iteration
        const items = Array.from(track.children);

        // -----------------------------------------------------------
        // DUPLICATE EACH ITEM
        // -----------------------------------------------------------
        // Append clones at the end to create a continuous loop effect
        items.forEach(item => {
            const clone = item.cloneNode(true); // Deep clone the carousel item

            // Hide cloned items from screen readers
            clone.setAttribute('aria-hidden', 'true');

            // Prevent keyboard focus on cloned elements to avoid confusion
            clone.querySelectorAll('a, button, input').forEach(el => {
                el.setAttribute('tabindex', '-1');
            });

            // Append the clone to the end of the track
            track.appendChild(clone);
        });
    }
});
