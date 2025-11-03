// ------------------------------------------------------
// POPUP HANDLER SCRIPT
// ------------------------------------------------------
// This script handles dynamically fetching and displaying a promotional popup in the store page.
// It ensures the popup is not repeatedly shown within 24 hours, manages the
// close button, and handles form submission via AJAX without reloading the page.
//This JS allows you to display a pop-up with a form on the store page. This allows people to ask questions, which are sent to the admin's email inbox.

document.addEventListener('DOMContentLoaded', function() {

    // ------------------------------------------------------
    // GET CONFIGURATION ELEMENT
    // ------------------------------------------------------
    // Retrieve the popup configuration element from the DOM.
    // This element should contain data attributes with the URLs needed.
    const config = document.getElementById('popup-config');

    // Stop execution if the configuration element is missing
    if (!config) {
        console.warn("Popup configuration not found.");
        return;
    }

    // ------------------------------------------------------
    // EXTRACT URLS FROM DATA ATTRIBUTES
    // ------------------------------------------------------
    const popupUrl = config.dataset.popupUrl;         // URL to fetch the popup HTML content
    const formActionUrl = config.dataset.formActionUrl; // URL for AJAX form submission

    // Ensure both URLs exist
    if (!popupUrl || !formActionUrl) {
        console.error("Missing popup URLs!");
        return;
    }

    // ------------------------------------------------------
    // CHECK LAST DISPLAY TIME
    // ------------------------------------------------------
    // Retrieve the last timestamp when the popup was displayed from localStorage
    const lastShown = localStorage.getItem('lastPopupTime');
    const now = Date.now();
    const oneDay = 24 * 60 * 60 * 1000; // 24 hours in milliseconds

    // ------------------------------------------------------
    // CONDITIONALLY SHOW POPUP
    // ------------------------------------------------------
    // Show the popup only if it hasn't been shown in the last 24 hours
    if (!lastShown || now - lastShown > oneDay) {

        // Wait 5 seconds before showing the popup to avoid overwhelming the user
        setTimeout(async () => {
            try {
                // ------------------------------------------------------
                // FETCH POPUP HTML
                // ------------------------------------------------------
                // Fetch the HTML content of the popup from the server
                const response = await fetch(popupUrl);
                const html = await response.text();

                // ------------------------------------------------------
                // INSERT POPUP INTO DOM
                // ------------------------------------------------------
                // Create a temporary container div and inject the HTML content
                const div = document.createElement('div');
                div.innerHTML = html;
                document.body.appendChild(div);

                // Select the popup element by its ID
                const popup = document.querySelector('#shop-popup');
                if (!popup) return; // Safety check: exit if popup element not found

                // Make the popup visible
                popup.style.display = 'block';

                // ------------------------------------------------------
                // CLOSE BUTTON LOGIC
                // ------------------------------------------------------
                // Add a click listener to the close button to hide the popup
                // Update localStorage with the current timestamp to avoid showing again too soon
                const closeBtn = document.getElementById('close-popup');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        popup.style.display = 'none';
                        localStorage.setItem('lastPopupTime', Date.now());
                    });
                }

                // ------------------------------------------------------
                // FORM SUBMISSION VIA AJAX
                // ------------------------------------------------------
                // Select the contact form inside the popup
                const form = document.getElementById('contact-form');
                if (form) {

                    // Listen for form submission
                    form.addEventListener('submit', async (e) => {
                        e.preventDefault(); // Prevent page reload

                        // Collect the form data into a FormData object
                        const formData = new FormData(form);

                        // Send form data to the server using POST
                        const res = await fetch(formActionUrl, {
                            method: 'POST',
                            body: formData
                        });

                        // ------------------------------------------------------
                        // HANDLE RESPONSE
                        // ------------------------------------------------------
                        // Show success or error messages in French (UX requirement)
                        if (res.ok) {
                            alert('Message envoyé !'); // User sees this in French
                            popup.style.display = 'none';
                            localStorage.setItem('lastPopupTime', Date.now());
                        } else {
                            alert('Erreur lors de l’envoi.'); // Keep in French
                        }
                    });
                }

            } catch (err) {
                // Log any errors in fetching or displaying the popup
                console.error("Error loading the popup:", err);
            }
        }, 5000); // Delay of 5 seconds before showing the popup
    }
});
