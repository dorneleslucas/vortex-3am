class AuthManager {
  constructor() {
    this.api = new UserAPI();
    this.tokenKey = 'vortex_token';
    this.userKey = 'vortex_user';
  }

  async login(email, password, userType) {
    try {
      const response = await this.api.login(email, password, userType);

      if (response.code === 200 && response.data?.token) {
        this.setToken(response.data.token);
        this.setUser(response.data);
        return { success: true, data: response.data };
      }
      return { success: false, message: response.message };
    } catch (error) {
      return { success: false, message: 'Erro na comunicação com servidor' };
    }
  }

  async register(name, email, password, userType = 'aluno') {
    try {
      const response = await this.api.register(name, email, password, userType);

      if (response.code === 201) {
        return { success: true, data: response.data };
      }
      return { success: false, message: response.message };
    } catch (error) {
      return { success: false, message: 'Erro na comunicação com servidor' };
    }
  }

  setToken(token) {
    localStorage.setItem(this.tokenKey, token);
  }

  getToken() {
    return localStorage.getItem(this.tokenKey);
  }

  setUser(user) {
    localStorage.setItem(this.userKey, JSON.stringify(user));
  }

  getUser() {
    const user = localStorage.getItem(this.userKey);
    return user ? JSON.parse(user) : null;
  }

  decodeToken(token) {
    try {
      const base64Url = token.split('.')[1];
      const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
      const jsonPayload = decodeURIComponent(
        atob(base64).split('').map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)).join('')
      );
      return JSON.parse(jsonPayload);
    } catch {
      return null;
    }
  }

  isAuthenticated() {
    const token = this.getToken();
    if (!token) return false;

    const decoded = this.decodeToken(token);
    if (!decoded) return false;

    const now = Math.floor(Date.now() / 1000);
    return decoded.exp > now;
  }

  getUserType() {
    const user = this.getUser();
    const typeId = Number(user?.type_id);

    if (typeId === 1) return 'admin';
    if (typeId === 2) return 'personal';
    if (typeId === 3) return 'aluno';
    return null;
  }

  logout() {
    localStorage.removeItem(this.tokenKey);
    localStorage.removeItem(this.userKey);
  }

  redirectIfNotAuth(requiredType = null) {
    if (!this.isAuthenticated()) {
      window.location.href = `${window.location.origin}/vortex-3am/views/auth.html`;
      return false;
    }

    if (requiredType) {
      const userType = this.getUserType();
      if (userType !== requiredType && userType !== 'admin') {
        window.location.href = `${window.location.origin}/vortex-3am/views/auth.html`;
        return false;
      }
    }

    return true;
  }

  autoRedirectIfAuth() {
    if (this.isAuthenticated()) {
      const userType = this.getUserType();
      if (userType === 'aluno') {
        window.location.href = `${window.location.origin}/vortex-3am/views/app.html`;
      } else if (userType === 'personal') {
        window.location.href = `${window.location.origin}/vortex-3am/views/admin.html`;
      } else if (userType === 'admin') {
        window.location.href = `${window.location.origin}/vortex-3am/views/admin.html`;
      }
    }
  }
}

const authManager = new AuthManager();
