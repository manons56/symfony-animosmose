// Sélectionne tous les éléments <p> du document
// (on suppose que chaque <p> agit comme un "titre" cliquable)
const titles = document.querySelectorAll("p");

// Parcourt chaque <p> sélectionné
titles.forEach((title) => {
    // Ajoute un écouteur d'événement sur le clic pour chaque <p>
    title.addEventListener("click", () => {
        // Récupère l’élément suivant dans le DOM
        const nextList = title.nextElementSibling;

        // Vérifie si cet élément existe ET si c’est bien une liste <ul>
        if (nextList && nextList.tagName === "UL") {
            // Bascule la classe "visible" sur cette liste
            // → si elle est présente, elle est retirée ; sinon, elle est ajoutée
            nextList.classList.toggle("visible");
        }
    });
});
