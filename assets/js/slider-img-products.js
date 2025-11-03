//This JS manages images in the detailed description of a product: click the left/right arrows, click on an image to display it in a larger size.

// Wait until the entire DOM (HTML structure) is fully loaded before executing the script.
// This ensures that all elements (main image, thumbnails, buttons) are available to be accessed.
document.addEventListener('DOMContentLoaded', () => {
    // Select the main product image element (the large image displayed prominently)
    const mainImage = document.getElementById('main-product-image');

    // Select all thumbnail images (small clickable images below the main image)
    const thumbnails = document.querySelectorAll('.thumbnail');

    // Select the previous and next navigation buttons (usually arrow buttons)
    const prevBtn = document.getElementById('prev-image');
    const nextBtn = document.getElementById('next-image');

    // Store the index of the currently displayed image
    let currentIndex = 0;

    /**
     * Updates the main product image to display the image corresponding to the given index.
     * Also updates the "active" class on thumbnails to highlight the selected one.
     * @param {number} index - The index of the thumbnail to display in the main image.
     */
    function updateMainImage(index) {
        // Update the current index with the selected thumbnail
        currentIndex = index;

        // Get the thumbnail element at the specified index
        const thumbnail = thumbnails[index];
        if (!thumbnail) return; // If no thumbnail exists at this index, exit the function

        // Update the src attribute of the main image to match the selected thumbnail
        mainImage.src = thumbnail.src;

        // Remove the 'active' class from all thumbnails to reset styling
        thumbnails.forEach(t => t.classList.remove('active'));

        // Add the 'active' class to the selected thumbnail to highlight it
        thumbnail.classList.add('active');
    }

    // Initialize the main image with the first thumbnail if any thumbnails exist
    if (thumbnails.length > 0) {
        updateMainImage(0);
    }

    /**
     * Handle click events on thumbnails.
     * When a thumbnail is clicked, update the main image to match it.
     */
    thumbnails.forEach((thumb, i) => {
        thumb.addEventListener('click', () => {
            updateMainImage(i);
        });
    });

    /**
     * Handle click on the "previous" button.
     * Displays the previous image in the carousel.
     * If currently at the first image, loops around to the last image.
     */
    prevBtn?.addEventListener('click', () => {
        if (thumbnails.length === 0) return; // Exit if there are no thumbnails

        let newIndex = currentIndex - 1;
        if (newIndex < 0) newIndex = thumbnails.length - 1; // Loop to last image if at the start

        updateMainImage(newIndex); // Update the main image to the new index
    });

    /**
     * Handle click on the "next" button.
     * Displays the next image in the carousel.
     * If currently at the last image, loops around to the first image.
     */
    nextBtn?.addEventListener('click', () => {
        if (thumbnails.length === 0) return; // Exit if there are no thumbnails

        let newIndex = currentIndex + 1;
        if (newIndex >= thumbnails.length) newIndex = 0; // Loop to first image if at the end

        updateMainImage(newIndex); // Update the main image to the new index
    });
});
