import ImagesService from "../../_common/services/ImagesService.js";

const api = new ImagesService();
const form = document.getElementById("gallery-form");
const idInput = document.getElementById("exercicio-id");
const filesInput = document.getElementById("gallery-files");
const status = document.getElementById("gallery-status");
const list = document.getElementById("gallery-list");
const previews = document.getElementById("gallery-preview");
const uploadButton = document.getElementById("upload-images");
let loading = false;

function trustedImageUrl(value) {
    const url = new URL(value, window.location.href);
    const expected = new URL("../api/uploads/exercicios/", window.location.href);
    if (url.origin !== expected.origin || !url.pathname.startsWith(expected.pathname) || !/[a-f0-9]{32}\.(jpg|png|webp)$/i.test(url.pathname.split("/").pop())) {
        throw new Error("A API retornou uma URL de imagem inválida.");
    }
    return url.href;
}

function exercicioId() { const id = Number(idInput.value); return Number.isInteger(id) && id > 0 ? id : null; }
function validFiles(files) { return [...files].filter(f => /^image\/(jpeg|png|webp)$/.test(f.type) && f.size <= 5 * 1024 * 1024); }
function setLoading(value, message) { loading = value; uploadButton.disabled = value; status.textContent = message; }

filesInput.addEventListener("change", () => {
    previews.replaceChildren();
    const files = [...filesInput.files];
    const valid = validFiles(files);
    if (valid.length !== files.length) status.textContent = "Alguns arquivos foram ignorados: escolha JPEG, PNG ou WebP de até 5 MB.";
    if (valid.length > 10) status.textContent = "Selecione no máximo 10 imagens por envio.";
    for (const file of valid.slice(0, 10)) {
        const img = document.createElement("img"); img.src = URL.createObjectURL(file); img.alt = `Prévia de ${file.name}`; img.width = 120; img.height = 100; img.style.objectFit = "cover"; previews.append(img);
    }
});

async function loadGallery() {
    const id = exercicioId();
    if (!id) { status.textContent = "Informe um ID de exercício válido."; return; }
    setLoading(true, "Carregando galeria…"); list.replaceChildren();
    try {
        const response = await api.listarExercicio(id);
        if (response.code !== 200 || !Array.isArray(response.data)) throw new Error(response.message || "Falha ao carregar a galeria.");
        if (!response.data.length) { status.textContent = "Este exercício ainda não tem imagens."; return; }
        status.textContent = `${response.data.length} imagem(ns) carregada(s).`;
        for (const image of response.data) {
            const card = document.createElement("article"); card.style.cssText = "border:1px solid #94a3b8;padding:12px;border-radius:8px";
            const img = document.createElement("img"); img.src = trustedImageUrl(image.url); img.alt = image.alt_text || "Imagem do exercício"; img.width = 180; img.height = 140; img.style.objectFit = "cover";
            const caption = document.createElement("p"); caption.textContent = image.alt_text || `Imagem ${image.id}`;
            const remove = document.createElement("button"); remove.type = "button"; remove.textContent = "Remover";
            remove.addEventListener("click", async () => {
                if (!confirm("Remover esta imagem?")) return;
                setLoading(true, "Removendo imagem…");
                try { const result = await api.removerExercicio(id, image.id); if (result.code !== 200) throw new Error(result.message || "Falha ao remover."); await loadGallery(); }
                catch (error) { status.textContent = error.message; }
                finally { uploadButton.disabled = false; loading = false; }
            });
            card.append(img, caption, remove); list.append(card);
        }
    } catch (error) { status.textContent = error.message; }
    finally { loading = false; uploadButton.disabled = false; }
}

form.addEventListener("submit", event => { event.preventDefault(); loadGallery(); });
uploadButton.addEventListener("click", async () => {
    const id = exercicioId(); const files = validFiles(filesInput.files);
    if (!id) { status.textContent = "Informe um ID de exercício válido."; return; }
    if (!files.length || files.length > 10) { status.textContent = "Selecione de 1 a 10 imagens válidas (JPEG, PNG ou WebP, até 5 MB cada)."; return; }
    setLoading(true, "Enviando imagens…");
    try {
        const result = await api.enviarExercicio(id, files, document.getElementById("gallery-alt").value.trim());
        if (result.code !== 201) throw new Error(result.message || "Falha ao enviar imagens.");
        filesInput.value = ""; previews.replaceChildren(); status.textContent = result.message || "Imagens enviadas."; await loadGallery();
    } catch (error) { status.textContent = error.message; }
    finally { loading = false; uploadButton.disabled = false; }
});
