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
10. **Authorized redirect URIs** (bắt buộc khi dùng redirect flow):
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
GOOGLE_CLIENT_ID=123456789-xxxxxxxx.apps.googleusercontent.com
GOOGLE_ALLOWED_DOMAIN=your-domain.com
```

- `GOOGLE_CLIENT_ID`: Client ID vừa tạo (kết thúc bằng `.apps.googleusercontent.com`)
- `GOOGLE_ALLOWED_DOMAIN`: Tên domain Google Workspace (vd: `company.com`) - chỉ user @company.com mới đăng nhập được

### Bước 3: Clear config cache

```bash
php artisan config:clear
```

---

## Kiểm tra

- URL bạn truy cập (vd: `http://localhost:8000`) **phải có** trong Authorized JavaScript origins
- Nếu dùng domain khác (vd: `http://192.168.1.100:8000`) → thêm vào origins
- Client ID không có khoảng trắng thừa, copy đầy đủ
