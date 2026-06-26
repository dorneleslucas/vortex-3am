class UserAPI {
  constructor(baseUrl = null) {
    if (!baseUrl) {
      baseUrl = `${window.location.origin}/vortex-3am/api`;
    }

    this.baseUrl = baseUrl;
    this.endpoint = `${baseUrl}/users`;
  }

  async register(name, email, password, userType = 'aluno') {
    try {
      const response = await fetch(`${this.endpoint}/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ name, email, password, user_type: userType })
      });
      return await response.json();
    } catch (error) {
      console.error('Erro ao registrar:', error);
      throw error;
    }
  }

  async login(email, password, userType = 'personal') {
    try {
      const response = await fetch(`${this.endpoint}/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ email, password, user_type: userType })
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
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ email, password })
      });
      return await response.json();
    } catch (error) {
      console.error('Erro ao fazer login como admin:', error);
      throw error;
    }
  }
}
