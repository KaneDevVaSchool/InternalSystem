import { getConfig } from '../config.js';

(function initLogin() {
  const config = getConfig();

  function showMsg(text, type) {
    const el = document.getElementById('login-msg');
    if (!el) return;
    el.textContent = text;
    el.className = `login-msg ${type} active`;
  }

  function processCredential(response) {
    const loadingEl = document.getElementById('login-loading');
    const msgEl = document.getElementById('login-msg');
    if (loadingEl) loadingEl.classList.add('active');
    if (msgEl) msgEl.classList.remove('active');

    fetch(`${config.apiUrl}/auth/google`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({ id_token: response.credential }),
    })
      .then((r) => r.json())
      .then((data) => {
        if (data.token) {
          localStorage.setItem('auth_token', data.token);
          window.location.href = config.homeUrl;
        } else {
          showMsg(data.message || 'Đăng nhập thất bại.', 'error');
        }
      })
      .catch(() => {
        showMsg('Lỗi kết nối. Vui lòng thử lại.', 'error');
      })
      .finally(() => {
        if (loadingEl) loadingEl.classList.remove('active');
      });
  }

  // Gán callback thật, xử lý queue nếu Google đã gọi trước khi script load
  window.handleCredentialResponse = function handleCredentialResponse(response) {
    processCredential(response);
  };

  // Xử lý credential đã queue (One Tap có thể gọi trước khi script load)
  const queue = window._googleCredentialQueue || [];
  window._googleCredentialQueue = [];
  queue.forEach(processCredential);
})();
