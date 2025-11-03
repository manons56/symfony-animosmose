// This script manages the display of the delivery price and the final order total
// based on the delivery method selected by the user.
// It also shows or hides the payment button (payline-button) depending on the chosen delivery method.

// When the page loads, the script:
// - Retrieves the base total (excluding delivery).
// - Waits for the user to select a delivery method (or checks for an existing selection).
// - Calculates the final total (base + delivery).
// - Shows or hides the payment button according to the selected delivery method.

document.addEventListener('DOMContentLoaded', function () {
    console.log('Delivery script loaded!');

    // -----------------------------------------------------------
    // SELECT DOM ELEMENTS
    // -----------------------------------------------------------
    const radios = document.querySelectorAll('input[name="delivery[delivery_method]"]');
    // Select all radio buttons representing delivery methods

    const baseTotal = parseFloat(document.getElementById('base-total').textContent.replace(/\s/g, '')) || 0;
    // Get the base total (excluding delivery), remove spaces, convert to float
    // If parsing fails, default to 0

    const deliveryPriceEl = document.getElementById('delivery-price');
    const finalTotalEl = document.getElementById('final-total');
    // Get DOM elements for displaying the delivery price and the final total

    const paylineButton = document.getElementById('payline-button');
    // Get the Payline payment button

    const submitButton = document.getElementById('submit-button');
    // Get the classic "Validate my order" submit button

    // If the Payline button is not found, log an error and stop the script
    if (!paylineButton) {
        console.error("Payline button not found in the DOM!");
        return;
    }

    // -----------------------------------------------------------
    // FUNCTION: Update totals and toggle payment buttons
    // -----------------------------------------------------------
    const updateTotals = (selectedRadio) => {
        // Retrieve the delivery price from the data-price attribute of the selected radio button
        // parseFloat converts the string to a decimal number
        // Default to 0 if data-price is not defined
        const price = parseFloat(selectedRadio.dataset.price || 0);

        // Update the displayed delivery price
        // toFixed(2) ensures exactly two decimal places
        deliveryPriceEl.textContent = price.toFixed(2);

        // Calculate and display the final total (base + delivery)
        const finalTotal = (baseTotal + price).toFixed(2);
        finalTotalEl.textContent = finalTotal;

        // Show or hide the Payline button depending on the delivery method
        if (selectedRadio.value === 'relay') {
            paylineButton.style.display = 'inline-block'; // Show Payline button
            if (submitButton) submitButton.style.display = 'none'; // Hide classic submit button
        } else {
            paylineButton.style.display = 'none'; // Hide Payline button
            if (submitButton) submitButton.style.display = 'inline-block'; // Show classic submit button
        }
    };

    // -----------------------------------------------------------
    // EVENT LISTENERS: Delivery method changes
    // -----------------------------------------------------------
    radios.forEach(radio => {
        // Add a change event listener to detect selection changes
        radio.addEventListener('change', function () {
            updateTotals(this); // Update totals with the selected radio button
        });

        // If a radio button is already checked on page load, update totals immediately
        if (radio.checked) {
            updateTotals(radio);
        }
    });
});
