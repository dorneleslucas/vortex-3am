const panelButton = document.querySelector(".panel-menu-button");
const panelNavigation = document.querySelector(".mobile-panel-nav");
const studentSearch = document.querySelector(".student-search");
const studentItems = document.querySelectorAll(".student-list li");

if (panelButton && panelNavigation) {
    panelButton.addEventListener("click", () => {
        const isOpen = panelNavigation.classList.toggle("is-open");
        panelButton.setAttribute("aria-expanded", String(isOpen));
    });
}

if (studentSearch && studentItems.length > 0) {
    studentSearch.addEventListener("input", () => {
        const searchTerm = studentSearch.value.toLowerCase();

        studentItems.forEach((item) => {
            const itemText = item.textContent.toLowerCase();
            item.hidden = !itemText.includes(searchTerm);
        });
    });
}
