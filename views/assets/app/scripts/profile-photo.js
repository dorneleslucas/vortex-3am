import ImagesService from "../../_common/services/ImagesService.js";

const service = new ImagesService();
const input = document.getElementById("profile-photo-input");
const preview = document.getElementById("profile-photo-preview");
const current = document.getElementById("profile-photo");
const button = document.getElementById("profile-photo-submit");
const status = document.getElementById("profile-photo-status");
const userKey = "vortex_user";

function showCurrent(url) {
    if (!url) { current.hidden = true; return; }
    const parsed = new URL(url, window.location.href);
    const expectedPrefix = new URL("../api/uploads/users/", window.location.href).pathname;
    if (parsed.origin !== window.location.origin || !parsed.pathname.startsWith(expectedPrefix)) { current.hidden = true; return; }
    current.src = parsed.href;
    current.hidden = false;
}

try {
    const user = JSON.parse(localStorage.getItem(userKey) || "null");
    const existing = user?.photo;
    if (typeof existing === "string" && /^[a-f0-9]{32}\.(jpg|png|webp)$/i.test(existing)) showCurrent(`../api/uploads/users/${encodeURIComponent(existing)}`);
} catch { /* sessão inválida é tratada pelo guard da página */ }

input.addEventListener("change", () => {
    const file = input.files?.[0];
    status.textContent = "";
    if (!file) { preview.hidden = true; return; }
    if (!/^image\/(jpeg|png|webp)$/.test(file.type) || file.size > 5 * 1024 * 1024) {
        input.value = ""; preview.hidden = true;
        status.textContent = "Selecione JPEG, PNG ou WebP com até 5 MB.";
        return;
    }
    preview.src = URL.createObjectURL(file);
    preview.hidden = false;
});

button.addEventListener("click", async () => {
    const file = input.files?.[0];
    if (!file) { status.textContent = "Selecione uma imagem primeiro."; return; }
    button.disabled = true; status.textContent = "Enviando foto…";
    try {
        const response = await service.atualizarFotoPerfil(file);
        if (response.code !== 200 || !response.data?.url) throw new Error(response.message || "Não foi possível atualizar a foto.");
        showCurrent(response.data.url);
        const user = JSON.parse(localStorage.getItem(userKey) || "{}");
        user.photo = response.data.photo;
        localStorage.setItem(userKey, JSON.stringify(user));
        input.value = ""; preview.hidden = true;
        status.textContent = response.message || "Foto atualizada com sucesso.";
    } catch (error) { status.textContent = error.message; }
    finally { button.disabled = false; }
});
