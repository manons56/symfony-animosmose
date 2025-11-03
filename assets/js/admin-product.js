/**
 * dynamic-subcategories.js
 * --------------------------
 * This script dynamically populates the subcategory dropdown based on the
 * selected parent category. It is useful for forms where subcategories
 * depend on a parent category choice.
 *
 * Features:
 * 1. Listens for changes on the parent category select element.
 * 2. Fetches related subcategories via an AJAX request.
 * 3. Updates the subcategory select element with the fetched options.
 */

document.addEventListener('DOMContentLoaded', () => {
    // -----------------------------------------------------------
    // SELECT DOM ELEMENTS
    // -----------------------------------------------------------
    const parentSelect = document.querySelector('#Products_categoryParent'); // Parent category select
    const subSelect = document.querySelector('#Products_category');         // Subcategory select

    // -----------------------------------------------------------
    // SAFETY CHECK: Exit if elements are missing
    // -----------------------------------------------------------
    if (!parentSelect || !subSelect) return;

    // -----------------------------------------------------------
    // EVENT: When the parent category changes
    // -----------------------------------------------------------
    parentSelect.addEventListener('change', () => {
        const parentId = parentSelect.value; // Get selected parent category ID

        // If no category is selected, clear the subcategory dropdown
        if (!parentId) {
            subSelect.innerHTML = '';
            return;
        }

        // -----------------------------------------------------------
        // FETCH: Retrieve subcategories for the selected parent
        // -----------------------------------------------------------
        fetch(`/admin/subcategories/${parentId}`)
            .then(res => res.json()) // Convert response to JSON
            .then(data => {
                // Clear existing subcategory options
                subSelect.innerHTML = '';

                // Create a default empty option at the top
                const emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.text = '-- Choisir une sous-catégorie --';
                subSelect.appendChild(emptyOption);

                // Populate the subcategory dropdown with received data
                data.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;   // Set the subcategory ID
                    option.text = sub.name;  // Set the display name
                    subSelect.appendChild(option);
                });
            });
    });
});
