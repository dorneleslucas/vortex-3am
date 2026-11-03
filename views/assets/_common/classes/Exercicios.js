import EntityBase from "./EntityBase.js";
export default class Exercicios extends EntityBase {
    #nome; #categoria; #descricao; #ativo;
    constructor({id=null,nome="",categoria=null,descricao=null,ativo=1}={}){super(id);this.nome=nome;this.categoria=categoria;this.descricao=descricao;this.ativo=ativo;}
    get nome(){return this.#nome;} set nome(v){this.#nome=EntityBase.text(v,"O nome");}
    get categoria(){return this.#categoria;} set categoria(v){this.#categoria=EntityBase.text(v,"A categoria",true);}
    get descricao(){return this.#descricao;} set descricao(v){this.#descricao=EntityBase.text(v,"A descrição",true);}
    get ativo(){return this.#ativo;} set ativo(v){this.#ativo=EntityBase.flag(v);}
    toJSON(){return {id:this.id,nome:this.nome,categoria:this.categoria,descricao:this.descricao,ativo:this.ativo};}
}
