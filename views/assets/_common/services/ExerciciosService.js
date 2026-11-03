import HttpClientBase from "../classes/HttpClientBase.js";
export default class ExerciciosService extends HttpClientBase {
    constructor() {
        super();
        const t = localStorage.getItem("vortex_token") || sessionStorage.getItem("token");
        if (t) this.setAuthToken(t);
    }
    listar() {
        return this.get("/exercicios/list");
    }
    buscar(id) {
        return this.get("/exercicios/list/:id", { id });
    }
    criar(d) {
        return this.postForm("/exercicios/", d);
    }
    atualizar(id, d) {
        return this.putForm("/exercicios/:id", d, { id });
    }
    excluir(id) {
        return this.delete("/exercicios/:id", { id });
    }
}
