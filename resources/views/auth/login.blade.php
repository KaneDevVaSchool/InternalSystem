@extends('layouts.app')

@section('title', config('app.name') . ' - Đăng nhập')

@push('head')
    {{-- Google callback stub: phải có trước GSI để tránh race condition --}}
    <script>
        window._googleCredentialQueue = [];
        window.handleCredentialResponse = function(r) {
            window._googleCredentialQueue.push(r);
        };
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
    <div class="login-card">
        <h1>{{ config('app.name') }}</h1>
        <p>Đăng nhập bằng tài khoản Google của tổ chức</p>

        <div id="login-msg" class="login-msg @if(session('error')) error active @endif">@if(session('error')){{ session('error') }}@endif</div>
        <div id="login-loading" class="login-loading">Đang xử lý...</div>

        @php $googleClientId = config('auth.google.client_id'); @endphp
        <div class="google-btn-wrap">
            @if(!empty($googleClientId))
            <div id="g_id_onload"
                 data-client_id="{{ $googleClientId }}"
                 data-context="signin"
                 data-ux_mode="redirect"
                 data-callback="handleCredentialResponse"
                 data-login_uri="{{ url('/auth/google/callback') }}">
            </div>
            <div class="g_id_signin"
                 data-type="standard"
                 data-shape="rectangular"
                 data-theme="filled_black"
                 data-text="signin_with"
                 data-size="large"
                 data-logo_alignment="left"
                 data-width="280">
            </div>
            @else
            <div class="login-msg error active">
                Lỗi: Chưa cấu hình <code>GOOGLE_CLIENT_ID</code> trong .env. Lỗi 401? Thêm <b>Authorized JavaScript origins</b> (vd: {{ url('/') }}) tại Google Cloud Console. Xem <code>docs/GOOGLE_OAUTH_SETUP.md</code>
            </div>
            @endif
        </div>
    </div>
@endsection
