class UserAPI {
  constructor(baseUrl = 'http://localhost/vortex-3am/api') {
    this.endpoint = `${baseUrl}/users`;
  }

  register(name, email, password, type) {
    const route = type === 'personal' ? 'register-personal' : 'register';
    return this.request(route, { name, email, password });
  }

  login(email, password, type) {
    const routes = {
      admin: 'login-admin',
      personal: 'login-personal',
      aluno: 'login'
    };

    return this.request(routes[type] || routes.aluno, { email, password });
  }

  async request(route, data) {
    let response;

    try {
      response = await fetch(`${this.endpoint}/${route}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
        },
        body: new URLSearchParams(data).toString()
      });
    } catch {
      throw new Error('Não foi possível conectar à API. Verifique se o Apache está em execução.');
    }

    const rawBody = await response.text();
    let result = {};

    try {
      result = rawBody ? JSON.parse(rawBody) : {};
    } catch {
      throw new Error(`A API retornou uma resposta inválida (${response.status}).`);
    }

    if (!response.ok) {
      throw new Error(result.message || `A solicitação falhou (${response.status}).`);
    }

    return result;
  }
}
