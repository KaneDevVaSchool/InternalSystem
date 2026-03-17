@extends('layouts.app')

@section('title', config('app.name') . ' - Đăng nhập')

@push('styles')
    @vite(['resources/css/pages/login.css'])
@endpush

@push('scripts')
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    @vite(['resources/js/pages/login.js'])
@endpush

@section('content')
    <div class="login-card">
        <h1>{{ config('app.name') }}</h1>
        <p>Đăng nhập bằng tài khoản Google của tổ chức</p>

        <div id="login-msg" class="login-msg"></div>
        <div id="login-loading" class="login-loading">Đang xử lý...</div>

        <div class="google-btn-wrap">
            <div id="g_id_onload"
                 data-client_id="{{ config('auth.google.client_id') }}"
                 data-context="signin"
                 data-ux_mode="popup"
                 data-callback="handleCredentialResponse"
                 data-auto_prompt="true">
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
        </div>
    </div>
@endsection
