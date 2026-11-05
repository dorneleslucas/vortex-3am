
export default class Users {
    #id; #type_id; #name; #email; #password; #photo; #active;
    constructor(
        { id = null, type_id = null, name = "", email = "", password = "", photo = null, active = 1 } = {}) {
        this.id = id;
        this.type_id = type_id;
        this.name = name;
        this.email = email;
        this.password = password;
        this.photo = photo;
        this.active = active;
    }
    get id() { return this.#id; } set id(v) { this.#id = Users.positiveId(v, "O ID", true); }
    get type_id() {
         return this.#type_id; 
        }
    set type_id(v) { 
        this.#type_id = v == null ? null : Users.positiveId(v, "O tipo");
    }
    get name() { 
        return this.#name; 
    }
    set name(v) {
         this.#name = Users.text(v, "O nome");
        }
    get email() {
         return this.#email; 
        }
    set email(v) { 
        this.#email = Users.text(v, "O e-mail"); if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.#email)) throw new TypeError("Informe um e-mail válido");
    }
    get password() { 
        return this.#password;
     } set password(v) {
         this.#password = Users.text(v, "A senha");
         }
    get photo() { 
        return this.#photo;
     }
    set photo(v) { 
        this.#photo = Users.text(v, "A foto", true);
     }
    get active() { 
        return this.#active; 
    }
    set active(v) {
         this.#active = Users.flag(v, "O active");
         }
    static positiveId(v, label, optional = false) { if (optional && (v == null || v === "")) return null; const n = Number(v); if (!Number.isInteger(n) || n < 1) throw new RangeError(`${label} deve ser inteiro positivo`); return n; }
    static text(v, label, optional = false) { if (optional && (v == null || v === "")) return null; if (typeof v !== "string" || !v.trim()) throw new TypeError(`${label} é obrigatório`); return v.trim(); }
    static flag(v, label) { const n = Number(v); if (![0, 1].includes(n)) throw new RangeError(`${label} deve ser 0 ou 1`); return n; }
    toJSON() {
        return { id: this.id, type_id: this.type_id, name: this.name, email: this.email, password: this.password, photo: this.photo, active: this.active };
    }
}


