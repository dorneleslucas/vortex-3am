document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.querySelector('#login-form');
  const registerForm = document.querySelector('#register-form');
  const tabs = document.querySelectorAll('.auth-tab');
  const typeOptions = document.querySelectorAll('.type-option');

  const showMessage = (selector, message, type) => {
    const element = document.querySelector(selector);
    if (!element) return;

    element.textContent = message;
    element.className = `form-message ${type}`;
  };

  const selectTab = (name) => {
    tabs.forEach((tab) => tab.classList.toggle('active', tab.dataset.tab === name));
    loginForm?.classList.toggle('active', name === 'login');
    registerForm?.classList.toggle('active', name === 'register');
  };

  tabs.forEach((tab) => tab.addEventListener('click', () => selectTab(tab.dataset.tab)));

  document.querySelector('.switch-to-login')?.addEventListener('click', (event) => {
    event.preventDefault();
    selectTab('login');
  });

  typeOptions.forEach((option) => {
    option.addEventListener('click', () => {
      const radio = option.querySelector('input[type="radio"]');
      if (!radio) return;

      radio.checked = true;
      typeOptions.forEach((item) => item.classList.toggle('active', item === option));
    });
  });

  loginForm?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const button = document.querySelector('#login-btn');
    const email = document.querySelector('#login-email')?.value.trim();
    const password = document.querySelector('#login-password')?.value;
    const type = document.querySelector('#login-type')?.value;

    if (!email || !password || !type) {
      showMessage('#login-message', 'Preencha todos os campos.', 'error');
      return;
    }

    button.disabled = true;
    button.textContent = 'Entrando...';

    try {
      const user = await authManager.login(email, password, type);
      window.location.href = user.type_id === 3 ? 'app.html' : 'admin.html';
    } catch (error) {
      showMessage('#login-message', error.message, 'error');
      button.disabled = false;
      button.textContent = 'Entrar';
    }
  });

  registerForm?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const button = document.querySelector('#register-btn');
    const name = document.querySelector('#register-name')?.value.trim();
    const email = document.querySelector('#register-email')?.value.trim();
    const password = document.querySelector('#register-password')?.value;
    const confirmation = document.querySelector('#register-password-confirm')?.value;
    const type = document.querySelector('input[name="register-type"]:checked')?.value;

    if (!name || !email || !password || !confirmation || !type) {
      showMessage('#register-message', 'Preencha todos os campos.', 'error');
      return;
    }

    if (password.length < 6) {
      showMessage('#register-message', 'A senha deve ter pelo menos 6 caracteres.', 'error');
      return;
    }

    if (password !== confirmation) {
      showMessage('#register-message', 'As senhas não coincidem.', 'error');
      return;
    }

    button.disabled = true;
    button.textContent = 'Criando conta...';

    try {
      await authManager.register(name, email, password, type);
      registerForm.reset();
      showMessage('#register-message', 'Conta criada. Faça login para continuar.', 'success');
      document.querySelector('#login-email').value = email;
      selectTab('login');
    } catch (error) {
      showMessage('#register-message', error.message, 'error');
    } finally {
      button.disabled = false;
      button.textContent = 'Criar conta';
    }
  });
});
