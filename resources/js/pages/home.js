import { getConfig } from '../config.js';

(function initHome() {
  const config = getConfig();
  const token = localStorage.getItem('auth_token');

  if (!token) {
    window.location.href = config.loginUrl;
    return;
  }

  fetch(`${config.apiUrl}/auth/me`, {
    headers: {
      Authorization: `Bearer ${token}`,
      Accept: 'application/json',
    },
  })
    .then((r) => {
      if (r.status === 401) {
        localStorage.removeItem('auth_token');
        window.location.href = config.loginUrl;
        return null;
      }
      return r.json();
    })
    .then((data) => {
      if (data?.user) {
        const userInfoEl = document.getElementById('user-info');
        const welcomeEl = document.getElementById('welcome');
        if (userInfoEl) {
          userInfoEl.textContent = `${data.user.name} (${data.user.email})`;
        }
        if (welcomeEl) {
          welcomeEl.textContent = `Xin chào, ${data.user.name}!`;
        }
      }
    });

  window.logout = function logout() {
    if (token) {
      fetch(`${config.apiUrl}/auth/logout`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json',
        },
      }).finally(() => {});
    }
    localStorage.removeItem('auth_token');
    window.location.href = config.loginUrl;
  };
})();
