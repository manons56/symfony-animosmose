/**
 * new-carousel.js
 * --------------------------
 * This script manages a horizontal carousel with previous/next buttons.
 * It supports responsive behavior, displaying a different number of items
 * depending on screen width, and resets the carousel on window resize.
 *
 * Features:
 * 1. Tracks the current carousel position using an index.
 * 2. Calculates how many items are visible per view based on viewport width.
 * 3. Moves the carousel left/right when navigation buttons are clicked.
 * 4. Resets the carousel to the first items when the window is resized.
 */

document.addEventListener('DOMContentLoaded', () => {
    // -----------------------------------------------------------
    // SELECT DOM ELEMENTS
    // -----------------------------------------------------------
    const track = document.querySelector('.new-carousel-track');       // The container holding all carousel items
    const prevBtn = document.querySelector('.new-carousel-btn.prev');  // Button to scroll to previous items
    const nextBtn = document.querySelector('.new-carousel-btn.next');  // Button to scroll to next items
    const items = Array.from(track.children);                          // Array of all carousel items

    let index = 0; // Tracks the first visible item in the carousel

    // -----------------------------------------------------------
    // FUNCTION: Determine number of items visible based on viewport
    // -----------------------------------------------------------
    function itemsPerView() {
        // Desktop: 2 items visible, Mobile: 1 item visible
        return window.innerWidth >= 768 ? 2 : 1;
    }

    // -----------------------------------------------------------
    // FUNCTION: Update carousel transform based on current index
    // -----------------------------------------------------------
    function updateCarousel() {
        const perView = itemsPerView();
        // Move the track to the left by a percentage of its width, based on index and items per view
        track.style.transform = `translateX(-${(100 / perView) * index}%)`;
    }

    // -----------------------------------------------------------
    // EVENT: Click "Next" button
    // -----------------------------------------------------------
    nextBtn.addEventListener('click', () => {
        const perView = itemsPerView();
        // Only increment index if there are more items to show
        if (index < items.length - perView) index++;
        updateCarousel();
    });

    // -----------------------------------------------------------
    // EVENT: Click "Previous" button
    // -----------------------------------------------------------
    prevBtn.addEventListener('click', () => {
        // Only decrement index if not at the start
        if (index > 0) index--;
        updateCarousel();
    });

    // -----------------------------------------------------------
    // EVENT: Reset carousel on window resize
    // -----------------------------------------------------------
    window.addEventListener('resize', () => {
        index = 0;        // Reset index to 0 to prevent misalignment
        updateCarousel(); // Update transform according to new viewport
    });

    // -----------------------------------------------------------
    // INITIALIZATION
    // -----------------------------------------------------------
    updateCarousel(); // Set initial transform on page load
});
