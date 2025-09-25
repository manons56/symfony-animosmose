
//ce code sert à charger dynamiquement des sous-catégories en fonction d’une catégorie parente sélectionnée

// Attend que le DOM soit complètement chargé avant d'exécuter le code
document.addEventListener('DOMContentLoaded', () => {
    // Récupère les éléments du DOM : la catégorie parente et la sous-catégorie
    const parentSelect = document.querySelector('#Products_categoryParent');
    const subSelect = document.querySelector('#Products_category');

    // Si l'un des éléments n'existe pas, on arrête l'exécution du script
    if (!parentSelect || !subSelect) return;

    // Quand l'utilisateur change la sélection de la catégorie parente
    parentSelect.addEventListener('change', () => {
        // Récupère l'ID de la catégorie sélectionnée
        const parentId = parentSelect.value;

        // Si aucune catégorie n'est sélectionnée (valeur vide), on vide les sous-catégories et on quitte
        if (!parentId) {
            subSelect.innerHTML = '';
            return;
        }

        // Fait une requête HTTP pour récupérer les sous-catégories liées à cette catégorie
        fetch(`/admin/subcategories/${parentId}`)
            .then(res => res.json()) // Convertit la réponse en JSON
            .then(data => {
                // Vide d'abord le select des sous-catégories
                subSelect.innerHTML = '';

                // Crée une option vide ou par défaut au début du select
                const emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.text = '-- Choisir une sous-catégorie --';
                subSelect.appendChild(emptyOption);

                // Pour chaque sous-catégorie reçue, crée une option HTML dans le select
                data.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;      // ID de la sous-catégorie
                    option.text = sub.name;     // Nom à afficher dans la liste
                    subSelect.appendChild(option); // Ajoute l'option dans le select
                });
            });
    });
});
