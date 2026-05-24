const leadForm = document.querySelector(".lead-form");
const formStatus = document.querySelector(".form-status");

if (leadForm && formStatus) {
    leadForm.addEventListener("submit", (event) => {
        event.preventDefault();
        formStatus.value = "Cadastro recebido. Em breve entraremos em contato.";
        leadForm.reset();
    });
}
