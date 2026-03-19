@extends('layouts.app')

@section('title', config('app.name') . ' - Admin Dashboard')

@push('styles')
    @vite(['resources/css/pages/admin.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/admin.js'])
@endpush

@section('content')
    <nav class="admin-nav">
        <h1>{{ config('app.name') }} <span class="admin-badge">Admin</span></h1>
        <div class="admin-nav-actions">
            <a href="{{ url('/admin') }}" class="admin-nav-link">Trang 1</a>
            <a href="{{ url('/admin/dashboard') }}" class="admin-nav-link">Trang 2</a>
            <span class="admin-user-info" id="admin-user-info">Đang tải...</span>
            <a href="{{ url('/home') }}" class="admin-nav-link">Trang chủ</a>
            <a href="#" class="btn-logout" onclick="adminLogout(); return false;">Đăng xuất</a>
        </div>
    </nav>
    <main class="admin-main">
        <div class="admin-card">
            <h2>Quản trị hệ thống</h2>
            <p class="admin-card-value" id="admin-page-title">Trang Admin 2</p>
            <div class="admin-stats" id="admin-stats">
                <p class="admin-loading">Đang tải thống kê...</p>
            </div>
        </div>
    </main>
@endsection
