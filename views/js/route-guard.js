class RouteGuard {
  constructor(requiredUserType = null) {
    this.requiredUserType = requiredUserType;
    this.init();
  }

  init() {
    document.addEventListener('DOMContentLoaded', () => {
      this.checkAuth();
    });
  }

  checkAuth() {
    if (!authManager.redirectIfNotAuth(this.requiredUserType)) {
      throw new Error('Unauthorized access - redirecting');
    }
  }
}
