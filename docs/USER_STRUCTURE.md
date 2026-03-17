# Cấu trúc User & UserInfo

## Phân quyền (Authorization)

### Vai trò (Roles)
- **admin**: Từ `ADMIN_EMAILS` trong `.env`
- **user**: Mặc định cho các tài khoản còn lại

### Policy UserInfo
| Hành động | User tự xem | Admin |
|-----------|-------------|-------|
| Xem thông tin nhạy cảm (CCCD, ngân hàng, địa chỉ...) | ✅ | ✅ |
| Sửa user_info | ✅ (chính mình) | ✅ |
| Xem thông tin công khai (company, department, position) | ✅ | ✅ |

### Dữ liệu công khai (user_info)
- `code`, `gender`, `company_name`, `department_name`, `unit_name`
- `headquarter_name`, `position_name`

### Dữ liệu nhạy cảm (chỉ self/admin)
- `birthdate`, `birth_place`, `national`, `religion`, `hometown`
- `identity`, `identity_date`, `identity_place` (CCCD/CMND)
- `tax_code`, `phone`, `address`, `household`
- `bank_account`, `bank`
- `start_working_date`, `working_place`, `note`

## Bảo mật (Security)

### User model – Hidden fields (không trả về API)
- `password`
- `google_access_token`, `google_refresh_token`
- `google_token_expires_at`, `google_scopes`

### Xác thực (Authentication)
- **Google OAuth** (ID token) – domain bị giới hạn bởi `GOOGLE_ALLOWED_DOMAIN`
- **Laravel Sanctum** – Bearer token cho API
- Đăng nhập: ghi `last_login_at`, `first_login_at`, `check_first_login`
- Đăng xuất: ghi `last_logout_at`

## Cấu trúc bảng

### users
| Cột | Mô tả |
|-----|-------|
| point, contribution_point | Điểm tích lũy |
| check_first_login | Đã đăng nhập lần đầu? |
| first_login_at, last_login_at, last_logout_at | Thời gian đăng nhập/xuất |
| level | Cấp độ |
| google_id | Google UID (sub) |
| deleted_at | Soft delete |

### user_info (1-1 với users)
| Cột | Mô tả |
|-----|-------|
| code | Mã nhân viên |
| gender | 1: Nam, 0: Nữ |
| birthdate, birth_place | Ngày sinh, nơi sinh |
| identity, identity_date, identity_place | CCCD/CMND |
| company_id, department_id | FK (nếu có bảng) |
| position_name, concurrent_position_name | Chức vụ |
