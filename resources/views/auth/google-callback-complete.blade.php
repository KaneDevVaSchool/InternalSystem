<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Đăng nhập thành công</title>
    <style>
        body { font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .msg { color: #0a0; }
    </style>
</head>
<body>
    <p class="msg">Đang chuyển hướng...</p>
    <script>
        (function() {
            var token = @json($token);
            var homeUrl = @json($homeUrl);
            if (token && homeUrl) {
                try { localStorage.setItem('auth_token', token); } catch (e) {}
                window.location.href = homeUrl;
            }
        })();
    </script>
</body>
</html>
