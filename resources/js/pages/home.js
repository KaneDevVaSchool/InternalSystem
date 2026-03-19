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
        const adminLink = document.getElementById('admin-link');
        if (userInfoEl) {
          userInfoEl.textContent = `${data.user.name} (${data.user.email})`;
        }
        if (welcomeEl) {
          welcomeEl.textContent = `Xin chào, ${data.user.name}!`;
        }
        if (adminLink && (data.user.roles || []).includes('admin')) {
          adminLink.style.display = '';
        }
      }
    });

  window.logout = function logout() {
    Swal.fire({
      title: 'Đăng xuất?',
      text: 'Bạn có chắc muốn đăng xuất khỏi tài khoản?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#dc2626',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Đăng xuất',
      cancelButtonText: 'Hủy',
    }).then((result) => {
      if (!result.isConfirmed) return;
      Swal.fire({
        title: 'Đang đăng xuất...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });
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
      setTimeout(() => {
        window.location.href = config.loginUrl;
      }, 300);
    });
  };
})();
