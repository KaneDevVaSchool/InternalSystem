<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Đăng nhập thành công</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Figtree', system-ui, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
            color: #e2e8f0;
            -webkit-font-smoothing: antialiased;
        }
        .success-card {
            text-align: center;
            padding: 2.5rem;
            animation: success-fade-in 0.5s ease;
        }
        .success-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            background: rgba(34, 197, 94, 0.2);
            border: 2px solid #22c55e;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: success-scale 0.4s ease 0.2s both;
        }
        .success-icon::after {
            content: '';
            width: 20px;
            height: 10px;
            border-left: 2px solid #22c55e;
            border-bottom: 2px solid #22c55e;
            transform: rotate(-45deg);
            margin-bottom: 6px;
        }
        .success-card h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #4ade80;
        }
        .success-card p {
            color: #94a3b8;
            font-size: 0.9rem;
        }
        .btn-close {
            margin-top: 1.5rem;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            background: #22c55e;
            color: #0f172a;
            transition: background 0.2s ease, transform 0.2s ease;
        }
        .btn-close:hover {
            background: #4ade80;
        }
        .btn-close:active {
            transform: scale(0.98);
        }
        @keyframes success-fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes success-scale {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon"></div>
        <h2>Đăng nhập thành công!</h2>
        <p>Nhấn đóng để chuyển đến trang chủ.</p>
        <button type="button" class="btn-close" id="btn-close">Đóng</button>
    </div>
    <script>
        (function() {
            var token = @json($token);
            var homeUrl = @json($homeUrl);
            if (token && homeUrl) {
                try { localStorage.setItem('auth_token', token); } catch (e) {}
                document.getElementById('btn-close').onclick = function() {
                    window.location.href = homeUrl;
                };
            }
        })();
    </script>
</body>
</html>
