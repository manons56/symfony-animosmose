
// ------------------------------------------------------
// Attente du chargement complet du DOM
// ------------------------------------------------------
// On encapsule tout le script dans DOMContentLoaded pour
// s'assurer que tous les éléments du DOM (boutons, formulaire, etc.)
// existent avant de manipuler quoi que ce soit.
document.addEventListener('DOMContentLoaded', function() {

    // Encapsulation immédiate pour éviter les variables globales
    (function() {

        // ------------------------------------------------------
        // --- Données variantes côté serveur -----------------
        // ------------------------------------------------------
        // Ces données sont injectées par Twig via window.PRODUCT_VARIANTS
        // Chaque variante contient :
        // id           -> identifiant unique
        // size         -> taille (M, L, XL)
        // color        -> couleur (Rouge, Bleu)
        // contenance   -> volume ou contenance (250ml, 500ml)
        // outOfStock   -> booléen si rupture de stock
        // price        -> prix en euros
        // url          -> URL du endpoint d'ajout au panier
        // token        -> token CSRF pour sécuriser le POST
        const VARIANTS = window.PRODUCT_VARIANTS || [];

        // ------------------------------------------------------
        // --- Sélection des éléments DOM essentiels ----------
        // ------------------------------------------------------
        const form = document.getElementById('add-to-cart-form');            // Formulaire principal
        const csrfInput = document.getElementById('csrf-token');            // Champ caché _token CSRF
        const selectedVariantHidden = document.getElementById('selected-variant-id'); // champ caché variantId
        const addToCartButton = form ? form.querySelector('button[type="submit"]') : null; // bouton submit
        const errorBox = document.getElementById('variant-error');          // conteneur d'erreur

        // ------------------------------------------------------
        // --- Récupération des boutons de sélection ---------
        // ------------------------------------------------------
        // NodeList converties en Array pour faciliter les forEach et méthodes Array
        const sizeButtons = Array.from(document.querySelectorAll('#sizes-container .variant-card'));
        const colorButtons = Array.from(document.querySelectorAll('#colors-container .variant-card'));
        const contenanceButtons = Array.from(document.querySelectorAll('.variants-container.contenance .variant-card'));

        // ------------------------------------------------------
        // --- Accessibilité clavier --------------------------
        // ------------------------------------------------------
        // Ajout d'un listener keydown sur tous les boutons
        // pour permettre la navigation au clavier via Enter ou Space
        [sizeButtons, colorButtons, contenanceButtons].forEach(list => {
            list.forEach(btn => {
                btn.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault(); // empêcher le scroll ou submit par défaut
                        btn.click();        // déclenche le clic comme si l'utilisateur avait cliqué
                    }
                });
            });
        });

        // ------------------------------------------------------
        // --- Flags pour savoir quels types d'attributs sont présents
        // ------------------------------------------------------
        const hasSize = sizeButtons.length > 0;
        const hasColor = colorButtons.length > 0;
        const hasContenance = contenanceButtons.length > 0;

        // ------------------------------------------------------
        // --- State interne pour gérer la sélection ----------
        // ------------------------------------------------------
        const state = {
            selectedSize: null,           // taille actuellement sélectionnée
            selectedColor: null,          // couleur actuellement sélectionnée
            selectedContenanceBtn: null,  // référence DOM du bouton contenance sélectionné
            selectedVariantId: null       // id de la variante actuellement choisie
        };

        // ------------------------------------------------------
        // --- Sécurité DOM : arrêt si éléments manquants -----
        // ------------------------------------------------------
        if (!form || !csrfInput || !addToCartButton) {
            console.warn('add-to-cart form or token/button not found — script stopped.');
            return; // stoppe le script si élément essentiel manquant
        }

        // ------------------------------------------------------
        // --- Fonctions utilitaires --------------------------
        // ------------------------------------------------------

        /**
         * findMatchingVariant(size, color)
         * -------------------------------
         * Parcourt VARIANTS pour trouver la première variante qui correspond
         * exactement à la taille et la couleur choisies.
         * Si le produit n'a pas de couleur, ne compare que la taille.
         * Ignore les variantes en rupture de stock.
         */
        function findMatchingVariant(size, color) {
            for (const v of VARIANTS) {
                const vs = v.size || '';
                const vc = v.color || '';

                // Si size et color sont définis
                if (size && color) {
                    if (vs === size && vc === color && !v.outOfStock) return v;
                }
                // Si size uniquement (pas de couleur)
                else if (size && !hasColor) {
                    if (vs === size && !v.outOfStock) return v;
                }
            }
            return null; // aucune variante trouvée
        }

        /**
         * setFormForVariant(variant)
         * --------------------------
         * Configure le formulaire pour pointer vers la variante choisie
         * - action du formulaire = variant.url
         * - _token = variant.token
         * - hidden input = variant.id
         * - active le bouton submit
         * - cache le message d'erreur
         */
        function setFormForVariant(variant) {
            if (!variant) return;

            // URL pour l'ajout au panier
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
         * ------------------
         * Réinitialise le formulaire à l'état "vide"
         * - action = #
         * - CSRF vide
         * - variantId vide
         * - bouton submit désactivé
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
         * ------------------
         * Supprime la classe CSS 'selected' de tous les boutons
         */
        function clearSelected(list) {
            list.forEach(el => el.classList.remove('selected'));
        }

        /**
         * markSelected(list, key, value)
         * -------------------------------
         * Ajoute la classe 'selected' à l'élément dont data-{key} === value
         */
        function markSelected(list, key, value) {
            if (!value) return;
            list.forEach(el => {
                if (el.dataset[key] === value) el.classList.add('selected');
            });
        }

        // ------------------------------------------------------
        // --- Gestion "contenance only" -----------------------
        // ------------------------------------------------------
        // Si le produit n'a que des contenances (pas de taille ni couleur)
        if (hasContenance && !hasSize && !hasColor) {

            // Désactive le bouton tant qu'aucune contenance n'est sélectionnée
            addToCartButton.disabled = true;

            // Ajout du clic pour chaque bouton contenance
            contenanceButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    if (btn.dataset.outofstock === '1') return; // ignore si outOfStock

                    clearSelected(contenanceButtons);
                    btn.classList.add('selected');

                    state.selectedContenanceBtn = btn;
                    state.selectedSize = null;
                    state.selectedColor = null;

                    const variantId = btn.dataset.variantId;
                    const token = btn.dataset.csrf;

                    form.setAttribute('method', 'post');
                    form.setAttribute('action', '/shop/cart/add/' + variantId);
                    csrfInput.value = token;
                    if (selectedVariantHidden) selectedVariantHidden.value = variantId;
                    state.selectedVariantId = variantId;

                    addToCartButton.disabled = false;
                    if (errorBox) errorBox.style.display = 'none';
                });
            });

            // Pré-sélection automatique si une seule contenance et pas outOfStock
            if (contenanceButtons.length === 1 && contenanceButtons[0].dataset.outofstock !== '1') {
                const btn = contenanceButtons[0];
                btn.classList.add('selected');
                const id = btn.dataset.variantId;
                form.setAttribute('action', '/shop/cart/add/' + id);
                csrfInput.value = btn.dataset.csrf;
                if (selectedVariantHidden) selectedVariantHidden.value = id;
                state.selectedVariantId = id;
                addToCartButton.disabled = false;
            }

            // Empêche l'envoi si aucune contenance sélectionnée
            form.addEventListener('submit', function(e) {
                if (!state.selectedVariantId) {
                    e.preventDefault();
                    if (errorBox) {
                        errorBox.textContent = '⚠️ Veuillez sélectionner une contenance.';
                        errorBox.style.display = 'block';
                    }
                    return false;
                }
                return true;
            });

            return; // On sort : logique contenance only terminée
        }

        // ------------------------------------------------------
        // --- Initialisation des disponibilités ---------------
        // ------------------------------------------------------
        function initAvailability() {
            // Pour chaque taille : si outOfStock = '1', on désactive visuellement et au clic
            sizeButtons.forEach(s => {
                if (s.dataset.outofstock === '1') { s.classList.add('variant-unavailable'); s.style.pointerEvents = 'none'; }
                else { s.classList.remove('variant-unavailable'); s.style.pointerEvents = ''; }
            });

            // Pour chaque couleur
            colorButtons.forEach(c => {
                if (c.dataset.outofstock === '1') { c.classList.add('variant-unavailable'); c.style.pointerEvents = 'none'; }
                else { c.classList.remove('variant-unavailable'); c.style.pointerEvents = ''; }
            });

            // Pour chaque contenance
            contenanceButtons.forEach(c => {
                if (c.dataset.outofstock === '1') { c.classList.add('variant-unavailable'); c.style.pointerEvents = 'none'; }
            });

            // Réinitialise le formulaire
            resetFormToEmpty();
        }
        initAvailability();


        // ------------------------------------------------------
        // --- Mise à jour des couleurs selon la taille -------
        // ------------------------------------------------------
        // Si une taille est choisie, seules les couleurs compatibles et disponibles
        // avec cette taille restent activables, les autres sont désactivées.
        function updateColorsForSize(size) {
            const allowed = new Set(); // Contiendra les couleurs valides
            VARIANTS.forEach(v => {
                // Vérifie si la variante correspond à la taille sélectionnée et n'est pas en rupture
                if ((v.size || '') === size && !v.outOfStock) allowed.add(v.color);
            });

            // Parcours tous les boutons couleur pour activer/désactiver selon allowed
            colorButtons.forEach(c => {
                if (allowed.has(c.dataset.color)) {
                    c.classList.remove('variant-unavailable'); // active visuel
                    c.style.pointerEvents = '';                 // active clic
                } else {
                    c.classList.add('variant-unavailable');    // grise visuel
                    c.style.pointerEvents = 'none';            // désactive clic
                }
            });
        }

        // ------------------------------------------------------
        // --- Mise à jour des tailles selon la couleur -------
        // ------------------------------------------------------
        // Même logique inversée : si couleur choisie, seules les tailles compatibles sont activables
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
        // --- Gestion clic sur un bouton taille ---------------
        // ------------------------------------------------------
        sizeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const size = btn.dataset.size;

                // Si l'utilisateur reclique sur la même taille, on désélectionne
                if (state.selectedSize === size) {
                    state.selectedSize = null;
                    clearSelected(sizeButtons);
                } else {
                    // Sinon, on sélectionne la nouvelle taille
                    state.selectedSize = size;
                    clearSelected(sizeButtons);
                    markSelected(sizeButtons, 'size', size);
                }

                // Toute contenance précédemment sélectionnée est annulée
                state.selectedContenanceBtn = null;

                // Réinitialise la variante sélectionnée
                state.selectedVariantId = null;
                if (selectedVariantHidden) selectedVariantHidden.value = '';
                csrfInput.value = '';
                form.setAttribute('action', '#');
                addToCartButton.disabled = true;

                // Si le produit a des couleurs, on met à jour celles disponibles
                if (hasColor) {
                    if (state.selectedSize) updateColorsForSize(state.selectedSize);
                    else initAvailability(); // reset si aucune taille sélectionnée
                } else {
                    // Si pas de couleurs, on tente de trouver directement la variante par taille
                    if (state.selectedSize) {
                        const variant = VARIANTS.find(v => (v.size || '') === state.selectedSize && !v.outOfStock);
                        if (variant) setFormForVariant(variant);
                    }
                }

                if (errorBox) errorBox.style.display = 'none'; // masque l'erreur
            });
        });

        // ------------------------------------------------------
        // --- Gestion clic sur un bouton couleur -------------
        // ------------------------------------------------------
        colorButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const color = btn.dataset.color;

                // Toggle visuel / logique
                if (state.selectedColor === color) {
                    state.selectedColor = null;
                    clearSelected(colorButtons);
                } else {
                    state.selectedColor = color;
                    clearSelected(colorButtons);
                    markSelected(colorButtons, 'color', color);
                }

                state.selectedContenanceBtn = null;

                // Réinitialise la variante sélectionnée
                state.selectedVariantId = null;
                if (selectedVariantHidden) selectedVariantHidden.value = '';
                csrfInput.value = '';
                form.setAttribute('action', '#');
                addToCartButton.disabled = true;

                // Si le produit a des tailles, on met à jour celles disponibles selon la couleur
                if (hasSize) {
                    if (state.selectedColor) updateSizesForColor(state.selectedColor);
                    else initAvailability(); // reset si aucune couleur sélectionnée
                }

                if (errorBox) errorBox.style.display = 'none';
            });
        });

        // ------------------------------------------------------
        // --- Refresh de la variante choisie ------------------
        // ------------------------------------------------------
        // Tente de retrouver la variante exacte selon la sélection size+color
        // et configure le formulaire si trouvée
        function refreshVariantFromSelections() {
            if (state.selectedSize && state.selectedColor) {
                const variant = findMatchingVariant(state.selectedSize, state.selectedColor);
                if (variant) {
                    setFormForVariant(variant);
                    return true;
                } else {
                    resetFormToEmpty(); // aucune variante compatible
                    return false;
                }
            }
            return false; // pas assez de sélection
        }

        // Appelle refreshVariantFromSelections() à chaque clic taille ou couleur
        sizeButtons.concat(colorButtons).forEach(btn => {
            btn.addEventListener('click', function() {
                refreshVariantFromSelections();
            });
        });

        // ------------------------------------------------------
        // --- Gestion submit du formulaire -------------------
        // ------------------------------------------------------
        form.addEventListener('submit', function(e) {
            // Si variante déjà configurée, on laisse passer
            if (state.selectedVariantId && form.getAttribute('action') && form.getAttribute('action') !== '#') {
                return true;
            }

            // 1) seulement size
            if (state.selectedSize && !hasColor) {
                const v = VARIANTS.find(x => (x.size || '') === state.selectedSize && !x.outOfStock);
                if (v) { setFormForVariant(v); return true; }
            }

            // 2) size+color
            if (state.selectedSize && state.selectedColor) {
                const v = findMatchingVariant(state.selectedSize, state.selectedColor);
                if (v) { setFormForVariant(v); return true; }
            }

            // 3) fallback : une seule variante en stock
            if (VARIANTS.length === 1 && !VARIANTS[0].outOfStock) {
                setFormForVariant(VARIANTS[0]);
                return true;
            }

            // Sinon : bloquer et afficher erreur
            e.preventDefault();
            if (errorBox) {
                errorBox.textContent = 'Merci de choisir une combinaison taille/couleur avant d\'ajouter au panier.';
                errorBox.style.display = 'block';
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        });

        // ------------------------------------------------------
        // --- Pré-sélection automatique si une seule variante
        // ------------------------------------------------------
        // Améliore l'UX pour produit sans options ou unique variante
        if (VARIANTS.length === 1 && !VARIANTS[0].outOfStock) {
            setFormForVariant(VARIANTS[0]);
            if (hasSize) clearSelected(sizeButtons), markSelected(sizeButtons, 'size', VARIANTS[0].size || '');
            if (hasColor) clearSelected(colorButtons), markSelected(colorButtons, 'color', VARIANTS[0].color || '');
            if (hasContenance && !hasSize && !hasColor) {
                contenanceButtons.forEach(b => { if (b.dataset.variantId == VARIANTS[0].id) b.classList.add('selected'); });
            }
        }

    })();
});

