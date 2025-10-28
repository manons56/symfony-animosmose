document.addEventListener('DOMContentLoaded', function() {
    const config = document.getElementById('popup-config');
    if (!config) {
        console.warn("Configuration de la popup introuvable.");
        return;
    }

    const popupUrl = config.dataset.popupUrl;
    const formActionUrl = config.dataset.formActionUrl;

    // Vérifie que les URLs existent bien
    if (!popupUrl || !formActionUrl) {
        console.error("URLs de la popup manquantes !");
        return;
    }

    const lastShown = localStorage.getItem('lastPopupTime');
    const now = Date.now();
    const oneDay = 24 * 60 * 60 * 1000; // 24h

    // Affiche la popup seulement si elle n’a pas été vue récemment
    if (!lastShown || now - lastShown > oneDay) {
        setTimeout(async () => {
            try {
                const response = await fetch(popupUrl);
                const html = await response.text();

                const div = document.createElement('div');
                div.innerHTML = html;
                document.body.appendChild(div);

                const popup = document.querySelector('#shop-popup');
                if (!popup) return;

                popup.style.display = 'block';

                // Bouton de fermeture
                const closeBtn = document.getElementById('close-popup');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        popup.style.display = 'none';
                        localStorage.setItem('lastPopupTime', Date.now());
                    });
                }

                // Envoi du formulaire en AJAX
                const form = document.getElementById('contact-form');
                if (form) {
                    form.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const formData = new FormData(form);

                        const res = await fetch(formActionUrl, {
                            method: 'POST',
                            body: formData
                        });

                        if (res.ok) {
                            alert('Message envoyé !');
                            popup.style.display = 'none';
                            localStorage.setItem('lastPopupTime', Date.now());
                        } else {
                            alert('Erreur lors de l’envoi.');
                        }
                    });
                }
            } catch (err) {
                console.error("Erreur lors du chargement de la popup :", err);
            }
        }, 5000);
    }
});
