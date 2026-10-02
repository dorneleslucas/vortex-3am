document.addEventListener("DOMContentLoaded", () => {
    fetchFAQs();
});

async function fetchFAQs() {
    const listFaqs = document.querySelector("#list-faqs");

    if (!listFaqs) return;

    try {
        // Caminho relativo: funciona no servidor local e no projeto publicado.
        const response = await fetch("api/faqs/list");

        if (!response.ok) {
            throw new Error(`Erro ao carregar FAQs: ${response.status}`);
        }

        const result = await response.json();
        const faqs = Array.isArray(result.data) ? result.data : [];

        listFaqs.innerHTML = "";

        if (!faqs.length) {
            listFaqs.textContent = "Nenhuma pergunta frequente encontrada.";
            return;
        }

        faqs.forEach((faq) => {
            const item = document.createElement("div");
            const question = document.createElement("dt");
            const button = document.createElement("button");
            const answer = document.createElement("dd");

            item.className = "faq-item";
            button.className = "faq-question";
            button.type = "button";
            button.setAttribute("aria-expanded", "false");
            button.textContent = faq.question;

            question.appendChild(button);
            answer.className = "faq-answer";
            answer.hidden = true;
            answer.textContent = faq.answer;

            button.addEventListener("click", () => {
                const isOpen = button.getAttribute("aria-expanded") === "true";
                button.setAttribute("aria-expanded", String(!isOpen));
                answer.hidden = isOpen;
            });

            item.append(question, answer);
            listFaqs.appendChild(item);
        });
    } catch (error) {
        console.error(error);
        listFaqs.textContent = "Não foi possível carregar as perguntas frequentes.";
    }
}
