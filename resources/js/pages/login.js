import { getConfig } from '../config.js';

(function initLogin() {
  const config = getConfig();
  const debug = config.debug;

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

    if (debug) {
      console.log('[Google Login Debug] Credential nhận được:', {
        hasCredential: !!response?.credential,
        credentialLength: response?.credential?.length ?? 0,
        clientId: response?.clientId,
      });
    }

    const apiUrl = `${config.apiUrl}/auth/google`;
    if (debug) console.log('[Google Login Debug] POST', apiUrl);

    fetch(apiUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({ id_token: response.credential }),
    })
      .then((r) => {
        if (debug) console.log('[Google Login Debug] Response status:', r.status, r.statusText);
        return r.json();
      })
      .then(async (data) => {
        if (debug) console.log('[Google Login Debug] Response data:', { ...data, token: data.token ? '[SET]' : undefined });
        if (data.token) {
          localStorage.setItem('auth_token', data.token);
          const textEl = loadingEl?.querySelector('.login-loading-text');
          if (loadingEl) loadingEl.dataset.status = 'success';
          if (textEl) textEl.textContent = 'Đăng nhập thành công! Đang chuyển hướng...';
          await new Promise((r) => setTimeout(r, 600));
          window.location.href = config.homeUrl;
        } else {
          showMsg(data.message || 'Đăng nhập thất bại.', 'error');
        }
      })
      .catch((err) => {
        if (debug) console.error('[Google Login Debug] Lỗi:', err);
        showMsg('Lỗi kết nối. Vui lòng thử lại.', 'error');
      })
      .finally(() => {
        if (loadingEl && !loadingEl.dataset.status) loadingEl.classList.remove('active');
      });
  }

  // Gán callback thật, xử lý queue nếu Google đã gọi trước khi script load
  window.handleCredentialResponse = function handleCredentialResponse(response) {
    if (debug) console.log('[Google Login Debug] handleCredentialResponse được gọi');
    processCredential(response);
  };

  // Xử lý credential đã queue (One Tap có thể gọi trước khi script load)
  const queue = window._googleCredentialQueue || [];
  window._googleCredentialQueue = [];
  if (debug && queue.length > 0) console.log('[Google Login Debug] Queue có', queue.length, 'credential');
  queue.forEach(processCredential);
})();
