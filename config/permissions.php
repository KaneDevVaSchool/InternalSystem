<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permission Guard
    |--------------------------------------------------------------------------
    | Guard name dùng cho Spatie Permission. Phải khớp với auth guard.
    */
    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Định nghĩa Permissions
    |--------------------------------------------------------------------------
    | Format: 'permission_name' => 'Mô tả hiển thị'
    | Quy ước đặt tên: {resource}_{action} (ví dụ: users.view, admin.stats)
    */
    'definitions' => [
        // User - Người dùng
        'user.profile.view' => 'Xem profile của chính mình',
        'user.profile.update' => 'Cập nhật profile của chính mình',
        'user.info.view_sensitive' => 'Xem thông tin nhạy cảm (CCCD, ngân hàng)',

        // Admin - Quản trị
        'admin.dashboard.view' => 'Xem trang admin dashboard',
        'admin.stats.view' => 'Xem thống kê hệ thống',
        'admin.users.view' => 'Xem danh sách người dùng',
        'admin.users.manage' => 'Quản lý người dùng (thêm/sửa/xóa)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Role ↔ Permission Mapping
    |--------------------------------------------------------------------------
    | Mỗi role được gán danh sách permissions. Admin có thể override user.
    */
    'roles' => [
        'user' => [
            'user.profile.view',
            'user.profile.update',
            'user.info.view_sensitive', // Chỉ cho chính user đó (policy check)
        ],
        'admin' => [
            'user.profile.view',
            'user.profile.update',
            'user.info.view_sensitive',
            'admin.dashboard.view',
            'admin.stats.view',
            'admin.users.view',
            'admin.users.manage',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Hierarchy (tùy chọn)
    |--------------------------------------------------------------------------
    | Admin kế thừa tất cả quyền của user. Xử lý trong Policy.
    */
    'hierarchy' => [
        'admin' => ['user'],
    ],

];
