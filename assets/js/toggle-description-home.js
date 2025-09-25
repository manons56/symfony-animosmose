document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('.toggle-arrow');

    toggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            const description = toggle.previousElementSibling;
            description.classList.toggle('expanded');
            toggle.classList.toggle('expanded');
        });
    });
});
