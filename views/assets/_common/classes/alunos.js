import EntityBase from "./EntityBase.js";
export default class Alunos extends EntityBase {
    #personal_id; #nome; #email; #telefone; #objetivo; #ativo;
    constructor({id=null,personal_id,nome="",email=null,telefone=null,objetivo=null,ativo=1}={}) { super(id); this.personal_id=personal_id; this.nome=nome; this.email=email; this.telefone=telefone; this.objetivo=objetivo; this.ativo=ativo; }
    get personal_id(){return this.#personal_id;} set personal_id(v){this.#personal_id=EntityBase.positiveId(v,"O personal_id");}
    get nome(){return this.#nome;} set nome(v){this.#nome=EntityBase.text(v,"O nome");}
    get email(){return this.#email;} set email(v){this.#email=EntityBase.text(v,"O e-mail",true); if(this.#email&&!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.#email))throw new TypeError("Informe um e-mail válido");}
    get telefone(){return this.#telefone;} set telefone(v){this.#telefone=EntityBase.text(v,"O telefone",true);}
    get objetivo(){return this.#objetivo;} set objetivo(v){this.#objetivo=EntityBase.text(v,"O objetivo",true);}
    get ativo(){return this.#ativo;} set ativo(v){this.#ativo=EntityBase.flag(v);}
    toJSON(){return {id:this.id,personal_id:this.personal_id,nome:this.nome,email:this.email,telefone:this.telefone,objetivo:this.objetivo,ativo:this.ativo};}
}
