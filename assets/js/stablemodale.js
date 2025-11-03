//This JS is used to manage price modals: open them, close them, you can also click outside the modal to close it.

// Wait until the entire DOM (HTML structure) is fully loaded before running the script.
// This ensures that all elements we’re trying to access are available.
document.addEventListener('DOMContentLoaded', () => {

    // -----------------------------
    // DOM ELEMENT SELECTION
    // -----------------------------

    // Selects all buttons or elements that open modals.
    // These are identified using the "data-modal-target" attribute.
    // The value of that attribute should be a CSS selector pointing to the modal to open.
    const openModalButtons = document.querySelectorAll('[data-modal-target]');

    // Selects all buttons or elements that close modals.
    // These are identified using the "data-close-button" attribute.
    const closeModalButtons = document.querySelectorAll('[data-close-button]');

    // Selects the overlay element — the semi-transparent background behind the modal.
    // It usually darkens the rest of the page to focus the user’s attention on the modal.
    const overlay = document.getElementById('overlay');

    // Selects the main content container.
    // A blur effect will be applied to this element when a modal is open to create visual depth.
    const contentWrapper = document.querySelector('.content-wrapper');

    // Keeps a reference to whichever modal is currently open.
    // This allows for easy management (e.g., closing the active one when clicking the overlay).
    let activeModal = null;


    // -----------------------------
    // FUNCTIONS TO OPEN AND CLOSE MODALS
    // -----------------------------

    /**
     * Opens the given modal.
     * @param {HTMLElement} modal - The modal element to display.
     * @param {HTMLElement} triggerButton - (optional) The button that opened the modal.
     */
    function openModal(modal, triggerButton = null) {
        if (!modal) return; // Safety check: if modal doesn’t exist, stop here.

        // Adds the "active" class to display the modal and overlay.
        modal.classList.add('active');
        overlay.classList.add('active');
        contentWrapper.classList.add('blurred'); // Apply blur to the main page content.

        // Make the modal focusable and immediately focus it for accessibility.
        modal.setAttribute('tabindex', '-1');
        modal.focus();

        // Store the currently active modal and its trigger button (for focus restoration later).
        activeModal = modal;
        modal.triggerButton = triggerButton;

        // -----------------------------
        // KEYBOARD ACCESSIBILITY HANDLING
        // -----------------------------
        // This event listener traps the keyboard focus within the modal
        // and allows closing it with the Escape key.
        modal._keydownHandler = function(e) {
            // Select all elements inside the modal that can receive focus.
            const focusableElements = modal.querySelectorAll(
                'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])'
            );

            const firstEl = focusableElements[0];
            const lastEl = focusableElements[focusableElements.length - 1];

            // Handle keyboard navigation
            if (e.key === 'Tab') {
                // If user presses Shift + Tab (backward tabbing)
                if (e.shiftKey) {
                    // Prevent focus from leaving the modal when tabbing backward.
                    if (document.activeElement === firstEl) {
                        e.preventDefault();
                        lastEl.focus(); // Move focus to the last focusable element.
                    }
                }
                // Normal Tab (forward navigation)
                else {
                    // Prevent focus from escaping the modal when tabbing forward.
                    if (document.activeElement === lastEl) {
                        e.preventDefault();
                        firstEl.focus(); // Loop back to the first focusable element.
                    }
                }
            }
            // Pressing Escape closes the modal.
            else if (e.key === 'Escape') {
                closeModal(modal);
            }
        };

        // Attach the keyboard event handler to the modal.
        modal.addEventListener('keydown', modal._keydownHandler);
    }

    /**
     * Closes the given modal.
     * @param {HTMLElement} modal - The modal element to hide.
     */
    function closeModal(modal) {
        if (!modal) return; // Safety check.

        // Remove the "active" classes to hide the modal and overlay.
        modal.classList.remove('active');
        overlay.classList.remove('active');
        contentWrapper.classList.remove('blurred'); // Remove blur from background.

        activeModal = null; // Reset reference.

        // If the modal was opened by a button, restore focus to that button.
        if (modal.triggerButton) {
            modal.triggerButton.focus();
        }

        // Remove the keydown listener to avoid trapping focus after closing.
        if (modal._keydownHandler) {
            modal.removeEventListener('keydown', modal._keydownHandler);
            delete modal._keydownHandler;
        }
    }



    // -----------------------------
    // FOCUS TRAP (ACCESSIBILITY)
    // -----------------------------

    /**
     * Keeps keyboard focus trapped inside the modal when it’s open.
     * Prevents users from tabbing into the background content.
     * @param {HTMLElement} modal
     */
    function trapFocus(modal) {
        // Select all focusable elements within the modal.
        const focusableElements = modal.querySelectorAll(
            'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])'
        );

        const firstEl = focusableElements[0]; // First focusable element.
        const lastEl = focusableElements[focusableElements.length - 1]; // Last focusable element.

        // Listen for keydown events (specifically Tab and Escape).
        modal.addEventListener('keydown', function(e) {
            // Handle Tab key navigation.
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    // Shift + Tab (backward navigation)
                    if (document.activeElement === firstEl) {
                        e.preventDefault();
                        lastEl.focus();
                    }
                } else {
                    // Normal Tab (forward navigation)
                    if (document.activeElement === lastEl) {
                        e.preventDefault();
                        firstEl.focus();
                    }
                }
            }
            // Escape key closes the modal.
            else if (e.key === 'Escape') {
                closeModal(modal);
            }
        });
    }


    // -----------------------------
    // EVENT LISTENERS FOR BUTTONS
    // -----------------------------

    // Add click listeners to all modal-open buttons.
    openModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Get the modal element using the selector specified in data-modal-target.
            const modal = document.querySelector(button.dataset.modalTarget);
            // Open the corresponding modal and record which button triggered it.
            openModal(modal, button);
        });
    });

    // Add click listeners to all modal-close buttons.
    closeModalButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Find the closest parent element with class "modal" — that’s the one to close.
            const modal = button.closest('.modal');
            closeModal(modal);
        });
    });

    // If the overlay (the dark background) is clicked, close the currently active modal.
    overlay.addEventListener('click', () => {
        if (activeModal) {
            closeModal(activeModal);
        }
    });

});
