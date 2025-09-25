//gère l'affichage du prix de livraison et du total final d'une commande en fonction du mode de livraison choisi par l'utilisateur.
// Il affiche également ou masque un bouton de paiement (payline-button) selon le mode de livraison sélectionné.

//Quand la page est chargée, le script :
//Récupère le total hors livraison.
//Attend une sélection (ou vérifie une sélection existante) de mode de livraison.
//Calcule le prix total (base + livraison).
// Affiche ou cache le bouton de paiement selon la méthode de livraison.


// Attendre que tout le DOM soit chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', function () {
    console.log('Script de livraison chargé !');

    // Sélectionne tous les boutons radio pour les méthodes de livraison
    const radios = document.querySelectorAll('input[name="delivery[delivery_method]"]');

    // Récupère le total de base (hors livraison), en supprimant les espaces et en convertissant en float
    const baseTotal = parseFloat(document.getElementById('base-total').textContent.replace(/\s/g, '')) || 0;

    // Récupère les éléments du DOM qui affichent le prix de livraison et le total final
    const deliveryPriceEl = document.getElementById('delivery-price');
    const finalTotalEl = document.getElementById('final-total');

    // Récupère le bouton de paiement (type Payline)
    const paylineButton = document.getElementById('payline-button');

    // Récupère le bouton "Valider ma commande" (formulaire classique)
    const submitButton = document.getElementById('submit-button');

    // Si le bouton de paiement est introuvable, afficher une erreur dans la console et arrêter le script
    if (!paylineButton) {
        console.error("Bouton Payline introuvable dans le DOM !");
        return;
    }

    // Fonction pour mettre à jour les totaux et le bouton de paiement selon la livraison choisie
    const updateTotals = (selectedRadio) => {
        // Récupère le prix de livraison depuis l'attribut data-price de l'option sélectionnée
        //selectedRadio.dataset.price : récupère la valeur de l’attribut data-price du bouton radio sélectionné.
        // parseFloat(...) : convertit cette valeur (au format texte) en nombre décimal.
        // || 0 : si jamais data-price n’est pas défini ou vide, on utilise 0 comme valeur par défaut.
        const price = parseFloat(selectedRadio.dataset.price || 0);

        // Met à jour l'affichage du prix de livraison
        //price.toFixed(2) : formate le prix pour afficher exactement deux décimales, même si le nombre est entier (ex. 5 devient 5.00).
        // deliveryPriceEl est un élément du DOM dans lequel on insère ce texte.
        deliveryPriceEl.textContent = price.toFixed(2);

        // Calcule et affiche le total final (base + livraison)
        //baseTotal : correspond au total du panier hors livraison, récupéré précédemment dans le script (parseFloat(document.getElementById('base-total').textContent)).
        // + price : on ajoute le prix de livraison.
        // .toFixed(2) : même logique, on garde deux décimales pour un affichage correct.
        const finalTotal = (baseTotal + price).toFixed(2);
        finalTotalEl.textContent = finalTotal + ' €';

        // Si le mode de livraison est "relay", afficher le bouton de paiement, sinon le masquer
        if (selectedRadio.value === 'relay') {
            paylineButton.style.display = 'inline-block'; // Affiche le bouton Payline
            if (submitButton) submitButton.style.display = 'none'; // Cache le bouton "Valider ma commande"
        } else {
            paylineButton.style.display = 'none'; // Masque le bouton Payline
            if (submitButton) submitButton.style.display = 'inline-block'; // Réaffiche le bouton "Valider ma commande"
        }
    };

    // Pour chaque bouton radio de livraison :
    radios.forEach(radio => {
        // Ajouter un écouteur d'événement pour détecter un changement de sélection
        radio.addEventListener('change', function () {
            updateTotals(this); // Met à jour les totaux avec le bouton sélectionné
        });

        // Si un bouton est déjà sélectionné au chargement, mettre à jour les totaux directement
        if (radio.checked) {
            updateTotals(radio);
        }
    });
});
