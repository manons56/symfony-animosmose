//On the dog pension page, in the home care section, there are toggles to view more information.
// This JS is used to open or close these toggles, to view or hide additional information.

// Selects all <p> elements in the document
// (we assume each <p> acts as a clickable "title")
const titles = document.querySelectorAll("p");

// Loops through each selected <p>
titles.forEach((title) => {
    // Adds a click event listener to each <p>
    title.addEventListener("click", () => {
        // Gets the next element in the DOM
        const nextList = title.nextElementSibling;

        // Checks if this element exists AND if it’s a <ul> list
        if (nextList && nextList.tagName === "UL") {
            // Toggles the "visible" class on this list
            // → if it’s present, it’s removed; otherwise, it’s added
            nextList.classList.toggle("visible");
        }
    });
});
