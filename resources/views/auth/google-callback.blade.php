@extends('layouts.app')

@section('title', config('app.name') . ' - Đang đăng nhập')

@push('head')
    {{-- Google callback stub: phải có trước GSI để tránh race condition --}}
    <script>
        window._googleCredentialQueue = [];
        window.handleCredentialResponse = function(r) {
            window._googleCredentialQueue.push(r);
        };
        @if(config('app.debug'))
        (function() {
            var q = new URLSearchParams(location.search);
            var h = location.hash ? location.hash.substring(0, 50) + '...' : '(empty)';
            console.log('[Google Callback Debug] Page load', {
                pathname: location.pathname,
                searchKeys: [...q.keys()],
                hasHash: !!location.hash,
                hashPreview: h
            });
        })();
        @endif
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
        <div id="login-loading" class="login-loading active">
            <span class="login-loading-spinner"></span>
            <span class="login-loading-text">Đang xử lý đăng nhập...</span>
        </div>
        <div id="login-msg" class="login-msg"></div>
    </div>
@endsection
