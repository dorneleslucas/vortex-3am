class AlunosManager {
  constructor() {
    this.api = new AlunosAPI();
    this.alunos = [];
    this.init();
  }

  init() {
    this.setupEventListeners();
    this.carregarAlunos();
  }

  setupEventListeners() {
    // Botão criar/salvar
    const btnSalvar = document.getElementById('btn-salvar-aluno');
    if (btnSalvar) {
      btnSalvar.addEventListener('click', () => this.salvarAluno());
    }

    // Botão novo
    const btnNovo = document.getElementById('btn-novo-aluno');
    if (btnNovo) {
      btnNovo.addEventListener('click', () => this.abrirFormulario());
    }

    // Botão cancelar
    const btnCancelar = document.getElementById('btn-cancelar-aluno');
    if (btnCancelar) {
      btnCancelar.addEventListener('click', () => this.fecharFormulario());
    }

    // Enter no formulário
    const form = document.getElementById('form-aluno');
    if (form) {
      form.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          this.salvarAluno();
        }
      });
    }
  }

  async carregarAlunos() {
    try {
      const resultado = await this.api.listar();
      if (resultado.code === 200) {
        this.alunos = resultado.data || [];
        this.renderizarTabela();
      } else {
        this.mostrarAviso('Erro ao carregar alunos', 'error');
      }
    } catch (error) {
      this.mostrarAviso('Erro na comunicação com servidor', 'error');
      console.error(error);
    }
  }

  renderizarTabela() {
    const tbody = document.getElementById('tbody-alunos');
    if (!tbody) return;

    if (this.alunos.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" class="text-center">Nenhum aluno cadastrado</td></tr>';
      return;
    }

    tbody.innerHTML = this.alunos.map(aluno => `
      <tr>
        <td>${aluno.id}</td>
        <td>${aluno.nome}</td>
        <td>${aluno.email || '-'}</td>
        <td>${aluno.telefone || '-'}</td>
        <td>${aluno.objetivo || '-'}</td>
        <td>
          <button class="btn btn-sm btn-primary" onclick="alunosManager.editar(${aluno.id})">Editar</button>
          <button class="btn btn-sm btn-danger" onclick="alunosManager.deletar(${aluno.id})">Deletar</button>
        </td>
      </tr>
    `).join('');
  }

  abrirFormulario(aluno = null) {
    const modal = document.getElementById('modal-aluno');
    const form = document.getElementById('form-aluno');

    if (form) {
      form.reset();
      if (aluno) {
        document.getElementById('aluno-id').value = aluno.id;
        document.getElementById('personal-id').value = aluno.personal_id;
        document.getElementById('nome').value = aluno.nome;
        document.getElementById('email').value = aluno.email;
        document.getElementById('telefone').value = aluno.telefone;
        document.getElementById('objetivo').value = aluno.objetivo;
        document.getElementById('ativo').checked = aluno.ativo === 1;
      } else {
        document.getElementById('aluno-id').value = '';
      }
    }

    if (modal) {
      modal.style.display = 'block';
    }
  }

  fecharFormulario() {
    const modal = document.getElementById('modal-aluno');
    if (modal) {
      modal.style.display = 'none';
    }
  }

  async salvarAluno() {
    const id = document.getElementById('aluno-id')?.value;
    const personalId = document.getElementById('personal-id')?.value;
    const nome = document.getElementById('nome')?.value;
    const email = document.getElementById('email')?.value;
    const telefone = document.getElementById('telefone')?.value;
    const objetivo = document.getElementById('objetivo')?.value;
    const ativo = document.getElementById('ativo')?.checked ? 1 : 0;

    if (!personalId || !nome) {
      this.mostrarAviso('Personal ID e Nome são obrigatórios', 'error');
      return;
    }

    const dados = {
      personal_id: parseInt(personalId),
      nome,
      email: email || null,
      telefone: telefone || null,
      objetivo: objetivo || null,
      ativo
    };

    try {
      let resultado;
      if (id) {
        dados.id = parseInt(id);
        resultado = await this.api.atualizar(id, dados);
      } else {
        resultado = await this.api.criar(dados);
      }

      if (resultado.code === 201 || resultado.code === 200) {
        this.mostrarAviso(resultado.message, 'success');
        this.fecharFormulario();
        this.carregarAlunos();
      } else {
        this.mostrarAviso(resultado.message || 'Erro ao salvar aluno', 'error');
      }
    } catch (error) {
      this.mostrarAviso('Erro na comunicação com servidor', 'error');
      console.error(error);
    }
  }

  async editar(id) {
    try {
      const resultado = await this.api.buscar(id);
      if (resultado.code === 200) {
        this.abrirFormulario(resultado.data);
      } else {
        this.mostrarAviso('Aluno não encontrado', 'error');
      }
    } catch (error) {
      this.mostrarAviso('Erro ao carregar aluno', 'error');
      console.error(error);
    }
  }

  async deletar(id) {
    if (!confirm('Tem certeza que deseja deletar este aluno?')) {
      return;
    }

    try {
      const resultado = await this.api.excluir(id);
      if (resultado.code === 200) {
        this.mostrarAviso(resultado.message, 'success');
        this.carregarAlunos();
      } else {
        this.mostrarAviso(resultado.message || 'Erro ao deletar aluno', 'error');
      }
    } catch (error) {
      this.mostrarAviso('Erro na comunicação com servidor', 'error');
      console.error(error);
    }
  }

  mostrarAviso(mensagem, tipo = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo === 'success' ? 'success' : tipo === 'error' ? 'danger' : 'info'}`;
    alertDiv.textContent = mensagem;
    alertDiv.style.marginBottom = '20px';

    const container = document.getElementById('alunos-container') || document.body;
    container.insertBefore(alertDiv, container.firstChild);

    setTimeout(() => {
      alertDiv.remove();
    }, 5000);
  }
}

let alunosManager;
document.addEventListener('DOMContentLoaded', () => {
  alunosManager = new AlunosManager();
});
