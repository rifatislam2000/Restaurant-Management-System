const passwordInput = document.getElementById('password');
const passwordToggle = document.getElementById('toggle-password');

if (passwordInput && passwordToggle) {
  passwordToggle.addEventListener('click', () => {
    const showPassword = passwordInput.type === 'password';

    passwordInput.type = showPassword ? 'text' : 'password';
    passwordToggle.textContent = showPassword ? 'Hide' : 'Show';
    passwordToggle.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
    passwordToggle.setAttribute('aria-pressed', String(showPassword));
  });
}
