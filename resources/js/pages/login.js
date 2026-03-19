import { getConfig } from '../config.js';

(function initLogin() {
  const config = getConfig();
  const debug = config.debug;
  let isProcessing = false;

  const isAdminLogin = document.querySelector('[data-login-type="admin"]');
  const redirectUrl = isAdminLogin ? (window.location.origin + '/admin') : config.homeUrl;

  const MSG = {
    ERR_NETWORK: 'Lỗi kết nối. Vui lòng kiểm tra mạng và thử lại.',
    ERR_RATE_LIMIT: 'Đăng nhập quá nhiều lần. Vui lòng đợi 1 phút.',
    ERR_SERVER: 'Lỗi hệ thống. Vui lòng thử lại sau.',
    ERR_INVALID: 'Đăng nhập thất bại. Vui lòng thử lại.',
  };

  function showMsg(text, type) {
    const el = document.getElementById('login-msg');
    if (!el) return;
    el.textContent = text;
    el.className = `login-msg ${type} active`;
    el.setAttribute('role', 'alert');
  }

  function setLoading(active, status) {
    const el = document.getElementById('login-loading');
    if (!el) return;
    el.classList.toggle('active', active);
    if (status) el.dataset.status = status;
  }

  function processCredential(response) {
    if (isProcessing) return;
    const credential = response?.credential;
    if (!credential) {
      showMsg(MSG.ERR_INVALID, 'error');
      return;
    }

    isProcessing = true;
    const loadingEl = document.getElementById('login-loading');
    const msgEl = document.getElementById('login-msg');
    if (msgEl) msgEl.classList.remove('active');
    setLoading(true);

    if (debug) {
      console.log('[Google Login] Credential received', { length: credential.length });
    }

    const apiUrl = `${config.apiUrl}/auth/google`;
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 15000);

    fetch(apiUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({ id_token: credential }),
      signal: controller.signal,
    })
      .then((res) => {
        clearTimeout(timeoutId);
        if (res.status === 429) {
          throw new Error(MSG.ERR_RATE_LIMIT);
        }
        if (!res.ok) {
          return res.json().then((data) => {
            throw new Error(data.message || MSG.ERR_SERVER);
          }).catch((e) => {
            if (e instanceof Error && e.message !== MSG.ERR_SERVER) throw e;
            throw new Error(MSG.ERR_SERVER);
          });
        }
        return res.json();
      })
      .then((data) => {
        if (data.token) {
          localStorage.setItem('auth_token', data.token);
          setLoading(true, 'success');
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'Đăng nhập thành công!',
              timer: 1500,
              showConfirmButton: false,
              allowOutsideClick: false,
            }).then(() => {
              window.location.href = redirectUrl;
            });
          } else {
            window.location.href = redirectUrl;
          }
        } else {
          isProcessing = false;
          setLoading(false);
          showMsg(data.message || MSG.ERR_INVALID, 'error');
        }
      })
      .catch((err) => {
        clearTimeout(timeoutId);
        isProcessing = false;
        setLoading(false);
        const msg = err.name === 'AbortError' ? MSG.ERR_NETWORK : (err.message || MSG.ERR_NETWORK);
        showMsg(msg, 'error');
        if (debug) console.error('[Google Login]', err);
      });
  }

  window.handleCredentialResponse = function handleCredentialResponse(response) {
    if (debug) console.log('[Google Login] handleCredentialResponse called');
    processCredential(response);
  };

  const queue = window._googleCredentialQueue || [];
  window._googleCredentialQueue = [];
  queue.forEach(processCredential);
})();
