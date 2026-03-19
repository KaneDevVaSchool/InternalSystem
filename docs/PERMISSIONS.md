# Phân quyền và Permission

## Tổng quan

Hệ thống sử dụng **Spatie Laravel Permission** kết hợp với **Role** và **Permission** rõ ràng.

- **Role**: Vai trò (user, admin)
- **Permission**: Quyền hạn cụ thể theo resource và hành động

## Cấu trúc

```
config/permissions.php     → Định nghĩa permissions & role mapping
database/seeders/PermissionSeeder.php  → Seed roles + permissions
app/Http/Middleware/       → EnsureRole, EnsurePermission
```

## Permissions

| Permission | Mô tả | Role |
|-----------|-------|------|
| `user.profile.view` | Xem profile của chính mình | user, admin |
| `user.profile.update` | Cập nhật profile của chính mình | user, admin |
| `user.info.view_sensitive` | Xem thông tin nhạy cảm (CCCD, ngân hàng) | user, admin |
| `admin.dashboard.view` | Xem trang admin dashboard | admin |
| `admin.stats.view` | Xem thống kê hệ thống | admin |
| `admin.users.view` | Xem danh sách người dùng | admin |
| `admin.users.manage` | Quản lý người dùng (thêm/sửa/xóa) | admin |

## Roles

- **user**: Người dùng thường - có quyền xem/sửa profile bản thân
- **admin**: Quản trị viên - có tất cả quyền của user + quyền admin

## Sử dụng

### Route middleware

```php
// Theo permission (khuyến nghị)
Route::middleware(['auth:sanctum', 'permission:admin.stats.view'])->get(...);

// Theo role (legacy)
Route::middleware(['auth:sanctum', 'role:admin'])->get(...);
```

### Policy

Policies dùng `$user->can('permission.name')` để kiểm tra.

### API Response

`/auth/me` và response login trả về:
- `roles`: Mảng tên role
- `permissions`: Mảng tên permission (để frontend ẩn/hiện UI)

## Bảo mật bổ sung

- **Sanctum token expiration**: Mặc định 7 ngày (env `SANCTUM_TOKEN_EXPIRATION`)
- **Security headers**: X-Content-Type-Options, X-Frame-Options, HSTS (production)
- **Rate limiting**: Login 10/phút, Admin API 120/phút theo user
- **HTTPS**: Bắt buộc trong production

## Thêm Permission mới

1. Thêm vào `config/permissions.php` → `definitions` và `roles`
2. Chạy: `php artisan db:seed --class=PermissionSeeder`
