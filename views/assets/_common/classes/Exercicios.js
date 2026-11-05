
export default class Exercicios {
    #id; #nome; #categoria; #descricao; #ativo;
    constructor({ id = null, nome = "", categoria = null, descricao = null, ativo = 1 } = {}) {
        this.id = id;
        this.nome = nome;
        this.categoria = categoria;
        this.descricao = descricao;
        this.ativo = ativo;
    }
    get id() { return this.#id; } set id(v) { this.#id = Exercicios.positiveId(v, "O ID", true); }
    get nome() {
        return this.#nome;
    } set nome(v) {
        this.#nome = Exercicios.text(v, "O nome");
    }
    get categoria() {
        return this.#categoria;
    } set categoria(v) {
        this.#categoria = Exercicios.text(v, "A categoria", true);
    }
    get descricao() {
        return this.#descricao;
    } set descricao(v) {
        this.#descricao = Exercicios.text(v, "A descrição", true);
    }
    get ativo() {
        return this.#ativo;
    } set ativo(v) {
        this.#ativo = Exercicios.flag(v);
    }
    static positiveId(v, label, optional = false) { if (optional && (v == null || v === "")) return null; const n = Number(v); if (!Number.isInteger(n) || n < 1) throw new RangeError(`${label} deve ser inteiro positivo`); return n; }
    static text(v, label, optional = false) { if (optional && (v == null || v === "")) return null; if (typeof v !== "string" || !v.trim()) throw new TypeError(`${label} é obrigatório`); return v.trim(); }
    static flag(v) { const n = Number(v); if (![0, 1].includes(n)) throw new RangeError("O ativo deve ser 0 ou 1"); return n; }
    toJSON() {
        return {
            id: this.id,
            nome: this.nome,
            categoria: this.categoria,
            descricao: this.descricao,
            ativo: this.ativo
        };
    }
}
