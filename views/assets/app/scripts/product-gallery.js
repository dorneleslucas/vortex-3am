import ImagesService from "../../_common/services/ImagesService.js";

const api = new ImagesService();
const form = document.getElementById("gallery-form");
const productInput = document.getElementById("product-id");
const filesInput = document.getElementById("gallery-files");
const status = document.getElementById("gallery-status");
const list = document.getElementById("gallery-list");
const previews = document.getElementById("gallery-preview");
const loadButton = document.getElementById("load-gallery");
const uploadButton = document.getElementById("upload-images");
const lastProductKey = "vortex_product_gallery_id";
const previewUrls = new Set();

function selectedProductId() {
    const id = Number(productInput.value);
    return Number.isInteger(id) && id > 0 ? id : null;
}

function validFiles(files) {
    return [...files].filter(file =>
        ["image/jpeg", "image/png", "image/webp"].includes(file.type) && file.size > 0 && file.size <= 5 * 1024 * 1024
    );
}

function setBusy(busy, message) {
    loadButton.disabled = busy;
    uploadButton.disabled = busy;
    status.textContent = message;
}

function clearPreviews() {
    for (const url of previewUrls) URL.revokeObjectURL(url);
    previewUrls.clear();
    previews.replaceChildren();
}

function trustedImageUrl(value) {
    const url = new URL(value, window.location.href);
    const expected = new URL("../api/uploads/products/", window.location.href);
    const filename = url.pathname.split("/").pop();
    if (url.origin !== expected.origin || !url.pathname.startsWith(expected.pathname) || !/^[a-f0-9]{32}\.(jpg|png|webp)$/i.test(filename)) {
        throw new Error("A API retornou uma URL de imagem inválida.");
    }
    return url.href;
}

filesInput.addEventListener("change", () => {
    clearPreviews();
    const files = [...filesInput.files];
    const valid = validFiles(files);
    if (valid.length !== files.length) status.textContent = "Alguns arquivos foram rejeitados: escolha JPEG, PNG ou WebP de até 5 MB.";
    if (valid.length > 10) status.textContent = "Selecione no máximo 10 imagens por envio.";
    for (const file of valid.slice(0, 10)) {
        const url = URL.createObjectURL(file);
        previewUrls.add(url);
        const image = document.createElement("img");
        image.src = url;
        image.alt = `Prévia de ${file.name}`;
        image.width = 120;
        image.height = 100;
        image.style.objectFit = "cover";
        previews.append(image);
    }
});

async function loadGallery() {
    const productId = selectedProductId();
    if (!productId) {
        status.textContent = "Informe um ID de produto válido.";
        return;
    }
    localStorage.setItem(lastProductKey, String(productId));
    setBusy(true, "Carregando galeria…");
    list.replaceChildren();
    try {
        const response = await api.listarProduto(productId);
        if (response.code !== 200 || !Array.isArray(response.data)) throw new Error(response.message || "Falha ao carregar a galeria.");
        if (response.data.length === 0) {
            status.textContent = "Este produto ainda não tem imagens.";
            return;
        }
        status.textContent = `${response.data.length} imagem(ns) carregada(s).`;
        for (const imageData of response.data) {
            const card = document.createElement("article");
            card.className = "gallery-card";
            const image = document.createElement("img");
            image.src = trustedImageUrl(imageData.url);
            image.alt = imageData.alt_text || "Imagem do produto";
            image.className = "gallery-image";
            const caption = document.createElement("p");
            caption.textContent = imageData.alt_text || `Imagem ${imageData.id}`;
            const removeButton = document.createElement("button");
            removeButton.type = "button";
            removeButton.textContent = "Remover imagem";
            removeButton.addEventListener("click", async () => {
                if (!window.confirm("Remover esta imagem do produto?")) return;
                setBusy(true, "Removendo imagem…");
                try {
                    const result = await api.removerProduto(productId, imageData.id);
                    if (result.code !== 200) throw new Error(result.message || "Falha ao remover a imagem.");
                    await loadGallery();
                } catch (error) {
                    status.textContent = `Erro: ${error.message}`;
                } finally {
                    loadButton.disabled = false;
                    uploadButton.disabled = false;
                }
            });
            card.append(image, caption, removeButton);
            list.append(card);
        }
    } catch (error) {
        status.textContent = `Erro: ${error.message}`;
    } finally {
        loadButton.disabled = false;
        uploadButton.disabled = false;
    }
}

form.addEventListener("submit", event => {
    event.preventDefault();
    loadGallery();
});

uploadButton.addEventListener("click", async () => {
    const productId = selectedProductId();
    const selectedFiles = [...filesInput.files];
    const files = validFiles(selectedFiles);
    if (!productId) { status.textContent = "Informe um ID de produto válido."; return; }
    if (files.length !== selectedFiles.length || files.length === 0 || files.length > 10) {
        status.textContent = "Selecione de 1 a 10 imagens válidas (JPEG, PNG ou WebP, até 5 MB cada).";
        return;
    }

    setBusy(true, "Enviando imagens…");
    try {
        const response = await api.enviarProduto(productId, files, document.getElementById("gallery-alt").value.trim());
        if (response.code !== 201) throw new Error(response.message || "Falha ao enviar as imagens.");
        filesInput.value = "";
        clearPreviews();
        status.textContent = response.message || "Imagens enviadas com sucesso.";
        await loadGallery();
    } catch (error) {
        status.textContent = `Erro: ${error.message}`;
    } finally {
        loadButton.disabled = false;
        uploadButton.disabled = false;
    }
});

const queryProductId = new URLSearchParams(window.location.search).get("product_id");
const rememberedProductId = queryProductId || localStorage.getItem(lastProductKey);
if (rememberedProductId && Number.isInteger(Number(rememberedProductId)) && Number(rememberedProductId) > 0) {
    productInput.value = rememberedProductId;
    loadGallery();
}
