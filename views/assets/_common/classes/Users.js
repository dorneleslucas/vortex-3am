import EntityBase from "./EntityBase.js";
export default class Users extends EntityBase {
    #type_id; #name; #email; #password; #photo; #active;
    constructor(
        { id = null, type_id = null, name = "", email = "", password = "", photo = null, active = 1 } = {}) {
        super(id);
        this.type_id = type_id;
        this.name = name;
        this.email = email;
        this.password = password;
        this.photo = photo;
        this.active = active;
    }
    get type_id() {
         return this.#type_id; 
        }
    set type_id(v) { 
        this.#type_id = v == null ? null : EntityBase.positiveId(v, "O tipo"); 
    }
    get name() { 
        return this.#name; 
    }
    set name(v) {
         this.#name = EntityBase.text(v, "O nome"); 
        }
    get email() {
         return this.#email; 
        }
    set email(v) { 
        this.#email = EntityBase.text(v, "O e-mail"); if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.#email)) throw new TypeError("Informe um e-mail válido"); 
    }
    get password() { 
        return this.#password;
     } set password(v) {
         this.#password = EntityBase.text(v, "A senha");
         }
    get photo() { 
        return this.#photo;
     }
    set photo(v) { 
        this.#photo = EntityBase.text(v, "A foto", true);
     }
    get active() { 
        return this.#active; 
    }
    set active(v) {
         this.#active = EntityBase.flag(v, "O active");
         }
    toJSON() {
        return { id: this.id, type_id: this.type_id, name: this.name, email: this.email, password: this.password, photo: this.photo, active: this.active };
    }
}


