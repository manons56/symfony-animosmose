// JS pour gérer le clic
document.addEventListener('DOMContentLoaded', () => {
    const acceptBtn = document.getElementById('accept');
    const rejectBtn = document.getElementById('reject');
    const banner = document.getElementById('cookie-banner');

    // Vérifie si la bannière existe (peut ne pas être là si le cookie est déjà défini)
    if (!banner) {
        return;
    }

    // Ajoute l'événement au bouton "Accepter"
    if (acceptBtn) {
        acceptBtn.addEventListener('click', () => sendConsent(true));
    }

    // Ajoute l'événement au bouton "Refuser"
    if (rejectBtn) {
        rejectBtn.addEventListener('click', () => sendConsent(false));
    }

    function sendConsent(accept) {
        fetch('/cookie', { // on envoie une requete HTTP à l'URL /cookie
            method: 'POST', // on envoie la requete en POST (on transmet des données)
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}, // précise que le corps de la requete est encodé comme un formulaire, càd sous la forme clé=valeur
            body: 'accept=' + accept // le corps de la requete contient la donnée accept avec la valeur du paramètre, si accept vaut true, accept=true, sinon accept=false
        }).then(() => { // renvoie une promesse qui se résout quand la réponse du serveur arrive
            banner.style.display = 'none'; // quand la réponse arrive, la banière de consentement va disparaitre car une réponse a été apportée
        });
    }
});
