/**
 * order.js
 * --------------------------
 * Gère la sélection de la méthode de livraison, l'intégration du widget Mondial Relay,
 * la mise à jour du prix total, et l'affichage des bons boutons (Payline / Valider).
 * --------------------------
 */

$(document).ready(function () {

    // ===============================
    //  Sélection des éléments du DOM
    // ===============================
    const radios = $('input[type="radio"][name$="[delivery_method]"], input[type="radio"][name$=".delivery_method"]');
    const relayContainer = $('#mondial-relay-container');
    const confirmRelayBtn = $('#confirm-relay');
    const paylineBtn = $('#payline-button');
    const submitBtn = $('button[type="submit"]'); // bouton principal "Valider ma commande"

    // Eléments de calcul du total
    const baseTotalEl = $('#base-total');
    const deliveryPriceEl = $('#delivery-price');
    const finalTotalEl = $('#final-total');

    // ===========================================================
    //  Fonction utilitaire : met à jour les totaux dynamiquement
    // ===========================================================
    function updateTotals(deliveryMethod) {
        // Récupère le prix total de base
        let baseTotal = parseFloat(baseTotalEl.text().replace(' ', '')) || 0;
        let deliveryPrice = 0;

        // Si une méthode est sélectionnée, on prend le prix associé via data-price
        if (deliveryMethod) {
            const selectedRadio = radios.filter(':checked');
            if (selectedRadio.length) {
                deliveryPrice = parseFloat(selectedRadio.data('price')) || 0;
            }
        }

        // Met à jour les champs visibles dans le DOM
        deliveryPriceEl.text(deliveryPrice.toFixed(2) + ' €');
        finalTotalEl.text((baseTotal + deliveryPrice).toFixed(2) + ' €');
    }

    // ===========================================================
    //  Initialisation : si une méthode est déjà cochée au chargement
    // ===========================================================
    const initiallyChecked = radios.filter(':checked').val();
    if (initiallyChecked) {
        updateTotals(initiallyChecked);

        if (initiallyChecked === 'relay') {
            // Si "point relais" est déjà sélectionné, on affiche directement la carte
            relayContainer.show();
            submitBtn.hide();   // Cache le bouton "Valider ma commande"
            paylineBtn.hide();  // Cache Payline tant que le relais n’est pas confirmé
        }
    }

    // ===========================================================
    //  Réagit au changement de méthode de livraison
    // ===========================================================
    radios.on('change', function () {
        const selected = $(this).val(); // "relay", "home", "pickup", etc.
        updateTotals(selected);

        if (selected === 'relay') {
            // Si l’utilisateur choisit "Point relais"
            relayContainer.show();    // Affiche la carte Mondial Relay
            paylineBtn.hide();        // Cache le bouton Payline pour l’instant
            submitBtn.hide();         // Cache le bouton principal
            confirmRelayBtn.prop('disabled', true); // Désactive le bouton tant qu’aucun relais choisi

            // Initialise ou recharge le widget Mondial Relay
            initMondialRelayWidget();

        } else {
            // Toute autre méthode → on masque la zone relais
            relayContainer.hide();
            paylineBtn.hide();   // Cache Payline
            submitBtn.show();    // Réaffiche "Valider ma commande"
            confirmRelayBtn.prop('disabled', true);
        }
    });

    // ===========================================================
    //  Confirmation d’un point relais
    // ===========================================================
    confirmRelayBtn.on('click', function() {
        // Quand l'utilisateur clique sur "Confirmer le point relais"
        paylineBtn.show();       // Montre le bouton Payline
        confirmRelayBtn.prop('disabled', true); // Désactive le bouton pour éviter un double clic
        submitBtn.hide();        // Cache le bouton principal du formulaire

        // Défilement fluide vers le bouton Payline
        $('html, body').animate({scrollTop: paylineBtn.offset().top}, 500);
    });

    // ===========================================================
    //  Fonction d’initialisation du widget Mondial Relay
    // ===========================================================
    function initMondialRelayWidget() {
        const initWidget = () => {
            // Vérifie si le plugin Mondial Relay est bien chargé
            if (typeof $.fn.MR_ParcelShopPicker === 'undefined') {
                console.warn('⏳ Plugin Mondial Relay non encore chargé, nouvel essai dans 300ms...');
                setTimeout(initWidget, 300); // Réessaie après 300ms
                return;
            }

            // Si déjà initialisé, on le rafraîchit simplement
            if ($("#Zone_Widget").data('MRWidgetInitialized')) {
                console.log(' Rafraîchissement du widget Mondial Relay');
                $("#Zone_Widget").MR_ParcelShopPicker('refresh');
                return;
            }

            // Initialisation du widget
            console.log(' Initialisation du widget Mondial Relay');
            $("#Zone_Widget").MR_ParcelShopPicker({
                Target: "Zone_Widget",  // ID du conteneur
                Brand: "CC20SXK2",      // Identifiant Mondial Relay de la boutique
                Country: "FR",          // Pays
                PostCode: "56000",      // Code postal par défaut
                NbResults: 10,          // Nombre de points affichés
                ShowResultsOnMap: true, // Affiche la carte Leaflet
                ColLivMod: "24R",       // Type de livraison (standard)
                OnParcelShopSelected: function (data) {
                    // ⚡ Callback : quand un relais est sélectionné
                    $('#relay-info').html(`
                        <strong>${data.Nom}</strong><br>
                        ${data.Adresse1}<br>
                        ${data.CP} ${data.Ville}
                    `);

                    // Enregistre les infos dans les champs cachés du formulaire
                    $('#relay_name').val(data.Nom);
                    $('#relay_address').val(data.Adresse1);
                    $('#relay_cp').val(data.CP);
                    $('#relay_city').val(data.Ville);

                    // Active le bouton "Confirmer le point relais"
                    confirmRelayBtn.prop('disabled', false);
                }
            });

            // Empêche les initialisations multiples
            $("#Zone_Widget").data('MRWidgetInitialized', true);

            // Forcer le resize Leaflet après l'initialisation pour mobile
            setTimeout(adjustLeafletMap, 500);
        };



        // Démarre la vérification/initialisation
        initWidget();
    }

    // ===========================================================
    //  Fonction utilitaire : ajuste la carte Leaflet
    // ===========================================================
    function adjustLeafletMap() {
        const mapContainer = $("#Zone_Widget .leaflet-container");
        if (mapContainer.length && mapContainer[0]._leaflet_map) {
            mapContainer[0]._leaflet_map.invalidateSize(); // Redessine correctement la carte
        }
    }

    // ===========================================================
    //  Rafraîchit la carte Mondial Relay si on redimensionne la fenêtre
    // ===========================================================
    $(window).on('resize', function () {
        if ($("#Zone_Widget").data('MRWidgetInitialized')) {
            $("#Zone_Widget").MR_ParcelShopPicker('refresh');
            setTimeout(adjustLeafletMap, 300);
        }
    });
});
