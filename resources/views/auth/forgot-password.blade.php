<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Forgot Password — DentaQueue</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #e0f2fe 0%, #f0fdf4 50%, #ede9fe 100%);
            display: flex;
            flex-direction: column;
        }
        .dq-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 14px 20px;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.6);
            position: sticky; top: 0; z-index: 50;
        }
        .dq-logo {
            display: flex; align-items: center; gap: 10px; text-decoration: none;
        }
        .dq-logo .circle {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, #1e40af, #2563eb);
            color: white; display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 13px;
            box-shadow: 0 4px 12px rgba(37,99,235,0.35); flex-shrink: 0;
        }
        .dq-logo .brand-name { font-weight: 700; font-size: 16px; color: #1e3a5f; }
        .dq-header nav { display: flex; gap: 10px; }
        .dq-header nav a {
            font-size: 13px; font-weight: 600; text-decoration: none;
            padding: 7px 14px; border-radius: 8px; border: 2px solid transparent;
            transition: all 0.2s;
        }
        .nav-login { color: #2563eb; border-color: #2563eb; }
        .nav-login:hover { background: #2563eb; color: white; }
        .nav-register { color: #16a34a; border-color: #16a34a; }
        .nav-register:hover { background: #16a34a; color: white; }

        .dq-main {
            flex: 1; display: flex; flex-direction: column; align-items: center;
            justify-content: center; padding: 40px 16px 56px; gap: 32px;
            position: relative; overflow: hidden;
        }
        .dq-main::before {
            content: ''; position: absolute; width: 350px; height: 350px;
            border-radius: 50%; background: radial-gradient(circle, rgba(37,99,235,0.08) 0%, transparent 70%);
            top: -80px; left: -80px; pointer-events: none;
        }

        .dq-hero { text-align: center; max-width: 440px; z-index: 1; }
        .dq-hero .icon { font-size: 52px; margin-bottom: 12px; display: block; }
        .dq-hero h1 { font-size: clamp(20px, 5vw, 30px); font-weight: 800; color: #1e3a5f; margin-bottom: 8px; }
        .dq-hero p { font-size: 14px; color: #64748b; line-height: 1.6; }

        .dq-card {
            width: 100%; max-width: 420px;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border-radius: 20px; border: 1px solid rgba(255,255,255,0.8);
            box-shadow: 0 20px 60px rgba(0,0,0,0.10), 0 4px 16px rgba(37,99,235,0.08);
            padding: 32px 28px; z-index: 1;
            animation: slideUp 0.45s ease forwards;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .dq-card h2 { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .dq-card .subtitle { font-size: 13px; color: #64748b; margin-bottom: 22px; line-height: 1.5; }

        .success-msg {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            color: #15803d; border-radius: 8px;
            padding: 10px 14px; font-size: 13px; margin-bottom: 18px;
        }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .input-wrap { position: relative; }
        .input-wrap .input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #9ca3af; pointer-events: none; width: 17px; height: 17px;
        }
        .input-wrap input {
            width: 100%; padding: 11px 12px 11px 38px;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: 15px; color: #0f172a; background: #f8fafc;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none; font-family: inherit;
        }
        .input-wrap input:focus {
            border-color: #2563eb; background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .error-msg { font-size: 12px; color: #dc2626; margin-top: 4px; }

        .btn-primary {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white; border: none; border-radius: 10px;
            font-size: 15px; font-weight: 700; cursor: pointer;
            transition: transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 6px 20px rgba(37,99,235,0.35);
            font-family: inherit; letter-spacing: 0.3px;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(37,99,235,0.4); }
        .btn-primary:active { transform: translateY(0); }

        .back-link {
            display: block; text-align: center; margin-top: 16px;
            font-size: 13px; color: #64748b; text-decoration: none;
        }
        .back-link a { color: #2563eb; font-weight: 600; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }

        @media (min-width: 800px) {
            .dq-main { flex-direction: row; align-items: center; justify-content: center; gap: 60px; padding: 60px 40px; }
            .dq-hero { text-align: left; flex: 1; max-width: 400px; }
            .dq-card { max-width: 400px; flex-shrink: 0; }
        }
    </style>
</head>
<body>
    <header class="dq-header">
        <a href="{{ url('/') }}" class="dq-logo">
            <div class="circle">DQ</div>
            <span class="brand-name">DentaQueue</span>
        </a>
        <nav>
            <a href="{{ route('login') }}" class="nav-login">Log In</a>
            <a href="{{ route('register') }}" class="nav-register">Register</a>
        </nav>
    </header>

    <main class="dq-main">
        <div class="dq-hero">
            <span class="icon">🔐</span>
            <h1>Reset your password</h1>
            <p>Enter the email address linked to your account and we'll send you a password reset link right away.</p>
        </div>

        <div class="dq-card">
            <h2>Forgot your password?</h2>
            <p class="subtitle">No worries — it happens! Enter your email and we'll help you get back in.</p>

            @if(session('status'))
                <div class="success-msg">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Email address</label>
                    <div class="input-wrap">
                        <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input id="email" name="email" type="email" required autofocus
                               value="{{ old('email') }}" placeholder="you@example.com">
                    </div>
                    @error('email')<div class="error-msg">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn-primary">Send Reset Link</button>
            </form>

            <p class="back-link">Remembered it? <a href="{{ route('login') }}">Back to Log In</a></p>
        </div>
    </main>
</body>
</html>
