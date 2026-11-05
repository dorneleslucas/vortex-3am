
export default class Alunos {
    #id; #personal_id; #nome; #email; #telefone; #objetivo; #ativo;
    constructor({ id = null, personal_id, nome = "", email = null, telefone = null, objetivo = null, ativo = 1 } = {}) {
        this.id = id;
        this.personal_id = personal_id;
        this.nome = nome;
        this.email = email;
        this.telefone = telefone;
        this.objetivo = objetivo;
        this.ativo = ativo;
    }
    get id() { return this.#id; } set id(v) { this.#id = Alunos.positiveId(v, "O ID", true); }
    get personal_id() {
        return this.#personal_id;
    } set personal_id(v) {
        this.#personal_id = Alunos.positiveId(v, "O personal_id");
    }
    get nome() {
        return this.#nome;
    } set nome(v) {
        this.#nome = Alunos.text(v, "O nome");
    }
    get email() {
        return this.#email;
    } set email(v) {
        this.#email = Alunos.text(v, "O e-mail", true);
        if (this.#email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.#email)) throw new TypeError("Informe um e-mail válido");
    }
    get telefone() {
        return this.#telefone;
    } set telefone(v) {
        this.#telefone = Alunos.text(v, "O telefone", true);
    }
    get objetivo() {
        return this.#objetivo;
    }
    set objetivo(v) {
        this.#objetivo = Alunos.text(v, "O objetivo", true);
    }
    get ativo() {
        return this.#ativo;
    }
    set ativo(v) {
        this.#ativo = Alunos.flag(v);
    }
    static positiveId(v, label, optional = false) { if (optional && (v == null || v === "")) return null; const n = Number(v); if (!Number.isInteger(n) || n < 1) throw new RangeError(`${label} deve ser inteiro positivo`); return n; }
    static text(v, label, optional = false) { if (optional && (v == null || v === "")) return null; if (typeof v !== "string" || !v.trim()) throw new TypeError(`${label} é obrigatório`); return v.trim(); }
    static flag(v) { const n = Number(v); if (![0, 1].includes(n)) throw new RangeError("O ativo deve ser 0 ou 1"); return n; }
    toJSON() {
        return {
            id: this.id,
            personal_id: this.personal_id,
            nome: this.nome,
            email: this.email,
            telefone: this.telefone,
            objetivo: this.objetivo,
            ativo: this.ativo
        };
    }
}
