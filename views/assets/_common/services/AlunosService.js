import HttpClientBase from "../classes/HttpClientBase.js";
export default class AlunosService extends HttpClientBase {
    constructor() { super(); const t = localStorage.getItem("vortex_token") || sessionStorage.getItem("token"); if (t) this.setAuthToken(t); }
    listar() {
        return this.get("/alunos/list");
    }
    buscar(id) {
        return this.get("/alunos/list/:id", { id });
    }
    listarPorPersonal(id) {
        return this.get("/alunos/personal/:personal_id", { personal_id: id });
    }
    criar(d) {
        return this.postForm("/alunos/", d);
    }

    atualizar(id, d) {
        return this.putForm("/alunos/:id", d, { id });
    }
    excluir(id) {
        return this.delete("/alunos/:id", { id });
    }
}
