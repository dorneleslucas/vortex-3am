export default class EntityBase {
    #id;
    constructor(id = null) { this.id = id; }
    get id() { return this.#id; }
    set id(value) { this.#id = EntityBase.positiveId(value, "O id", true); }
    static positiveId(value, label, optional = false) {
        if (optional && (value === null || value === "" || value === undefined)) return null;
        const id = Number(value);
        if (!Number.isInteger(id) || id < 1) throw new RangeError(`${label} deve ser um inteiro positivo`);
        return id;
    }
    static text(value, label, optional = false) {
        if (optional && (value === null || value === "" || value === undefined)) return null;
        if (typeof value !== "string" || !value.trim()) throw new TypeError(`${label} é obrigatório`);
        return value.trim();
    }
    static flag(value, label = "O ativo") {
        const flag = Number(value);
        if (flag !== 0 && flag !== 1) throw new RangeError(`${label} deve ser 0 ou 1`);
        return flag;
    }
}
