class AlunosAPI {
  constructor(baseUrl = null) {
    if (!baseUrl) {
      baseUrl = `${window.location.origin}/vortex-3am/api`;
    }

    this.baseUrl = baseUrl;
    this.endpoint = `${baseUrl}/alunos`;
  }

  async criar(dados) {
    try {
      const response = await fetch(`${this.endpoint}/`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'Authorization': `Bearer ${this.getToken()}`
        },
        body: new URLSearchParams(dados)
      });
      return await response.json();
    } catch (error) {
      console.error('Erro ao criar aluno:', error);
      throw error;
    }
  }

  async listar() {
    try {
      const response = await fetch(`${this.endpoint}/list`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${this.getToken()}`
        }
      });
      return await response.json();
    } catch (error) {
      console.error('Erro ao listar alunos:', error);
      throw error;
    }
  }

  async buscar(id) {
    try {
      const response = await fetch(`${this.endpoint}/list/${id}`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${this.getToken()}`
        }
      });
      return await response.json();
    } catch (error) {
      console.error('Erro ao buscar aluno:', error);
      throw error;
    }
  }

  async buscarPorPersonal(personalId) {
    try {
      const response = await fetch(`${this.endpoint}/personal/${personalId}`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${this.getToken()}`
        }
      });
      return await response.json();
    } catch (error) {
      console.error('Erro ao buscar alunos do personal:', error);
      throw error;
    }
  }

  async atualizar(id, dados) {
    try {
      const response = await fetch(`${this.endpoint}/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'Authorization': `Bearer ${this.getToken()}`
        },
        body: new URLSearchParams(dados)
      });
      return await response.json();
    } catch (error) {
      console.error('Erro ao atualizar aluno:', error);
      throw error;
    }
  }

  async excluir(id) {
    try {
      const response = await fetch(`${this.endpoint}/${id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${this.getToken()}`
        }
      });
      return await response.json();
    } catch (error) {
      console.error('Erro ao excluir aluno:', error);
      throw error;
    }
  }

  getToken() {
    return localStorage.getItem('vortex_token') || '';
  }
}

// Uso:
// const alunosAPI = new AlunosAPI();
// alunosAPI.listar().then(resultado => console.log(resultado));
