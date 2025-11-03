// ======================================================
// PRODUCT VARIANT SELECTION SCRIPT (DETAILED DOCUMENTATION)
// ======================================================

// ------------------------------------------------------
// WAIT FOR COMPLETE DOM LOADING
// ------------------------------------------------------
// We wrap the entire script in `DOMContentLoaded` to ensure that all DOM elements
// (buttons, form inputs, containers, etc.) are fully available before we try
// to access or manipulate them. This avoids runtime errors and ensures correct behavior.
document.addEventListener('DOMContentLoaded', function() {

    // ------------------------------------------------------
    // USE IIFE TO AVOID GLOBAL SCOPE POLLUTION
    // ------------------------------------------------------
    // By using an Immediately Invoked Function Expression, all variables and functions
    // are contained within this function scope and will not leak into the global scope,
    // preventing potential conflicts with other scripts on the page.
    (function() {

        // ------------------------------------------------------
        // SERVER-SIDE VARIANT DATA
        // ------------------------------------------------------
        // The variants are injected by the server (via Twig) into `window.PRODUCT_VARIANTS`.
        // Each variant has:
        // - id: unique identifier
        // - size: e.g., M, L, XL
        // - color: e.g., Red, Blue
        // - contenance: volume/capacity, e.g., 250ml
        // - outOfStock: boolean indicating if variant is unavailable
        // - price: in euros
        // - url: endpoint to add this variant to cart
        // - token: CSRF token to secure the form POST request
        const VARIANTS = window.PRODUCT_VARIANTS || [];

        // ------------------------------------------------------
        // ESSENTIAL DOM ELEMENTS
        // ------------------------------------------------------
        // Select elements that are essential for variant selection and form submission
        const form = document.getElementById('add-to-cart-form');            // Main form
        const csrfInput = document.getElementById('csrf-token');            // Hidden CSRF token input
        const selectedVariantHidden = document.getElementById('selected-variant-id'); // Hidden input for selected variant ID
        const addToCartButton = form ? form.querySelector('button[type="submit"]') : null; // Submit button
        const errorBox = document.getElementById('variant-error');          // Container for displaying validation errors

        // ------------------------------------------------------
        // VARIANT SELECTION BUTTONS
        // ------------------------------------------------------
        // Convert NodeLists to Arrays for easier iteration and manipulation
        const sizeButtons = Array.from(document.querySelectorAll('#sizes-container .variant-card'));
        const colorButtons = Array.from(document.querySelectorAll('#colors-container .variant-card'));
        const contenanceButtons = Array.from(document.querySelectorAll('.variants-container.contenance .variant-card'));

        // ------------------------------------------------------
        // KEYBOARD ACCESSIBILITY
        // ------------------------------------------------------
        // Add keydown listeners to allow users to select variants via keyboard.
        // Enter or Space triggers the button click programmatically.
        [sizeButtons, colorButtons, contenanceButtons].forEach(list => {
            list.forEach(btn => {
                btn.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault(); // Prevent default behavior (scrolling or submitting)
                        btn.click();        // Trigger click to select variant
                    }
                });
            });
        });

        // ------------------------------------------------------
        // DETECT EXISTING ATTRIBUTE TYPES
        // ------------------------------------------------------
        const hasSize = sizeButtons.length > 0;
        const hasColor = colorButtons.length > 0;
        const hasContenance = contenanceButtons.length > 0;

        // ------------------------------------------------------
        // INTERNAL STATE
        // ------------------------------------------------------
        // Keep track of the current selection so we can update the form correctly
        const state = {
            selectedSize: null,
            selectedColor: null,
            selectedContenanceBtn: null,
            selectedVariantId: null
        };

        // ------------------------------------------------------
        // STOP SCRIPT IF ESSENTIAL ELEMENTS MISSING
        // ------------------------------------------------------
        if (!form || !csrfInput || !addToCartButton) {
            console.warn('Essential elements not found. Script stopped.');
            return;
        }

        // ------------------------------------------------------
        // UTILITY FUNCTIONS
        // ------------------------------------------------------

        /**
         * findMatchingVariant(size, color)
         * Finds the first available variant matching the selected size and color.
         * Ignores out-of-stock variants.
         * Used to determine which variant ID to submit in the form.
         */
        function findMatchingVariant(size, color) {
            for (const v of VARIANTS) {
                const vs = v.size || '';
                const vc = v.color || '';
                if (size && color) {
                    if (vs === size && vc === color && !v.outOfStock) return v;
                } else if (size && !hasColor) {
                    if (vs === size && !v.outOfStock) return v;
                }
            }
            return null;
        }

        /**
         * setFormForVariant(variant)
         * Configures the form for the selected variant:
         * - Updates form action to variant URL
         * - Sets CSRF token
         * - Updates hidden input with variant ID
         * - Enables submit button
         * - Hides error messages
         */
        function setFormForVariant(variant) {
            if (!variant) return;
            const url = variant.url && variant.url !== '' ? variant.url : '/shop/cart/add/' + variant.id;
            const token = variant.token || variant.csrf || '';
            form.setAttribute('method', 'post');
            form.setAttribute('action', url);
            csrfInput.value = token;
            if (selectedVariantHidden) selectedVariantHidden.value = variant.id;
            state.selectedVariantId = variant.id;
            addToCartButton.disabled = false;
            if (errorBox) errorBox.style.display = 'none';
        }

        /**
         * resetFormToEmpty()
         * Resets the form to a "blank" state:
         * - Action = #
         * - Empty CSRF token
         * - Empty variant ID
         * - Disables submit button
         */
        function resetFormToEmpty() {
            form.setAttribute('action', '#');
            csrfInput.value = '';
            if (selectedVariantHidden) selectedVariantHidden.value = '';
            state.selectedVariantId = null;
            addToCartButton.disabled = true;
        }

        /**
         * clearSelected(list)
         * Removes the 'selected' CSS class from all elements in a given list.
         */
        function clearSelected(list) {
            list.forEach(el => el.classList.remove('selected'));
        }

        /**
         * markSelected(list, key, value)
         * Adds 'selected' class to the element whose data-{key} equals value.
         * Provides visual feedback for the selected option.
         */
        function markSelected(list, key, value) {
            if (!value) return;
            list.forEach(el => {
                if (el.dataset[key] === value) el.classList.add('selected');
            });
        }

        // ------------------------------------------------------
        // "CONTENANCE ONLY" PRODUCTS
        // ------------------------------------------------------
        // For products that only have volume options (no size or color)
        if (hasContenance && !hasSize && !hasColor) {
            addToCartButton.disabled = true;

            contenanceButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    if (btn.dataset.outofstock === '1') return; // Skip unavailable variant
                    clearSelected(contenanceButtons);
                    btn.classList.add('selected');
                    state.selectedContenanceBtn = btn;
                    state.selectedVariantId = btn.dataset.variantId;
                    form.setAttribute('action', '/shop/cart/add/' + btn.dataset.variantId);
                    csrfInput.value = btn.dataset.csrf;
                    if (selectedVariantHidden) selectedVariantHidden.value = btn.dataset.variantId;
                    addToCartButton.disabled = false;
                    if (errorBox) errorBox.style.display = 'none';
                });
            });

            // Auto-select if only one available option
            if (contenanceButtons.length === 1 && contenanceButtons[0].dataset.outofstock !== '1') {
                const btn = contenanceButtons[0];
                btn.classList.add('selected');
                form.setAttribute('action', '/shop/cart/add/' + btn.dataset.variantId);
                csrfInput.value = btn.dataset.csrf;
                if (selectedVariantHidden) selectedVariantHidden.value = btn.dataset.variantId;
                state.selectedVariantId = btn.dataset.variantId;
                addToCartButton.disabled = false;
            }

            // Prevent submission if no selection
            form.addEventListener('submit', function(e) {
                if (!state.selectedVariantId) {
                    e.preventDefault();
                    if (errorBox) {
                        errorBox.textContent = '⚠️ Please select a volume option.';
                        errorBox.style.display = 'block';
                    }
                    return false;
                }
                return true;
            });

            return; // Exit logic for "contenance-only" products
        }

        // ------------------------------------------------------
        // INITIALIZE AVAILABILITY
        // ------------------------------------------------------
        // Visually disables buttons that are out of stock
        function initAvailability() {
            sizeButtons.forEach(s => {
                if (s.dataset.outofstock === '1') { s.classList.add('variant-unavailable'); s.style.pointerEvents = 'none'; }
                else { s.classList.remove('variant-unavailable'); s.style.pointerEvents = ''; }
            });
            colorButtons.forEach(c => {
                if (c.dataset.outofstock === '1') { c.classList.add('variant-unavailable'); c.style.pointerEvents = 'none'; }
                else { c.classList.remove('variant-unavailable'); c.style.pointerEvents = ''; }
            });
            contenanceButtons.forEach(c => {
                if (c.dataset.outofstock === '1') { c.classList.add('variant-unavailable'); c.style.pointerEvents = 'none'; }
            });
            resetFormToEmpty();
        }
        initAvailability();

        // ------------------------------------------------------
        // UPDATE COLORS BASED ON SIZE
        // ------------------------------------------------------
        // Only colors compatible with the selected size are enabled
        function updateColorsForSize(size) {
            const allowed = new Set();
            VARIANTS.forEach(v => {
                if ((v.size || '') === size && !v.outOfStock) allowed.add(v.color);
            });
            colorButtons.forEach(c => {
                if (allowed.has(c.dataset.color)) {
                    c.classList.remove('variant-unavailable');
                    c.style.pointerEvents = '';
                } else {
                    c.classList.add('variant-unavailable');
                    c.style.pointerEvents = 'none';
                }
            });
        }

        // ------------------------------------------------------
        // UPDATE SIZES BASED ON COLOR
        // ------------------------------------------------------
        // Only sizes compatible with the selected color are enabled
        function updateSizesForColor(color) {
            const allowed = new Set();
            VARIANTS.forEach(v => {
                if ((v.color || '') === color && !v.outOfStock) allowed.add(v.size);
            });
            sizeButtons.forEach(s => {
                if (allowed.has(s.dataset.size)) {
                    s.classList.remove('variant-unavailable');
                    s.style.pointerEvents = '';
                } else {
                    s.classList.add('variant-unavailable');
                    s.style.pointerEvents = 'none';
                }
            });
        }

        // ------------------------------------------------------
        // SIZE BUTTON CLICK LOGIC
        // ------------------------------------------------------
        // Handles selection, deselection, updating dependent options,
        // resetting form, and hiding errors
        sizeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const size = btn.dataset.size;
                if (state.selectedSize === size) {
                    state.selectedSize = null;
                    clearSelected(sizeButtons);
                } else {
                    state.selectedSize = size;
                    clearSelected(sizeButtons);
                    markSelected(sizeButtons, 'size', size);
                }
                state.selectedContenanceBtn = null;
                state.selectedVariantId = null;
                if (selectedVariantHidden) selectedVariantHidden.value = '';
                csrfInput.value = '';
                form.setAttribute('action', '#');
                addToCartButton.disabled = true;
                if (hasColor) {
                    if (state.selectedSize) updateColorsForSize(state.selectedSize);
                    else initAvailability();
                } else {
                    if (state.selectedSize) {
                        const variant = VARIANTS.find(v => (v.size || '') === state.selectedSize && !v.outOfStock);
                        if (variant) setFormForVariant(variant);
                    }
                }
                if (errorBox) errorBox.style.display = 'none';
            });
        });

        // ------------------------------------------------------
        // COLOR BUTTON CLICK LOGIC
        // ------------------------------------------------------
        // Same logic as size buttons: toggles selection, updates dependent sizes
        colorButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const color = btn.dataset.color;
                if (state.selectedColor === color) {
                    state.selectedColor = null;
                    clearSelected(colorButtons);
                } else {
                    state.selectedColor = color;
                    clearSelected(colorButtons);
                    markSelected(colorButtons, 'color', color);
                }
                state.selectedContenanceBtn = null;
                state.selectedVariantId = null;
                if (selectedVariantHidden) selectedVariantHidden.value = '';
                csrfInput.value = '';
                form.setAttribute('action', '#');
                addToCartButton.disabled = true;
                if (hasSize) {
                    if (state.selectedColor) updateSizesForColor(state.selectedColor);
                    else initAvailability();
                }
                if (errorBox) errorBox.style.display = 'none';
            });
        });

        // ------------------------------------------------------
        // REFRESH VARIANT BASED ON SELECTIONS
        // ------------------------------------------------------
        function refreshVariantFromSelections() {
            if (state.selectedSize && state.selectedColor) {
                const variant = findMatchingVariant(state.selectedSize, state.selectedColor);
                if (variant) { setFormForVariant(variant); return true; }
                else { resetFormToEmpty(); return false; }
            }
            return false;
        }

        sizeButtons.concat(colorButtons).forEach(btn => {
            btn.addEventListener('click', function() {
                refreshVariantFromSelections();
            });
        });

        // ------------------------------------------------------
        // FORM SUBMISSION VALIDATION
        // ------------------------------------------------------
        form.addEventListener('submit', function(e) {
            if (state.selectedVariantId && form.getAttribute('action') && form.getAttribute('action') !== '#') {
                return true; // Valid selection, submit form
            }
            if (state.selectedSize && !hasColor) {
                const v = VARIANTS.find(x => (x.size || '') === state.selectedSize && !x.outOfStock);
                if (v) { setFormForVariant(v); return true; }
            }
            if (state.selectedSize && state.selectedColor) {
                const v = findMatchingVariant(state.selectedSize, state.selectedColor);
                if (v) { setFormForVariant(v); return true; }
            }
            if (VARIANTS.length === 1 && !VARIANTS[0].outOfStock) {
                setFormForVariant(VARIANTS[0]);
                return true;
            }
            e.preventDefault(); // Block form submission
            if (errorBox) {
                errorBox.textContent = 'Please choose a valid size/color combination before adding to cart.';
                errorBox.style.display = 'block';
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        });

        // ------------------------------------------------------
        // AUTO-PRESELECT SINGLE VARIANT
        // ------------------------------------------------------
        // Improves UX for products with only one variant
        if (VARIANTS.length === 1 && !VARIANTS[0].outOfStock) {
            setFormForVariant(VARIANTS[0]);

            // If sizes exist, mark the single variant as selected
            if (hasSize) {
                clearSelected(sizeButtons);
                markSelected(sizeButtons, 'size', VARIANTS[0].size || '');
            }

            // If colors exist, mark the single variant as selected
            if (hasColor) {
                clearSelected(colorButtons);
                markSelected(colorButtons, 'color', VARIANTS[0].color || '');
            }

            // If only contenance exists without size or color, select it
            if (hasContenance && !hasSize && !hasColor) {
                contenanceButtons.forEach(b => {
                    if (b.dataset.variantId == VARIANTS[0].id) {
                        b.classList.add('selected');
                    }
                });
            }
        }

    })();
});


