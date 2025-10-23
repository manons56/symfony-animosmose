// Attendre que le DOM soit entièrement chargé
document.addEventListener('DOMContentLoaded', () => {
    // -- ÉLÉMENTS DU DOM --
    const banner = document.getElementById('cookie-banner');
    const acceptBtn = document.getElementById('accept');
    const rejectBtn = document.getElementById('reject');
    const instaContainer = document.querySelector('.posts-insta');
    const dailymotionContainer = document.getElementById('dailymotion-container');
    const consentStatus = document.getElementById('cookie-consent-status');

    const isAccepted = consentStatus?.dataset.accepted === 'true';
    const isRejected = consentStatus?.dataset.rejected === 'true';

    // -- FONCTION : Injecter la vidéo Dailymotion --
    function loadDailymotion() {
        if (!dailymotionContainer?.querySelector('iframe')) {
            const iframe = document.createElement('iframe');
            iframe.src = "https://geo.dailymotion.com/player.html?video=x63r9l";
            iframe.style.width = "100%";
            iframe.style.height = "100%";
            iframe.style.border = "none";
            iframe.allowFullscreen = true;
            iframe.title = "Vidéo d'Animosmose dans la presse";
            iframe.allow = "web-share";
            dailymotionContainer.appendChild(iframe);
        }
    }

    // -- FONCTION : Injecter le script Instagram --
    function loadInstagram() {
        const script = document.createElement('script');
        script.src = "//www.instagram.com/embed.js";
        script.async = true;
        document.body.appendChild(script);
    }

    // -- FONCTION : Envoi du consentement au serveur --
    function sendConsent(accept) {
        fetch('/cookie', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'accept=' + accept
        }).then(() => {
            // Masquer la bannière
            if (banner) banner.style.display = 'none';

            if (accept) {
                // Instagram
                if (instaContainer) {
                    instaContainer.classList.remove('blurred');
                    const overlay = instaContainer.querySelector('.insta-overlay');
                    if (overlay) overlay.remove();
                    loadInstagram();
                }

                // Dailymotion
                if (dailymotionContainer) {
                    loadDailymotion();
                }
            }
        });
    }

    // -- Si déjà accepté : charger les contenus bloqués directement --
    if (isAccepted) {
        loadInstagram();
        loadDailymotion();
    }

    // -- Bannières : clic sur "Accepter" ou "Refuser" --
    if (acceptBtn) {
        acceptBtn.addEventListener('click', () => sendConsent(true));
    }

    if (rejectBtn) {
        rejectBtn.addEventListener('click', () => sendConsent(false));
    }

    // -- Overlay : bouton "Accepter maintenant" --
    document.addEventListener('click', (e) => {
        if (e.target && e.target.classList.contains('accept-now-btn')) {
            sendConsent(true);
        }
    });
});
