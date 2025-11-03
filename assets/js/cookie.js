/**
 * cookie-consent.js
 * --------------------------
 * This script manages the cookie consent banner and controls the loading of
 * third-party content (Instagram posts and a Dailymotion video) based on user consent.
 *
 * Key functionalities:
 * 1. Shows a cookie consent banner to the user.
 * 2. Sends the user's choice (accept/reject) to the server via a POST request.
 * 3. Loads Instagram embeds and a Dailymotion video only if the user has accepted cookies.
 * 4. Provides buttons to accept/reject cookies, including overlay buttons.
 * 5. Automatically loads blocked content if consent was already given.
 */

document.addEventListener('DOMContentLoaded', () => {
    // -----------------------------------------------------------
    // DOM ELEMENT SELECTION
    // -----------------------------------------------------------
    const banner = document.getElementById('cookie-banner'); // The cookie consent banner container
    const acceptBtn = document.getElementById('accept');     // Button to accept cookies
    const rejectBtn = document.getElementById('reject');     // Button to reject cookies
    const instaContainer = document.querySelector('.posts-insta'); // Container for Instagram posts
    const dailymotionContainer = document.getElementById('dailymotion-container'); // Container for Dailymotion video
    const consentStatus = document.getElementById('cookie-consent-status'); // Hidden element holding current consent status

    // Check if user has previously accepted or rejected cookies
    const isAccepted = consentStatus?.dataset.accepted === 'true';
    const isRejected = consentStatus?.dataset.rejected === 'true';

    // -----------------------------------------------------------
    // FUNCTION: Load Dailymotion video
    // -----------------------------------------------------------
    function loadDailymotion() {
        // Check if iframe already exists to avoid duplicate insertion
        if (!dailymotionContainer?.querySelector('iframe')) {
            const iframe = document.createElement('iframe');
            iframe.src = "https://geo.dailymotion.com/player.html?video=x63r9l";
            iframe.style.width = "100%";
            iframe.style.height = "100%";
            iframe.style.border = "none";
            iframe.allowFullscreen = true;            // Allow fullscreen playback
            iframe.title = "Vidéo d'Animosmose dans la presse"; // Accessibility: title for screen readers
            iframe.allow = "web-share";              // Allow web sharing
            dailymotionContainer.appendChild(iframe); // Insert iframe into the DOM
        }
    }

    // -----------------------------------------------------------
    // FUNCTION: Load Instagram embed script
    // -----------------------------------------------------------
    function loadInstagram() {
        const script = document.createElement('script');
        script.src = "//www.instagram.com/embed.js"; // Official Instagram embed script
        script.async = true;                          // Load asynchronously to avoid blocking
        document.body.appendChild(script);           // Append to body to execute
    }

    // -----------------------------------------------------------
    // FUNCTION: Send user consent to the server
    // -----------------------------------------------------------
    function sendConsent(accept) {
        // Send a POST request to the /cookie endpoint with the user's choice
        fetch('/cookie', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'accept=' + accept
        }).then(() => {
            // Hide the cookie banner after user action
            if (banner) banner.style.display = 'none';

            if (accept) {
                // If accepted, remove Instagram blur overlay and load Instagram content
                if (instaContainer) {
                    instaContainer.classList.remove('blurred');          // Remove visual blur
                    const overlay = instaContainer.querySelector('.insta-overlay'); // Remove any overlay
                    if (overlay) overlay.remove();
                    loadInstagram();                                    // Inject Instagram script
                }

                // Load the Dailymotion video if container exists
                if (dailymotionContainer) {
                    loadDailymotion();
                }
            }
        });
    }

    // -----------------------------------------------------------
    // AUTOMATIC CONTENT LOADING IF CONSENT ALREADY GIVEN
    // -----------------------------------------------------------
    if (isAccepted) {
        loadInstagram();
        loadDailymotion();
    }

    // -----------------------------------------------------------
    // EVENT LISTENERS: Accept / Reject buttons on the banner
    // -----------------------------------------------------------
    if (acceptBtn) {
        acceptBtn.addEventListener('click', () => sendConsent(true)); // Accept cookies
    }

    if (rejectBtn) {
        rejectBtn.addEventListener('click', () => sendConsent(false)); // Reject cookies
    }

    // -----------------------------------------------------------
    // EVENT LISTENER: Overlay "Accept Now" button
    // -----------------------------------------------------------
    document.addEventListener('click', (e) => {
        // Detect clicks on any element with the class 'accept-now-btn'
        if (e.target && e.target.classList.contains('accept-now-btn')) {
            sendConsent(true); // Accept cookies immediately
        }
    });
});
