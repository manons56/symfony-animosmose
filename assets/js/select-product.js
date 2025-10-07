
/*document.addEventListener('DOMContentLoaded', function () {

    // Récupération des données Twig via data-*
    const productDataEl = document.getElementById('product-data');
    const VARIANTS = JSON.parse(productDataEl.dataset.variants);
    const hasSize = productDataEl.dataset.hasSize === '1';
    const hasColor = productDataEl.dataset.hasColor === '1';
    const hasContenance = productDataEl.dataset.hasContenance === '1';

    // Sélecteurs basés sur ta structure HTML actuelle
    const sizeButtons = document.querySelectorAll('.variant-card.size-card');
    const colorButtons = document.querySelectorAll('.variant-card.color-card');
    const contenanceButtons = document.querySelectorAll('.variant-card:not(.size-card):not(.color-card)');

    const mainImage = document.getElementById('main-product-image');
    const addToCartForm = document.getElementById('add-to-cart-form');
    const addToCartBtn = document.querySelector('.btn-add-cart');
    const variantIdInput = document.getElementById('selected-variant-id');

    let selectedSize = null;
    let selectedColor = null;
    let selectedContenance = null;

    function getCurrentVariant() {
        return VARIANTS.find(variant => {
            return (!hasSize || variant.size === selectedSize) &&
                (!hasColor || variant.color === selectedColor) &&
                (!hasContenance || variant.contenance === selectedContenance);
        });
    }

    function updateAddToCartButton() {
        const variant = getCurrentVariant();
        const allSelected = (!hasSize || selectedSize) &&
            (!hasColor || selectedColor) &&
            (!hasContenance || selectedContenance);

        if (variant && allSelected) {
            addToCartBtn.disabled = false;
            addToCartBtn.textContent = 'Ajouter au panier';
            variantIdInput.value = variant.id;
            document.getElementById('variant-error').style.display = 'none';
        } else {
            addToCartBtn.disabled = true;
            addToCartBtn.textContent = 'Choisissez une option';
        }
    }

    function toggleSelected(button, group) {
        group.forEach(btn => btn.classList.remove('selected'));
        button.classList.add('selected');
    }

    contenanceButtons.forEach(button => {
        button.addEventListener('click', () => {
            selectedContenance = button.dataset.contenance;
            toggleSelected(button, contenanceButtons);
            updateAddToCartButton();
        });
    });

    sizeButtons.forEach(button => {
        button.addEventListener('click', () => {
            selectedSize = button.dataset.size;
            toggleSelected(button, sizeButtons);
            updateAddToCartButton();
        });
    });

    colorButtons.forEach(button => {
        button.addEventListener('click', () => {
            selectedColor = button.dataset.color;
            toggleSelected(button, colorButtons);
            updateAddToCartButton();
        });
    });

    addToCartForm?.addEventListener('submit', function (e) {
        const variant = getCurrentVariant();
        if (!variant) {
            e.preventDefault();
            document.getElementById('variant-error').style.display = 'block';
        }
    });

    updateAddToCartButton();
});
*/
