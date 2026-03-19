@extends('layouts.app')

@section('title', config('app.name') . ' - Đăng nhập Admin')

@push('head')
    <script>
        window._googleCredentialQueue = [];
        window.handleCredentialResponse = function(r) { window._googleCredentialQueue.push(r); };
    </script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
@endpush

@push('styles')
    @vite(['resources/css/pages/login.css'])
@endpush

@push('scripts')
    @vite(['resources/js/pages/login.js'])
@endpush

@section('content')
    <main class="login-page login-page--admin" role="main" data-login-type="admin">
        <div class="login-card" aria-labelledby="login-title">
            <h1 id="login-title">{{ config('app.name') }} <span class="login-badge-admin">Admin</span></h1>
            <p class="login-subtitle">Đăng nhập Admin bằng tài khoản Google</p>
            <p class="login-type-label">Trang đăng nhập cho quản trị viên</p>

            @php
                $googleClientId = config('auth.google.client_id');
                $initialError = session('error') ?: (empty($googleClientId) ? 'Chưa cấu hình GOOGLE_CLIENT_ID trong .env. Thêm Authorized JavaScript origins (' . url('/') . ') tại Google Cloud Console.' : null);
            @endphp
            <div id="login-msg" class="login-msg @if($initialError) error active @endif" role="alert" aria-live="polite">@if($initialError){{ $initialError }}@endif</div>

            <div id="login-loading" class="login-loading" aria-hidden="true">
                <span class="login-loading-spinner" aria-hidden="true"></span>
                <span class="login-loading-text">Đang xử lý...</span>
            </div>

            @if(!empty($googleClientId))
                <div class="google-btn-wrap">
                    <div id="g_id_onload"
                         data-client_id="{{ $googleClientId }}"
                         data-context="signin"
                         data-ux_mode="redirect"
                         data-callback="handleCredentialResponse"
                         data-login_uri="{{ url('/auth/google/callback?intent=admin') }}"></div>
                    <div class="g_id_signin"
                         data-type="standard"
                         data-shape="rectangular"
                         data-theme="filled_black"
                         data-text="signin_with"
                         data-size="large"
                         data-logo_alignment="left"
                         data-width="280"
                         role="button"
                         aria-label="Đăng nhập bằng Google"></div>
                </div>
            @endif

            <p class="login-switch">
                <a href="{{ url('/') }}">← Đăng nhập cho người dùng</a>
            </p>
        </div>
    </main>
@endsection
