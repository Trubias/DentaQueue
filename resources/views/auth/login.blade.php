<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Log In — DentaQueue</title>
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

        /* ── HEADER ── */
        .dq-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 20px;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.6);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .dq-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .dq-logo .circle {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e40af, #2563eb);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 13px;
            box-shadow: 0 4px 12px rgba(37,99,235,0.35);
            flex-shrink: 0;
        }
        .dq-logo .brand-name {
            font-weight: 700;
            font-size: 16px;
            color: #1e3a5f;
        }
        .dq-header nav a {
            font-size: 14px;
            font-weight: 600;
            color: #16a34a;
            text-decoration: none;
            padding: 8px 16px;
            border: 2px solid #16a34a;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .dq-header nav a:hover {
            background: #16a34a;
            color: white;
        }

        /* ── MAIN ── */
        .dq-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 16px 48px;
            gap: 32px;
            position: relative;
            overflow: hidden;
        }

        /* decorative blobs */
        .dq-main::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37,99,235,0.08) 0%, transparent 70%);
            top: -100px; left: -100px;
            pointer-events: none;
        }
        .dq-main::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(22,163,74,0.08) 0%, transparent 70%);
            bottom: -80px; right: -60px;
            pointer-events: none;
        }

        /* ── HERO ── */
        .dq-hero {
            text-align: center;
            max-width: 480px;
            z-index: 1;
        }
        .dq-hero .tooth-icon {
            font-size: 52px;
            margin-bottom: 12px;
            display: block;
            filter: drop-shadow(0 4px 8px rgba(37,99,235,0.2));
        }
        .dq-hero h1 {
            font-size: clamp(22px, 5vw, 36px);
            font-weight: 800;
            color: #1e3a5f;
            line-height: 1.2;
            margin-bottom: 10px;
        }
        .dq-hero h1 span { color: #2563eb; }
        .dq-hero p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
        }

        /* ── CARD ── */
        .dq-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.8);
            box-shadow: 0 20px 60px rgba(0,0,0,0.10), 0 4px 16px rgba(37,99,235,0.08);
            padding: 32px 28px;
            z-index: 1;
            animation: slideUp 0.45s ease forwards;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .dq-card h2 {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .dq-card .subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 24px;
        }

        /* form elements */
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap .input-icon {
            position: absolute;
            left: 12px; top: 50%; transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
            width: 18px; height: 18px;
        }
        .input-wrap input {
            width: 100%;
            padding: 11px 40px 11px 38px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            color: #0f172a;
            background: #f8fafc;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
            font-family: inherit;
        }
        .input-wrap input:focus {
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .input-wrap .toggle-pw {
            position: absolute;
            right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #9ca3af; padding: 2px;
            display: flex; align-items: center;
            transition: color 0.2s;
        }
        .input-wrap .toggle-pw:hover { color: #2563eb; }
        .error-msg { font-size: 12px; color: #dc2626; margin-top: 5px; }

        .row-remember {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 8px;
        }
        .row-remember label {
            display: flex; align-items: center; gap: 7px;
            font-size: 13px; color: #374151; cursor: pointer;
            font-weight: 400;
        }
        .row-remember input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: #2563eb;
            cursor: pointer;
        }
        .row-remember a {
            font-size: 13px;
            font-weight: 600;
            color: #16a34a;
            text-decoration: none;
            transition: color 0.2s;
        }
        .row-remember a:hover { color: #15803d; text-decoration: underline; }

        .btn-primary {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.2s, opacity 0.2s;
            box-shadow: 0 6px 20px rgba(37,99,235,0.35);
            font-family: inherit;
            letter-spacing: 0.3px;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(37,99,235,0.4); }
        .btn-primary:active { transform: translateY(0); }

        .session-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        /* ── DESKTOP layout ── */
        @media (min-width: 800px) {
            .dq-main {
                flex-direction: row;
                align-items: center;
                justify-content: center;
                gap: 60px;
                padding: 60px 40px;
            }
            .dq-hero {
                text-align: left;
                flex: 1;
                max-width: 440px;
            }
            .dq-card {
                max-width: 400px;
                flex-shrink: 0;
            }
        }

        /* divider */
        .card-divider {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin: 18px 0 0;
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
            <a href="{{ route('register') }}">Register</a>
        </nav>
    </header>

    <main class="dq-main">
        <!-- Hero -->
        <div class="dq-hero">
            <span class="tooth-icon">🦷</span>
            <h1>Dental appointments,<br><span>without the wait</span></h1>
            <p>Sign in to manage your appointments and view notifications at your fingertips.</p>
        </div>

        <!-- Card -->
        <div class="dq-card">
            <h2>Welcome back</h2>
            <p class="subtitle">Log in to your DentaQueue account</p>

            @if(session('status'))
                <div class="session-error" style="background:#f0fdf4;border-color:#bbf7d0;color:#15803d;">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->has('email') && str_contains($errors->first('email'), 'These credentials'))
                <div class="session-error">{{ $errors->first('email') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email address</label>
                    <div class="input-wrap">
                        <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input id="email" name="email" type="email" required autofocus autocomplete="email"
                               value="{{ old('email') }}" placeholder="you@example.com">
                    </div>
                    @error('email')
                        @if(!str_contains($message, 'These credentials'))
                            <div class="error-msg">{{ $message }}</div>
                        @endif
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input id="password" name="password" type="password" required
                               autocomplete="current-password" placeholder="••••••••">
                        <button type="button" class="toggle-pw" onclick="togglePw()" aria-label="Toggle password">
                            <svg id="eye-open" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="eye-closed" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row-remember">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <button type="submit" class="btn-primary">Log In</button>

                <p class="card-divider">Don't have an account? <a href="{{ route('register') }}" style="color:#2563eb;font-weight:600;text-decoration:none;">Sign up</a></p>
            </form>
        </div>
    </main>

    <script>
        function togglePw() {
            const inp = document.getElementById('password');
            const open = document.getElementById('eye-open');
            const closed = document.getElementById('eye-closed');
            if (inp.type === 'password') {
                inp.type = 'text';
                open.style.display = 'none';
                closed.style.display = 'block';
            } else {
                inp.type = 'password';
                open.style.display = 'block';
                closed.style.display = 'none';
            }
        }
    </script>
</body>
</html>