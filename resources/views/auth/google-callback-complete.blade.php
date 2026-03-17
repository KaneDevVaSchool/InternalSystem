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
        .success-loading {
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.875rem;
        }
        .success-loading .dot {
            width: 6px;
            height: 6px;
            background: #22c55e;
            border-radius: 50%;
            animation: success-dot 1.2s ease-in-out infinite;
        }
        .success-loading .dot:nth-child(2) { animation-delay: 0.2s; }
        .success-loading .dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes success-fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes success-scale {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        @keyframes success-dot {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon"></div>
        <h2>Đăng nhập thành công!</h2>
        <p>Đang chuyển hướng đến trang chủ...</p>
        <div class="success-loading">
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </div>
    <script>
        (function() {
            var token = @json($token);
            var homeUrl = @json($homeUrl);
            if (token && homeUrl) {
                try { localStorage.setItem('auth_token', token); } catch (e) {}
                setTimeout(function() {
                    window.location.href = homeUrl;
                }, 800);
            }
        })();
    </script>
</body>
</html>
