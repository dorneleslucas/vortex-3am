import EntityBase from "./EntityBase.js";
export default class Treinos extends EntityBase {
    #aluno_id; #titulo; #objetivo; #observacoes; #data_inicio; #data_fim; #status;
    constructor({id=null,
        aluno_id,titulo="",
        objetivo=null,
        observacoes=null,
        data_inicio=null,
        data_fim=null,
        status="ativo"}={}){
            super(id);
            this.aluno_id=aluno_id;
            this.titulo=titulo;
            this.objetivo=objetivo;
            this.observacoes=observacoes;
            this.data_inicio=data_inicio;
            this.data_fim=data_fim;
            this.status=status;
        }

    get aluno_id(){
        return this.#aluno_id;
    }
     set aluno_id(v){
        this.#aluno_id=EntityBase.positiveId(v,"O aluno");
    }
    get titulo(){
        return this.#titulo;
    } 
    set titulo(v){
        this.#titulo=EntityBase.text(v,"O título");
    }
    get objetivo(){
        return this.#objetivo;
    } 
    set objetivo(v){
        this.#objetivo=EntityBase.text(v,"O objetivo",true);
    }
    get observacoes(){
        return this.#observacoes;
    } 
    set observacoes(v){
        this.#observacoes=EntityBase.text(v,"As observações",true);
    }
    get data_inicio(){
        return this.#data_inicio;
    } 
    set data_inicio(v){
        this.#data_inicio=this.#date(v,"A data inicial");
    }
    get data_fim(){
        return this.#data_fim;
    } 
    set data_fim(v){
        this.#data_fim=this.#date(v,"A data final");~
    }
    get status(){
        return this.#status;
    } 
    set status(v){
        const s=EntityBase.text(v,"O status");
        if(!["ativo","concluido","pausado"].includes(s))throw new TypeError("Status inválido");this.#status=s;}
    #date(v,label){if(v==null||v==="")return null;if(typeof v!=="string"||!/^\d{4}-\d{2}-\d{2}$/.test(v)||Number.isNaN(Date.parse(`${v}T00:00:00`)))throw new TypeError(`${label} inválida`);return v;}
    toJSON(){return {id:this.id,aluno_id:this.aluno_id,titulo:this.titulo,objetivo:this.objetivo,observacoes:this.observacoes,dataInicio:this.data_inicio,dataFim:this.data_fim,status:this.status};}
}
