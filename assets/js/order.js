/**
 * order.js
 * --------------------------
 * Handles the selection of delivery methods, integrates the Mondial Relay widget,
 * updates the total price dynamically, and controls the visibility of appropriate
 * buttons (Payline / Submit).
 * --------------------------
 */

$(document).ready(function () {

    // ===============================
    //  SELECT DOM ELEMENTS
    // ===============================
    const radios = $('input[type="radio"][name$="[delivery_method]"], input[type="radio"][name$=".delivery_method"]');
    const relayContainer = $('#mondial-relay-container'); // Container for Mondial Relay map & options
    const confirmRelayBtn = $('#confirm-relay');          // Button to confirm chosen relay
    const paylineBtn = $('#payline-button');             // Payline payment button
    const submitBtn = $('button[type="submit"]');        // Main form submit button ("Valider ma commande")

    // Elements used for dynamic total price calculations
    const baseTotalEl = $('#base-total');       // Base order total
    const deliveryPriceEl = $('#delivery-price'); // Delivery price display
    const finalTotalEl = $('#final-total');     // Final total display including delivery

    // ===========================================================
    //  UTILITY FUNCTION: Update dynamic totals
    // ===========================================================
    function updateTotals(deliveryMethod) {
        // Parse the base total from the DOM, fallback to 0 if missing
        let baseTotal = parseFloat(baseTotalEl.text().replace(' ', '')) || 0;
        let deliveryPrice = 0;

        // If a delivery method is selected, retrieve its price from data-price attribute
        if (deliveryMethod) {
            const selectedRadio = radios.filter(':checked');
            if (selectedRadio.length) {
                deliveryPrice = parseFloat(selectedRadio.data('price')) || 0;
            }
        }

        // Update visible DOM elements with calculated prices
        deliveryPriceEl.text(deliveryPrice.toFixed(2) + ' €');
        finalTotalEl.text((baseTotal + deliveryPrice).toFixed(2) + ' €');
    }

    // ===========================================================
    //  INITIALIZATION: Handle pre-selected delivery method
    // ===========================================================
    const initiallyChecked = radios.filter(':checked').val();
    if (initiallyChecked) {
        updateTotals(initiallyChecked);

        if (initiallyChecked === 'relay') {
            // If "relay point" was pre-selected, show the map immediately
            relayContainer.show();
            submitBtn.hide();   // Hide the main submit button
            paylineBtn.hide();  // Hide Payline until relay is confirmed
        }
    }

    // ===========================================================
    //  LISTENER: Delivery method change
    // ===========================================================
    radios.on('change', function () {
        const selected = $(this).val(); // e.g., "relay", "home", "pickup", etc.
        updateTotals(selected);

        if (selected === 'relay') {
            // User selects a relay point
            relayContainer.show();       // Display Mondial Relay widget
            paylineBtn.hide();           // Hide Payline temporarily
            submitBtn.hide();            // Hide main submit button
            confirmRelayBtn.prop('disabled', true); // Disable confirm button until a relay is chosen

            // Initialize or refresh the Mondial Relay widget
            initMondialRelayWidget();

        } else {
            // Any other delivery method → hide relay-related UI
            relayContainer.hide();
            paylineBtn.hide();
            submitBtn.show();            // Show main submit button
            confirmRelayBtn.prop('disabled', true); // Ensure confirm button is disabled
        }
    });

    // ===========================================================
    //  RELAY CONFIRMATION
    // ===========================================================
    confirmRelayBtn.on('click', function() {
        // User clicks "Confirm relay point"
        paylineBtn.show();             // Show Payline button for payment
        confirmRelayBtn.prop('disabled', true); // Disable confirm to prevent double-clicks
        submitBtn.hide();              // Hide the main submit button

        // Smooth scroll to Payline button for better UX
        $('html, body').animate({scrollTop: paylineBtn.offset().top}, 500);
    });

    // ===========================================================
    //  INITIALIZE MONDIAL RELAY WIDGET
    // ===========================================================
    function initMondialRelayWidget() {
        const initWidget = () => {
            // Ensure the Mondial Relay plugin is loaded
            if (typeof $.fn.MR_ParcelShopPicker === 'undefined') {
                console.warn('⏳ Mondial Relay plugin not loaded yet, retrying in 300ms...');
                setTimeout(initWidget, 300); // Retry after 300ms
                return;
            }

            // Refresh the widget if already initialized
            if ($("#Zone_Widget").data('MRWidgetInitialized')) {
                console.log('Refreshing Mondial Relay widget');
                $("#Zone_Widget").MR_ParcelShopPicker('refresh');
                return;
            }

            // Initial widget setup
            console.log('Initializing Mondial Relay widget');
            $("#Zone_Widget").MR_ParcelShopPicker({
                Target: "Zone_Widget",          // Container ID
                Brand: "CC20SXK2",              // Shop-specific Mondial Relay ID
                Country: "FR",                  // Country for search
                PostCode: "56000",              // Default postal code
                NbResults: 10,                  // Number of relay points displayed
                ShowResultsOnMap: true,         // Display Leaflet map
                ColLivMod: "24R",               // Delivery type
                OnParcelShopSelected: function (data) {
                    // Callback: when a relay is selected
                    $('#relay-info').html(`
                        <strong>${data.Nom}</strong><br>
                        ${data.Adresse1}<br>
                        ${data.CP} ${data.Ville}
                    `);

                    // Store selected relay information in hidden form fields
                    $('#relay_name').val(data.Nom);
                    $('#relay_address').val(data.Adresse1);
                    $('#relay_cp').val(data.CP);
                    $('#relay_city').val(data.Ville);

                    // Enable the confirm relay button
                    confirmRelayBtn.prop('disabled', false);
                }
            });

            // Prevent multiple widget initializations
            $("#Zone_Widget").data('MRWidgetInitialized', true);

            // Adjust Leaflet map size after initialization for mobile responsiveness
            setTimeout(adjustLeafletMap, 500);
        };

        // Start initialization check
        initWidget();
    }

    // ===========================================================
    //  UTILITY FUNCTION: Adjust Leaflet map
    // ===========================================================
    function adjustLeafletMap() {
        const mapContainer = $("#Zone_Widget .leaflet-container");
        // Ensure the Leaflet map exists before calling invalidateSize
        if (mapContainer.length && mapContainer[0]._leaflet_map) {
            mapContainer[0]._leaflet_map.invalidateSize(); // Redraw map correctly
        }
    }

    // ===========================================================
    //  REFRESH MONDIAL RELAY MAP ON WINDOW RESIZE
    // ===========================================================
    $(window).on('resize', function () {
        if ($("#Zone_Widget").data('MRWidgetInitialized')) {
            $("#Zone_Widget").MR_ParcelShopPicker('refresh'); // Refresh widget to adapt to new size
            setTimeout(adjustLeafletMap, 300);               // Re-adjust Leaflet map
        }
    });
});
