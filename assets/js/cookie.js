// On attend que tout le DOM soit chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', () => {

    // Récupère la bannière de cookies
    const banner = document.getElementById('cookie-banner');

    // Récupère les boutons "Accepter" et "Refuser" de la bannière
    const acceptBtn = document.getElementById('accept');
    const rejectBtn = document.getElementById('reject');

    // Récupère le conteneur des publications Instagram
    const instaContainer = document.querySelector('.posts-insta');

    // Récupère le conteneur de la vidéo Dailymotion
    const dailymotionContainer = document.getElementById('dailymotion-container');


    // --- FONCTION : Injection de la vidéo Dailymotion ---
    function loadDailymotion() {
        // Vérifie si l'iframe n'existe pas déjà pour éviter les doublons
        if (!dailymotionContainer.querySelector('iframe')) {
            const iframe = document.createElement('iframe'); // Crée un nouvel iframe
            iframe.src = "https://geo.dailymotion.com/player.html?video=x63r9l"; // URL de la vidéo
            iframe.style.width = "100%"; // Largeur responsive
            iframe.style.height = "100%"; // Hauteur responsive
            iframe.style.border = "none"; // Pas de bordure
            iframe.allowFullscreen = true; // Permet le plein écran
            iframe.title = "Vidéo d'Animosmose dans la presse"; // Pour l'accessibilité
            iframe.allow = "web-share"; // Autorise le partage via Dailymotion
            dailymotionContainer.appendChild(iframe); // Ajoute l'iframe au DOM
        }
    }

    // --- FONCTION : Injection du script Instagram ---
    function loadInstagram() {
        const script = document.createElement('script'); // Crée un élément script
        script.src = "//www.instagram.com/embed.js"; // Source du script Instagram
        script.async = true; // Charge le script de façon asynchrone
        document.body.appendChild(script); // Injecte le script dans le body
    }

    // --- FONCTION : Envoi du consentement au serveur ---
    function sendConsent(accept) {
        fetch('/cookie', { // Envoie une requête POST vers /cookie
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'accept=' + accept // Envoie true ou false selon le choix
        }).then(() => {
            // Masque la bannière de cookies après choix
            if (banner) banner.style.display = 'none';

            // Si l'utilisateur accepte
            if (accept) {
                // --- Instagram ---
                if (instaContainer) {
                    instaContainer.classList.remove('blurred'); // Supprime le flou
                    const overlay = instaContainer.querySelector('.insta-overlay'); // Récupère l'overlay
                    if (overlay) overlay.remove(); // Supprime l'overlay
                    loadInstagram(); // Charge les posts Instagram
                }

                // --- Dailymotion ---
                if (dailymotionContainer) {
                    loadDailymotion(); // Injecte la vidéo
                }
            }
        });
    }

    // --- ÉCOUTEURS DES BOUTONS DE LA BANNIÈRE ---
    if (acceptBtn) acceptBtn.addEventListener('click', () => sendConsent(true)); // Acceptation
    if (rejectBtn) rejectBtn.addEventListener('click', () => sendConsent(false)); // Refus

    // --- BOUTON “ACCEPTER MAINTENANT” DANS LES OVERLAYS ---
    document.addEventListener('click', (e) => {
        // Si on clique sur un bouton avec la classe accept-now-btn
        if (e.target && e.target.classList.contains('accept-now-btn')) {
            sendConsent(true); // Accepte les cookies et charge le contenu bloqué
        }
    });

});
