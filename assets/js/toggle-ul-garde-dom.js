// Sélectionne tous les <p> et <ul> correspondants
const titles = document.querySelectorAll("p");

titles.forEach((title) => {
    title.addEventListener("click", () => {
        const nextList = title.nextElementSibling;
        if (nextList && nextList.tagName === "UL") {
            nextList.classList.toggle("visible");
        }
    });
});
