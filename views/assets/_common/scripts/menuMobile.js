const navigation = document.querySelector(".site-nav");
const menuButton = document.querySelector(".menu-button");
const menuLinks = document.querySelector("#main-menu");

if (navigation && menuButton && menuLinks) {
    menuButton.addEventListener("click", () => {
        const isOpen = navigation.classList.toggle("is-open");
        document.body.classList.toggle("menu-open", isOpen);
        menuButton.setAttribute("aria-expanded", String(isOpen));
    });

    menuLinks.addEventListener("click", (event) => {
        if (event.target.matches("a")) {
            navigation.classList.remove("is-open");
            document.body.classList.remove("menu-open");
            menuButton.setAttribute("aria-expanded", "false");
        }
    });
}
