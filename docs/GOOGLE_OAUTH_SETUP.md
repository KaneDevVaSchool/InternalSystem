# Cấu hình Google OAuth - Sửa lỗi 401 invalid_client

## Nguyên nhân lỗi 401 invalid_client

1. **Chưa cấu hình** `GOOGLE_CLIENT_ID` trong `.env`
2. **Chưa thêm Authorized JavaScript origins** trong Google Cloud Console
3. Dùng sai loại Client (phải là **Web application**)
4. Copy sai Client ID

---

## Hướng dẫn cấu hình

### Bước 1: Tạo OAuth Client trong Google Cloud Console

1. Truy cập [Google Cloud Console](https://console.cloud.google.com/)
2. Tạo project mới hoặc chọn project có sẵn
3. Vào **APIs & Services** → **Credentials**
4. Bấm **Create Credentials** → **OAuth client ID**
5. Nếu chưa có OAuth consent screen → tạo **OAuth consent screen** trước:
   - User Type: **Internal** (chỉ cho Workspace) hoặc **External** (cho mọi người)
   - App name, Support email, Developer contact
6. Quay lại **Credentials** → **Create Credentials** → **OAuth client ID**
7. Chọn **Application type**: **Web application**
8. Đặt tên (ví dụ: "PM Manage Web")
9. **Quan trọng - Authorized JavaScript origins**:
   ```
   http://localhost
   http://localhost:8000
   http://127.0.0.1
   http://127.0.0.1:8000
   ```
   Và domain production (khi deploy):
   ```
   https://your-domain.com
   ```
10. **Authorized redirect URIs** (bắt buộc khi dùng redirect flow). Google GSI gửi credential qua **POST** về URL này:
    ```
    http://localhost/auth/google/callback
    http://localhost:8000/auth/google/callback
    http://127.0.0.1/auth/google/callback
    http://127.0.0.1:8000/auth/google/callback
    ```
    Và khi deploy:
    ```
    https://your-domain.com/auth/google/callback
    ```
11. Bấm **Create** → copy **Client ID**

### Bước 2: Cấu hình .env

```env
APP_URL=http://localhost:8000
GOOGLE_CLIENT_ID=123456789-xxxxxxxx.apps.googleusercontent.com
GOOGLE_ALLOWED_DOMAIN=your-domain.com
```

- `APP_URL`: URL gốc của ứng dụng. **Quan trọng**: Callback URL = `{APP_URL}/auth/google/callback` → phải trùng với **Authorized redirect URIs** trong Google Cloud Console.
  - Local: `http://localhost:8000` hoặc `http://127.0.0.1:8000`
  - Production: `https://your-domain.com`
- `GOOGLE_CLIENT_ID`: Client ID vừa tạo (kết thúc bằng `.apps.googleusercontent.com`)
- `GOOGLE_ALLOWED_DOMAIN`: Tên domain Google Workspace (vd: `company.com`) - chỉ user @company.com mới đăng nhập được

### Bước 3: Clear config cache

```bash
php artisan config:clear
```

---

## Debug

Khi `APP_DEBUG=true` trong .env, hệ thống sẽ ghi log debug:

- **Browser Console (F12)**:
  - `[Google Login Debug]` - credential nhận được, request/response API
  - `[Google Callback Debug]` - URL callback khi redirect từ Google
- **Laravel log** (`storage/logs/debug/laravel-YYYY-MM-DD.log`):
  - Token length, preview
  - Thành công: user_id, email
  - Thất bại: exception detail

Đảm bảo `LOG_LEVEL=debug` trong .env để thấy `Log::debug()`.

---

## Kiểm tra

- `APP_URL` trong .env phải khớp với URL bạn truy cập (vd: `http://localhost:8000`) → callback sẽ là `{APP_URL}/auth/google/callback`
- URL trong `APP_URL` **phải có** trong Authorized JavaScript origins và Authorized redirect URIs
- Nếu dùng domain khác (vd: `http://192.168.1.100:8000`) → cập nhật `APP_URL` và thêm vào Google Cloud Console
- Client ID không có khoảng trắng thừa, copy đầy đủ
