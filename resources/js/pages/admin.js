import { getConfig } from '../config.js';

(function initAdmin() {
  const config = getConfig();
  const token = localStorage.getItem('auth_token');

  if (!token) {
    window.location.href = window.location.origin + '/admin/login';
    return;
  }

  function redirectNonAdmin() {
    window.location.href = config.homeUrl || '/home';
  }

  function renderStats(stats) {
    const el = document.getElementById('admin-stats');
    if (!el) return;
    el.innerHTML = `
      <div class="admin-stat-item">
        <div class="admin-stat-value">${stats.total_users ?? '-'}</div>
        <div class="admin-stat-label">Người dùng</div>
      </div>
      <div class="admin-stat-item">
        <div class="admin-stat-value">${stats.admins_count ?? '-'}</div>
        <div class="admin-stat-label">Quản trị viên</div>
      </div>
    `;
  }

  Promise.all([
    fetch(`${config.apiUrl}/auth/me`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      },
    }),
  ])
    .then(([meRes]) => {
      if (meRes.status === 401) {
        localStorage.removeItem('auth_token');
        window.location.href = window.location.origin + '/admin/login';
        return { user: null };
      }
      return meRes.json();
    })
    .then((meData) => {
      const user = meData?.user;
      if (!user) return redirectNonAdmin();

      const roles = user.roles || [];
      if (!roles.includes('admin')) {
        redirectNonAdmin();
        return;
      }

      const userInfoEl = document.getElementById('admin-user-info');
      if (userInfoEl) {
        userInfoEl.textContent = `${user.name} (${user.email})`;
      }

      return fetch(`${config.apiUrl}/admin/stats`, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json',
        },
      }).then((r) => (r.ok ? r.json() : { stats: {} }));
    })
    .then((statsData) => {
      if (statsData && statsData.stats) {
        renderStats(statsData.stats);
      } else {
        const el = document.getElementById('admin-stats');
        if (el) el.innerHTML = '<p class="admin-loading">Không tải được thống kê.</p>';
      }
    })
    .catch(() => {
      redirectNonAdmin();
    });

  window.adminLogout = function adminLogout() {
    if (typeof Swal === 'undefined') {
      localStorage.removeItem('auth_token');
      window.location.href = config.loginUrl;
      return;
    }
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
        didOpen: () => Swal.showLoading(),
      });
      fetch(`${config.apiUrl}/auth/logout`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json',
        },
      }).finally(() => {});
      localStorage.removeItem('auth_token');
      setTimeout(() => {
        window.location.href = config.loginUrl;
      }, 300);
    });
  };
})();
