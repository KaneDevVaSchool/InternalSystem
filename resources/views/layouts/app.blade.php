<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-api-url="{{ url('/api') }}"
      data-home-url="{{ url('/home') }}"
      data-login-url="{{ url('/') }}"
      data-google-client-id="{{ config('auth.google.client_id') }}"
      data-app-debug="{{ config('app.debug') ? 'true' : 'false' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('head')
    @stack('styles')
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>
