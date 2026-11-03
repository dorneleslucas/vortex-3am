import Alunos from "../../_common/classes/alunos.js";
import Exercicios from "../../_common/classes/Exercicios.js";
import Treinos from "../../_common/classes/Treinos.js";
import AlunosService from "../../_common/services/AlunosService.js";
import ExerciciosService from "../../_common/services/ExerciciosService.js";
import TreinosService from "../../_common/services/TreinosService.js";
import "../../_common/scripts/prototypes.js";

const defs = {
    alunos: { label:"Alunos", entity:Alunos, service:new AlunosService(), fields:[
        ["personal_id","ID do personal","number",true],["nome","Nome","text",true],["email","E-mail","email"],["telefone","Telefone","tel"],["objetivo","Objetivo","text"],["ativo","Ativo","checkbox"]
    ], columns:[["id","ID"],["nome","Nome"],["email","E-mail"],["telefone","Telefone"],["objetivo","Objetivo"]] },
    treinos: { label:"Treinos", entity:Treinos, service:new TreinosService(), fields:[
        ["aluno_id","ID do aluno","number",true],["titulo","Título","text",true],["objetivo","Objetivo","text"],["observacoes","Observações","textarea"],["data_inicio","Data inicial","date"],["data_fim","Data final","date"],["status","Status","select",true,["ativo","concluido","pausado"]]
    ], columns:[["id","ID"],["titulo","Treino"],["aluno_nome","Aluno"],["objetivo","Objetivo"],["status","Status"]] },
    exercicios: { label:"Exercícios", entity:Exercicios, service:new ExerciciosService(), fields:[
        ["nome","Nome","text",true],["categoria","Categoria","text"],["descricao","Descrição","textarea"],["ativo","Ativo","checkbox"]
    ], columns:[["id","ID"],["nome","Nome"],["categoria","Categoria"],["descricao","Descrição"],["ativo","Ativo"]] }
};

class GestaoManager {
    #tipo="alunos"; #registros=[]; #loading=false; #erroCarregamento=null;
    constructor(){this.#bind();this.#tab("alunos");}
    #bind(){
        document.querySelectorAll("[data-entity]").forEach(b=>b.addEventListener("click",()=>this.#tab(b.dataset.entity)));
        document.getElementById("btn-novo-aluno").addEventListener("click",()=>this.#abrir());
        document.getElementById("btn-salvar-aluno").addEventListener("click",()=>this.#salvar());
        document.getElementById("btn-cancelar-aluno").addEventListener("click",()=>this.#fechar());
        document.querySelector(".close").addEventListener("click",()=>this.#fechar());
        document.getElementById("form-aluno").addEventListener("submit",e=>{e.preventDefault();this.#salvar();});
        document.getElementById("tbody-alunos").addEventListener("click",e=>{const b=e.target.closest("button[data-action]");if(!b)return;if(b.dataset.action==="edit")this.#editar(b.dataset.id);else this.#excluir(b.dataset.id);});
        document.querySelector("#modal-aluno").addEventListener("click",e=>{if(e.target.id==="modal-aluno")this.#fechar();});
    }
    async #tab(tipo){this.#tipo=tipo;document.querySelectorAll("[data-entity]").forEach(b=>b.classList.toggle("is-active",b.dataset.entity===tipo));document.getElementById("titulo-entidade").textContent=defs[tipo].label;document.getElementById("btn-novo-aluno").textContent=`+ Novo ${tipo.toTitleCase().replace(/s$/,"")}`;await this.#carregar();}
    async #carregar(){
        this.#loading=true;this.#erroCarregamento=null;this.#render();
        try{
            const r=await defs[this.#tipo].service.listar();
            if(r.code!==200)throw new Error(r.message||"Falha ao carregar dados");
            this.#registros=Array.isArray(r.data)?r.data:[];
        }catch(e){
            this.#erroCarregamento=e.message;
            this.#toast(e.message,"error");
        }finally{
            this.#loading=false;
            this.#render();
        }
    }
    #render(){const d=defs[this.#tipo],body=document.getElementById("tbody-alunos"),head=document.getElementById("tabela-head");head.innerHTML=`<tr>${d.columns.map(([,l])=>`<th>${this.#esc(l)}</th>`).join("")}<th>Ações</th></tr>`;if(this.#loading){body.innerHTML=`<tr><td colspan="${d.columns.length+1}" class="text-center">Carregando ${d.label.toLocaleLowerCase("pt-BR")}…</td></tr>`;return;}if(this.#erroCarregamento){body.innerHTML=`<tr><td colspan="${d.columns.length+1}" class="text-center">${this.#esc(this.#erroCarregamento)}</td></tr>`;return;}if(!this.#registros.length){body.innerHTML=`<tr><td colspan="${d.columns.length+1}" class="text-center">Nenhum registro encontrado</td></tr>`;return;}body.innerHTML=this.#registros.sortBy("id","desc").map(row=>`<tr>${d.columns.map(([k])=>`<td>${this.#esc(k==="ativo"?(Number(row[k])?"Sim":"Não"):(row[k]??"—"))}</td>`).join("")}<td><button class="btn btn-sm btn-primary" data-action="edit" data-id="${Number(row.id)}">Editar</button><button class="btn btn-sm btn-danger" data-action="delete" data-id="${Number(row.id)}">Excluir</button></td></tr>`).join("");}
    async #editar(id){try{const r=await defs[this.#tipo].service.buscar(id);if(r.code!==200||!r.data)throw new Error(r.message||"Registro não encontrado");this.#abrir(r.data);}catch(e){this.#toast(e.message,"error");}}
    #abrir(item=null){const form=document.getElementById("form-aluno"),fields=defs[this.#tipo].fields;form.replaceChildren();document.getElementById("modal-titulo").textContent=`${item?"Editar":"Novo"} ${defs[this.#tipo].label.slice(0,-1)}`;document.getElementById("aluno-id").value=item?.id||"";for(const [key,label,type,required,options] of fields){const wrap=document.createElement("div");wrap.className="mb-3";const lab=document.createElement("label");lab.className="form-label";lab.textContent=label;let input;if(type==="textarea")input=document.createElement("textarea");else if(type==="select"){input=document.createElement("select");options.forEach(v=>{const o=document.createElement("option");o.value=v;o.textContent=v.toTitleCase();input.append(o);});}else{input=document.createElement("input");input.type=type;}input.name=key;input.className=type==="checkbox"?"form-check-input":"form-control";input.required=Boolean(required);if(type==="checkbox")input.checked=item?Number(item[key])===1:true;else if(item)input.value=item[key]??"";else if(key==="personal_id"){try{input.value=JSON.parse(localStorage.getItem("vortex_user")||"{}").id||"";}catch{}}if(type!=="checkbox")lab.append(input);else{lab.append(input,document.createTextNode(` ${label}`));}wrap.append(lab);form.append(wrap);}document.getElementById("modal-acoes").hidden=false;document.getElementById("modal-aluno").style.display="block";}
    async #salvar(){const form=document.getElementById("form-aluno");if(!form.reportValidity())return;const id=document.getElementById("aluno-id").value,data={};for(const [key,,type] of defs[this.#tipo].fields){const el=form.elements[key];data[key]=type==="checkbox"?(el.checked?1:0):el.value.trim()||null;}try{const entity=new defs[this.#tipo].entity(data),payload=entity.toJSON();delete payload.id;const r=id?await defs[this.#tipo].service.atualizar(id,payload):await defs[this.#tipo].service.criar(payload);if(![200,201].includes(r.code))throw new Error(r.message||"Não foi possível salvar");this.#toast(r.message||"Registro salvo com sucesso","success");this.#fechar();await this.#carregar();}catch(e){this.#toast(e.message,"error");}}
    async #excluir(id){if(!window.confirm("Deseja excluir este registro?"))return;try{const r=await defs[this.#tipo].service.excluir(id);if(r.code!==200)throw new Error(r.message||"Não foi possível excluir");this.#toast(r.message||"Registro excluído com sucesso","success");await this.#carregar();}catch(e){this.#toast(e.message,"error");}}
    #fechar(){document.getElementById("modal-aluno").style.display="none";}
    #toast(msg,tipo){const el=document.createElement("div");el.className=`alert alert-${tipo==="success"?"success":"danger"} toast-poo`;el.setAttribute("role","status");el.textContent=msg;document.body.append(el);setTimeout(()=>el.remove(),4500);}
    #esc(v){return String(v).replace(/[&<>"']/g,c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]));}
}
document.addEventListener("DOMContentLoaded",()=>{window.gestaoManager=new GestaoManager();});
