document.addEventListener('DOMContentLoaded', () => {
    const parentSelect = document.querySelector('#Products_categoryParent');
    const subSelect = document.querySelector('#Products_category');

    if (!parentSelect || !subSelect) return;

    parentSelect.addEventListener('change', () => {
        const parentId = parentSelect.value;

        if (!parentId) {
            subSelect.innerHTML = '';
            return;
        }

        fetch(`/admin/subcategories/${parentId}`)
            .then(res => res.json())
            .then(data => {
                subSelect.innerHTML = '';
                const emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.text = '-- Choisir une sous-catégorie --';
                subSelect.appendChild(emptyOption);

                data.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.text = sub.name;
                    subSelect.appendChild(option);
                });
            });
    });
});
