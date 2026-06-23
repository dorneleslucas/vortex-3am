class UserAPI {
  constructor(baseUrl = '/api') {
    this.baseUrl = baseUrl;
    this.endpoint = `${baseUrl}/users`;
  }

  async register(name, email, password) {
    try {
      const response = await fetch(`${this.endpoint}/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, email, password })
      });
      return await response.json();
    } catch (error) {
      console.error('Erro ao registrar:', error);
      throw error;
    }
  }

  async login(email, password) {
    try {
      const response = await fetch(`${this.endpoint}/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
      });
      return await response.json();
    } catch (error) {
      console.error('Erro ao fazer login:', error);
      throw error;
    }
  }

  async loginAdmin(email, password) {
    try {
      const response = await fetch(`${this.endpoint}/login-admin`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
      });
      return await response.json();
    } catch (error) {
      console.error('Erro ao fazer login como admin:', error);
      throw error;
    }
  }
}
