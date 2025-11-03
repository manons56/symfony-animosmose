//This JS allows you to search for a product in the store. You type letters into the search bar, and the corresponding products are displayed.
//This JavaScript adds interactivity to the website: it manages the mobile hamburger menu, filters and paginates products in real time, handles delivery method selection and checkout interactions, and ensures accessibility and smooth user experience throughout.

document.addEventListener('DOMContentLoaded', () => {
    // Wait until the entire DOM is loaded before running the script

    // -----------------------------------------------------------
    // SELECT DOM ELEMENTS
    // -----------------------------------------------------------
    const searchInput = document.getElementById('search');
    // Get the search input field by its ID "search"

    const productList = document.getElementById('product-list');
    // Get the container holding all product items

    const allProducts = Array.from(productList.querySelectorAll('.product-item'));
    // Get all product elements with class "product-item"
    // Convert the NodeList to an array to use array methods like filter and slice

    const paginationControls = document.getElementById('pagination-controls');
    // Get the container where pagination buttons will be rendered

    const productsPerPage = 16;
    // Number of products to display per page

    let filteredProducts = [...allProducts];
    // Dynamic array of products currently matching the search/filter
    // Initially contains all products

    let currentPage = 1;
    // Keep track of the currently displayed page

    // -----------------------------------------------------------
    // FUNCTION: Display a specific page of products
    // -----------------------------------------------------------
    function showPage(page) {
        currentPage = page;
        // Update the current page

        // Hide all products initially
        allProducts.forEach(p => p.style.display = 'none');

        // Calculate start and end index for products on the requested page
        const start = (page - 1) * productsPerPage;
        const end = start + productsPerPage;

        // Display only the filtered products belonging to the current page
        filteredProducts.slice(start, end).forEach(p => p.style.display = 'block');

        // Update pagination buttons based on the current page
        renderPagination();
    }

    // -----------------------------------------------------------
    // FUNCTION: Render pagination buttons dynamically
    // -----------------------------------------------------------
    function renderPagination() {
        paginationControls.innerHTML = '';
        // Clear the container to recreate buttons each time

        const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
        // Calculate the total number of pages based on filtered products

        if (totalPages <= 1) return;
        // Do not render pagination if there is only one page or none

        // Create a "Previous" button if not on the first page
        if (currentPage > 1) {
            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Précédent';
            prevBtn.classList.add('pagination-link');
            prevBtn.addEventListener('click', () => showPage(currentPage - 1));
            paginationControls.appendChild(prevBtn);
        }

        // Create a button for each page number
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.textContent = i;
            pageBtn.classList.add('pagination-link');
            if (i === currentPage) pageBtn.disabled = true; // Disable button for current page
            pageBtn.addEventListener('click', () => showPage(i));
            paginationControls.appendChild(pageBtn);
        }

        // Create a "Next" button if not on the last page
        if (currentPage < totalPages) {
            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Suivant';
            nextBtn.classList.add('pagination-link');
            nextBtn.addEventListener('click', () => showPage(currentPage + 1));
            paginationControls.appendChild(nextBtn);
        }
    }

    // -----------------------------------------------------------
    // LISTENER: Filter products when typing in search input
    // -----------------------------------------------------------
    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        // Get typed value and convert to lowercase for case-insensitive search

        // Filter products to only keep those whose name contains the search query
        filteredProducts = allProducts.filter(card => {
            const name = card.querySelector('.product-name').textContent.toLowerCase();
            return name.includes(query);
        });

        currentPage = 1; // Automatically return to first page on new search
        showPage(currentPage); // Display first page with filtered products
    });

    // -----------------------------------------------------------
    // INITIALIZATION: Display first page when page loads
    // -----------------------------------------------------------
    showPage(currentPage);
});
