class AuthManager {
  constructor() {
    this.api = new UserAPI();
    this.tokenKey = 'vortex_token';
    this.userKey = 'vortex_user';
  }

  async login(email, password, type) {
    const response = await this.api.login(email, password, type);

    if (response.code !== 200 || !response.data?.token) {
      throw new Error(response.message || 'Não foi possível fazer login.');
    }

    this.setSession(response.data);
    return response.data;
  }

  async register(name, email, password, type) {
    const response = await this.api.register(name, email, password, type);

    if (response.code !== 201) {
      throw new Error(response.message || 'Não foi possível criar a conta.');
    }

    return response.data;
  }

  setSession(user) {
    localStorage.setItem(this.tokenKey, user.token);
    localStorage.setItem(this.userKey, JSON.stringify(user));
  }

  getToken() {
    return localStorage.getItem(this.tokenKey);
  }

  getUser() {
    try {
      return JSON.parse(localStorage.getItem(this.userKey));
    } catch {
      return null;
    }
  }

  decodeToken(token) {
    try {
      const payload = token.split('.')[1].replace(/-/g, '+').replace(/_/g, '/');
      return JSON.parse(atob(payload));
    } catch {
      return null;
    }
  }

  isAuthenticated() {
    const token = this.getToken();
    const payload = token ? this.decodeToken(token) : null;
    return Boolean(payload?.exp && payload.exp > Math.floor(Date.now() / 1000));
  }

  getUserType() {
    const typeId = this.getUser()?.type_id;
    return ({ 1: 'admin', 2: 'personal', 3: 'aluno' })[typeId] || null;
  }

  redirectIfNotAuth(requiredType = null) {
    if (!this.isAuthenticated() || (requiredType && this.getUserType() !== requiredType && this.getUserType() !== 'admin')) {
      window.location.href = '/vortex-3am/views/auth.html';
      return false;
    }
    return true;
  }

  autoRedirectIfAuth() {
    if (!this.isAuthenticated()) return;

    window.location.href = this.getUserType() === 'aluno' ? 'app.html' : 'admin.html';
  }

  logout() {
    localStorage.removeItem(this.tokenKey);
    localStorage.removeItem(this.userKey);
  }
}

const authManager = new AuthManager();
