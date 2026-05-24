const adminPanelButton = document.querySelector(".panel-menu-button");
const adminNavigation = document.querySelector(".mobile-panel-nav");
const adminSearch = document.querySelector(".admin-search");
const personalItems = document.querySelectorAll(".personal-list li");
const adminForm = document.querySelector(".admin-form");
const adminStatus = document.querySelector(".admin-status");

if (adminPanelButton && adminNavigation) {
    adminPanelButton.addEventListener("click", () => {
        const isOpen = adminNavigation.classList.toggle("is-open");
        adminPanelButton.setAttribute("aria-expanded", String(isOpen));
    });
}

if (adminSearch && personalItems.length > 0) {
    adminSearch.addEventListener("input", () => {
        const searchTerm = adminSearch.value.toLowerCase();

        personalItems.forEach((item) => {
            const itemText = item.textContent.toLowerCase();
            item.hidden = !itemText.includes(searchTerm);
        });
    });
}

if (adminForm && adminStatus) {
    adminForm.addEventListener("submit", (event) => {
        event.preventDefault();
        adminStatus.value = "Configuracoes salvas nesta demonstracao estatica.";
    });
}
