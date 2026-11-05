import HttpClientBase from "../classes/HttpClientBase.js";

export default class ImagesService extends HttpClientBase {
    constructor() {
        super();
        const token = localStorage.getItem("vortex_token") || sessionStorage.getItem("token");
        if (token) this.setAuthToken(token);
    }

    atualizarFotoPerfil(file) {
        const form = new FormData();
        form.append("photo", file);
        return this.post("/users/photo", form);
    }

    listarExercicio(exercicioId) { return this.get("/exercicios/:exercicio_id/images", { exercicio_id: exercicioId }); }

    enviarExercicio(exercicioId, files, altText = "") {
        const form = new FormData();
        for (const file of files) form.append("images[]", file);
        if (altText) form.append("alt_text", altText);
        return this.post("/exercicios/:exercicio_id/images", form, { exercicio_id: exercicioId });
    }

    removerExercicio(exercicioId, imageId) {
        return this.delete("/exercicios/:exercicio_id/images/:image_id", { exercicio_id: exercicioId, image_id: imageId });
    }
}
