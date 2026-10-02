class UserAPI {
  constructor(baseUrl = '../api') {
    this.baseUrl = baseUrl;
    this.endpoint = `${baseUrl}/users`;
  }

  async register(name, email, password) {
    return this.request('register', { name, email, password });
  }

  async registerPersonal(name, email, password) {
    return this.request('register-personal', { name, email, password });
  }

  async login(email, password) {
    return this.request('login', { email, password });
  }

  async loginPersonal(email, password) {
    return this.request('login-personal', { email, password });
  }

  async loginAdmin(email, password) {
    return this.request('login-admin', { email, password });
  }

  async request(path, data) {
    const response = await fetch(`${this.endpoint}/${path}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.message || `Erro na requisição: ${response.status}`);
    }

    return result;
  }
}
