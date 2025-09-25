document.addEventListener('DOMContentLoaded', () => {
    const variantCards = document.querySelectorAll('.variant-card');
    const addToCartForm = document.getElementById('add-to-cart-form');

    if (variantCards.length === 0 || !addToCartForm) return; // sécurité si pas de variants

    const csrfTokenInput = document.getElementById('csrf-token');
    const addToCartButton = addToCartForm.querySelector('button');

    // URL template avec placeholder 'IDPLACEHOLDER' pour l'id du variant
    const urlTemplate = "{{ path('app_shop_cart_add', {'id': 'IDPLACEHOLDER'}) }}";

    let selectedVariantId = variantCards[0].dataset.variantId; // premier variant par défaut
    variantCards[0].classList.add('selected');

    // Empêche l'envoi du formulaire si le bouton est désactivé
    addToCartForm.addEventListener('submit', (e) => {
        if (addToCartButton.disabled) {
            e.preventDefault();
            alert("Ce produit est actuellement en rupture de stock.");
        }
    });

    // Mise à jour du formulaire lors du clic sur un variant
    variantCards.forEach(card => {
        card.addEventListener('click', () => {
            variantCards.forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            selectedVariantId = card.dataset.variantId;

            // Mise à jour de l'action du formulaire et du token CSRF
            addToCartForm.action = urlTemplate.replace('IDPLACEHOLDER', selectedVariantId);
            csrfTokenInput.value = "{{ csrf_token('cart_add_') }}" + selectedVariantId;
        });
    });
});
