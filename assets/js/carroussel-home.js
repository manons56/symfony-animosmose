/**
 * carousel-navigation.js
 * --------------------------
 * This script manages a horizontal carousel with previous/next buttons,
 * supporting responsive behavior (different items visible on desktop vs. mobile).
 *
 * Main functionalities:
 * 1. Tracks the current carousel index.
 * 2. Calculates how many items should be visible based on screen width.
 * 3. Moves the carousel left/right when navigation buttons are clicked.
 * 4. Resets the carousel on window resize to prevent misalignment.
 */

document.addEventListener('DOMContentLoaded', () => {
    // -----------------------------------------------------------
    // SELECT DOM ELEMENTS
    // -----------------------------------------------------------
    const track = document.querySelector('.carousel-track');       // The container holding all carousel items
    const prevBtn = document.querySelector('.carousel-btn.prev');  // Button to scroll to previous items
    const nextBtn = document.querySelector('.carousel-btn.next');  // Button to scroll to next items
    const items = Array.from(track.children);                      // Array of all carousel items

    let index = 0; // Tracks the first visible item in the carousel

    // -----------------------------------------------------------
    // FUNCTION: Determine number of items visible based on viewport
    // -----------------------------------------------------------
    function itemsPerView() {
        return window.innerWidth >= 768 ? 3 : 1; // Desktop: 3 items visible, Mobile: 1 item visible
    }

    // -----------------------------------------------------------
    // FUNCTION: Update carousel transform based on current index
    // -----------------------------------------------------------
    function updateCarousel() {
        const perView = itemsPerView(); // Get current number of items per view
        // Translate the track left by the proper percentage
        track.style.transform = `translateX(-${(100 / perView) * index}%)`;
    }

    // -----------------------------------------------------------
    // EVENT: Click "Next" button
    // -----------------------------------------------------------
    nextBtn.addEventListener('click', () => {
        const perView = itemsPerView();
        // Only increment index if we haven’t reached the last fully visible set
        if (index < items.length - perView) index++;
        updateCarousel(); // Apply new transform
    });

    // -----------------------------------------------------------
    // EVENT: Click "Previous" button
    // -----------------------------------------------------------
    prevBtn.addEventListener('click', () => {
        if (index > 0) index--; // Decrement index if not at start
        updateCarousel();       // Apply new transform
    });

    // -----------------------------------------------------------
    // EVENT: Reset carousel on window resize
    // -----------------------------------------------------------
    window.addEventListener('resize', () => {
        index = 0;       // Reset index to 0 to avoid misalignment
        updateCarousel(); // Update transform
    });

    // -----------------------------------------------------------
    // INITIALIZATION
    // -----------------------------------------------------------
    updateCarousel(); // Set initial transform when page loads
});
