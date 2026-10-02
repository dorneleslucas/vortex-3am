document.addEventListener('DOMContentLoaded', () => {
    authManager.autoRedirectIfAuth();

    const loginForm = document.querySelector('#login-form');
    const registerForm = document.querySelector('#register-form');
    const tabs = document.querySelectorAll('.auth-tab');
    const typeOptions = document.querySelectorAll('.type-option');
    const cursorDot = document.querySelector('.cursor-dot');
    const cursorRing = document.querySelector('.cursor-ring');

    const showMessage = (elementId, message, type) => {
        const messageElement = document.querySelector(elementId);
        if (!messageElement) return;

        messageElement.textContent = message;
        messageElement.className = `form-message ${type}`;
    };

    const switchTab = (tabName) => {
        tabs.forEach((tab) => {
            const isActive = tab.dataset.tab === tabName;
            tab.classList.toggle('active', isActive);
            tab.setAttribute('aria-selected', String(isActive));
        });

        document.querySelector('#login-form')?.classList.toggle('active', tabName === 'login');
        document.querySelector('#register-form')?.classList.toggle('active', tabName === 'register');
    };

    tabs.forEach((tab) => {
        tab.type = 'button';
        tab.addEventListener('click', () => switchTab(tab.dataset.tab));
    });

    document.querySelector('.switch-to-login')?.addEventListener('click', (event) => {
        event.preventDefault();
        switchTab('login');
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
        const userType = document.querySelector('#login-type')?.value;

        if (!email || !password || !userType) {
            showMessage('#login-message', 'Preencha todos os campos.', 'error');
            return;
        }

        button.disabled = true;
        button.textContent = 'Entrando...';

        const result = await authManager.login(email, password, userType);
        if (!result.success) {
            showMessage('#login-message', result.message || 'Não foi possível entrar.', 'error');
            button.disabled = false;
            button.textContent = 'Entrar';
            return;
        }

        window.location.href = result.data.type_id === 3 ? 'app.html' : 'admin.html';
    });

    registerForm?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const button = document.querySelector('#register-btn');
        const name = document.querySelector('#register-name')?.value.trim();
        const email = document.querySelector('#register-email')?.value.trim();
        const password = document.querySelector('#register-password')?.value;
        const passwordConfirmation = document.querySelector('#register-password-confirm')?.value;
        const userType = document.querySelector('input[name="register-type"]:checked')?.value;

        if (!name || !email || !password || !passwordConfirmation || !userType) {
            showMessage('#register-message', 'Preencha todos os campos.', 'error');
            return;
        }

        if (password !== passwordConfirmation) {
            showMessage('#register-message', 'As senhas não coincidem.', 'error');
            return;
        }

        if (password.length < 6) {
            showMessage('#register-message', 'A senha deve ter pelo menos 6 caracteres.', 'error');
            return;
        }

        button.disabled = true;
        button.textContent = 'Criando conta...';

        const result = await authManager.register(name, email, password, userType);
        if (!result.success) {
            showMessage('#register-message', result.message || 'Não foi possível criar a conta.', 'error');
            button.disabled = false;
            button.textContent = 'Criar conta';
            return;
        }

        registerForm.reset();
        typeOptions.forEach((option) => {
            option.classList.toggle('active', option.querySelector('#type-aluno')?.checked);
        });
        showMessage('#register-message', 'Conta criada com sucesso. Faça login para continuar.', 'success');
        switchTab('login');
        document.querySelector('#login-email').value = email;
        button.disabled = false;
        button.textContent = 'Criar conta';
    });

    if (cursorDot && cursorRing && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        window.addEventListener('pointermove', (event) => {
            document.body.classList.add('cursor-ready');
            cursorDot.style.transform = `translate(${event.clientX}px, ${event.clientY}px) translate(-50%, -50%)`;
            cursorRing.style.transform = `translate(${event.clientX}px, ${event.clientY}px) translate(-50%, -50%)`;
        });

        document.querySelectorAll('a, button, input, select, label').forEach((element) => {
            element.addEventListener('pointerenter', () => document.body.classList.add('cursor-active'));
            element.addEventListener('pointerleave', () => document.body.classList.remove('cursor-active'));
        });
    }
});
