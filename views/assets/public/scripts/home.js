const leadForm = document.querySelector(".lead-form");
const formStatus = document.querySelector(".form-status");
const cursorDot = document.querySelector(".cursor-dot");
const cursorRing = document.querySelector(".cursor-ring");
const interactiveElements = document.querySelectorAll("a, button, input, .button");
const revealElements = document.querySelectorAll(".reveal");

if (leadForm && formStatus) {
    leadForm.addEventListener("submit", (event) => {
        event.preventDefault();
        formStatus.value = "Cadastro recebido. Em breve entraremos em contato.";
        leadForm.reset();
    });
}

if (cursorDot && cursorRing && window.matchMedia("(hover: hover) and (pointer: fine)").matches) {
    window.addEventListener("pointermove", (event) => {
        document.body.classList.add("cursor-ready");
        cursorDot.style.transform = `translate(${event.clientX}px, ${event.clientY}px) translate(-50%, -50%)`;
        cursorRing.style.transform = `translate(${event.clientX}px, ${event.clientY}px) translate(-50%, -50%)`;
    });

    interactiveElements.forEach((element) => {
        element.addEventListener("pointerenter", () => document.body.classList.add("cursor-active"));
        element.addEventListener("pointerleave", () => document.body.classList.remove("cursor-active"));
    });
}

if (revealElements.length) {
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("is-visible");
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.18 });

    revealElements.forEach((element) => revealObserver.observe(element));
}
