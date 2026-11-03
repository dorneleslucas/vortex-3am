import HttpClientBase from "../classes/HttpClientBase.js";
export default class TreinosService extends HttpClientBase {
    constructor() { super(); const t = localStorage.getItem("vortex_token") || sessionStorage.getItem("token"); if (t) this.setAuthToken(t); }
    listar() {
        return this.get("/treinos");
    }
    buscar(id) {
        return this.get("/treinos/:id", { id });
    } criar(d) {
        return this.postForm("/treinos", d);
    } atualizar(id, d) {
        return this.putForm("/treinos/:id", d, { id });
    } excluir(id) {
        return this.delete("/treinos/:id", { id });
    }
}
