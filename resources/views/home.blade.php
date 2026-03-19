@extends('layouts.app')

@section('title', config('app.name') . ' - Trang chủ')

@push('styles')
    @vite(['resources/css/pages/home.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/home.js'])
@endpush

@section('content')
    <nav class="app-nav">
        <h1>{{ config('app.name') }}</h1>
        <div class="app-nav-actions">
            <span class="app-user-info" id="user-info">Đang tải...</span>
            <a href="{{ url('/admin') }}" class="app-nav-link" id="admin-link" style="display: none;">Admin</a>
            <a href="#" class="btn-logout" onclick="logout(); return false;">Đăng xuất</a>
        </div>
    </nav>
    <main class="app-main">
        <div class="app-card">
            <h2>Chào mừng</h2>
            <p class="app-card-value" id="welcome">Bạn đã đăng nhập thành công.</p>
        </div>
    </main>
@endsection
