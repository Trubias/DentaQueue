<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Client — DentaQueue</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-deeper: #1e3a8a;
            --primary-light: #dbeafe;
            --accent: #06b6d4;
            --sidebar-w: 268px;
            --topbar-h: 60px;
            --radius: 14px;
            --transition: 0.22s cubic-bezier(0.4,0,0.2,1);
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f0f4ff;
            min-height: 100vh;
            color: #1e293b;
        }

        /* ═══════════════ MOBILE TOP BAR ═══════════════ */
        .mobile-topbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 16px;
            height: var(--topbar-h);
            background: linear-gradient(135deg, var(--primary-deeper) 0%, var(--primary) 100%);
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 4px 24px rgba(37,99,235,0.35);
        }
        .mobile-topbar .logo-area { display: flex; align-items: center; gap: 10px; }
        .mobile-topbar .logo-circle {
            width: 36px; height: 36px; border-radius: 10px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.28);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 13px; color: white;
            letter-spacing: -0.5px;
        }
        .mobile-topbar .brand { font-weight: 800; font-size: 17px; color: white; letter-spacing: -0.3px; }
        .hamburger-btn {
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 10px;
            width: 40px; height: 40px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 5px; cursor: pointer; transition: background var(--transition);
        }
        .hamburger-btn:hover { background: rgba(255,255,255,0.24); }
        .hamburger-btn span {
            display: block; width: 18px; height: 2px;
            background: white; border-radius: 2px;
            transition: transform 0.3s, opacity 0.3s, width 0.3s;
        }
        .hamburger-btn.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger-btn.open span:nth-child(2) { opacity: 0; width: 0; }
        .hamburger-btn.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* ═══════════════ SIDEBAR OVERLAY ═══════════════ */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(15,23,42,0.55); z-index: 200;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
        .sidebar-overlay.active { display: block; animation: fadeIn 0.2s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* ═══════════════ SIDEBAR ═══════════════ */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100%;
            background: linear-gradient(180deg, #0f172a 0%, #1e3a8a 55%, #1d4ed8 100%);
            color: white; display: flex; flex-direction: column;
            z-index: 300;
            transform: translateX(-100%);
            transition: transform 0.32s cubic-bezier(0.4,0,0.2,1);
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: none;
        }
        .sidebar::-webkit-scrollbar { display: none; }
        .sidebar.open { transform: translateX(0); box-shadow: 12px 0 48px rgba(0,0,0,0.4); }

        .sidebar-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 22px 18px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            flex-shrink: 0;
        }
        .sidebar-logo { display: flex; align-items: center; gap: 12px; }
        .sidebar-logo-img {
            width: 46px; height: 46px; border-radius: 13px;
            background: linear-gradient(135deg, rgba(255,255,255,0.25), rgba(255,255,255,0.08));
            border: 1px solid rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 14px; flex-shrink: 0; overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        }
        .sidebar-logo-img img { width: 100%; height: 100%; object-fit: contain; }
        .sidebar-brand { font-weight: 800; font-size: 17px; letter-spacing: -0.4px; }
        .sidebar-role {
            font-size: 11px; color: rgba(255,255,255,0.5);
            font-weight: 500; letter-spacing: 0.3px; margin-top: 1px;
        }
        .close-btn {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 9px; width: 34px; height: 34px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: rgba(255,255,255,0.7);
            transition: all var(--transition); flex-shrink: 0;
        }
        .close-btn:hover { background: rgba(255,255,255,0.18); color: white; }

        /* ─── User avatar section ─── */
        .sidebar-user {
            display: flex; align-items: center; gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;
        }
        .sidebar-user-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, #06b6d4, #2563eb);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 15px; color: white;
            border: 2px solid rgba(255,255,255,0.2);
            flex-shrink: 0; overflow: hidden;
        }
        .sidebar-user-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .sidebar-user-name { font-weight: 600; font-size: 14px; line-height: 1.3; }
        .sidebar-user-label { font-size: 11px; color: rgba(255,255,255,0.45); font-weight: 400; }

        /* ─── Nav ─── */
        nav.sidebar-nav { flex: 1; padding: 14px 12px; }
        .nav-section-label {
            font-size: 9.5px; font-weight: 700; letter-spacing: 1.2px;
            color: rgba(255,255,255,0.35); text-transform: uppercase;
            padding: 0 10px; margin: 16px 0 6px;
        }
        .nav-section-label:first-child { margin-top: 4px; }

        .sidebar-nav a {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 12px; border-radius: 11px;
            color: rgba(255,255,255,0.7); text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            transition: background var(--transition), color var(--transition), transform var(--transition);
            margin-bottom: 2px; position: relative;
            letter-spacing: -0.1px;
        }
        .sidebar-nav a .nav-icon {
            width: 34px; height: 34px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,0.07);
            transition: background var(--transition);
            flex-shrink: 0;
        }
        .sidebar-nav a:hover {
            background: rgba(255,255,255,0.09);
            color: white; transform: translateX(2px);
        }
        .sidebar-nav a:hover .nav-icon { background: rgba(255,255,255,0.14); }
        .sidebar-nav a.active {
            background: rgba(255,255,255,0.14);
            color: white;
        }
        .sidebar-nav a.active .nav-icon {
            background: rgba(255,255,255,0.22);
        }
        .sidebar-nav a.active::before {
            content: ''; position: absolute;
            left: 0; top: 6px; bottom: 6px;
            width: 3px; border-radius: 0 3px 3px 0;
            background: #38bdf8;
        }
        .sidebar-nav a svg { width: 17px; height: 17px; flex-shrink: 0; }
        .nav-label { flex: 1; }
        .badge {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white; font-size: 10px; font-weight: 700;
            padding: 2px 7px; border-radius: 999px;
            box-shadow: 0 2px 6px rgba(239,68,68,0.4);
            min-width: 20px; text-align: center;
        }

        /* ─── Sign out ─── */
        .sidebar-signout {
            padding: 10px 12px 6px;
        }
        .signout-btn {
            width: 100%; padding: 10px 14px; border-radius: 11px;
            background: rgba(239,68,68,0.12);
            color: #fca5a5;
            border: 1px solid rgba(239,68,68,0.2);
            font-size: 13.5px; font-weight: 600;
            cursor: pointer; font-family: inherit;
            transition: all var(--transition);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .signout-btn:hover {
            background: rgba(239,68,68,0.22);
            color: #fda4a4;
            border-color: rgba(239,68,68,0.35);
        }

        .sidebar-footer {
            padding: 13px 18px;
            border-top: 1px solid rgba(255,255,255,0.07);
            font-size: 11px; color: rgba(255,255,255,0.35);
            text-align: center; letter-spacing: 0.2px;
            flex-shrink: 0;
        }

        /* ═══════════════ BOTTOM NAV (mobile only) ═══════════════ */
        .bottom-nav {
            display: flex;
            position: fixed; bottom: 0; left: 0; right: 0;
            background: white;
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -8px 30px rgba(0,0,0,0.08);
            z-index: 90;
            padding: 4px 0 max(8px, env(safe-area-inset-bottom));
        }
        .bottom-nav a {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 3px;
            text-decoration: none; color: #94a3b8;
            font-size: 9.5px; font-weight: 600;
            padding: 5px 0;
            transition: color var(--transition);
            position: relative;
        }
        .bottom-nav a .bn-icon {
            width: 32px; height: 32px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            transition: background var(--transition), color var(--transition);
        }
        .bottom-nav a svg { width: 20px; height: 20px; }
        .bottom-nav a:hover { color: var(--primary); }
        .bottom-nav a.active { color: var(--primary); }
        .bottom-nav a.active .bn-icon {
            background: var(--primary-light);
        }
        .bottom-nav .notif-dot {
            position: absolute; top: 4px; right: calc(50% - 18px);
            width: 7px; height: 7px; background: #ef4444;
            border-radius: 50%; border: 2px solid white;
        }
        .bottom-nav span.bn-label { line-height: 1; }

        /* ═══════════════ PAGE WRAPPER ═══════════════ */
        .page-wrapper {
            display: flex; flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1; padding: 20px 16px;
            padding-bottom: calc(78px + max(8px, env(safe-area-inset-bottom)));
        }

        /* ═══════════════ DESKTOP ═══════════════ */
        @media (min-width: 1024px) {
            .mobile-topbar { display: none; }
            .bottom-nav { display: none; }
            .sidebar-overlay { display: none !important; }
            .close-btn { display: none; }
            .sidebar {
                position: sticky; top: 0; left: auto;
                transform: none !important;
                width: var(--sidebar-w); height: 100vh;
                flex-shrink: 0;
                box-shadow: none;
            }
            .page-wrapper { flex-direction: row; }
            .main-content { padding: 32px 36px; padding-bottom: 32px; }
        }

        /* ═══════════════ SHARED PAGE STYLES ═══════════════ */
        .page-header {
            margin-bottom: 28px;
        }
        .page-title {
            font-size: 26px; font-weight: 800;
            color: #0f172a; letter-spacing: -0.8px;
            line-height: 1.2;
        }
        .page-subtitle {
            font-size: 14px; color: #64748b; margin-top: 4px; font-weight: 400;
        }

        .card {
            background: white;
            border-radius: var(--radius);
            border: 1px solid #e8edf5;
            box-shadow: 0 2px 12px rgba(15,23,42,0.05), 0 1px 3px rgba(15,23,42,0.04);
            overflow: hidden;
        }
        .card-header {
            padding: 18px 22px 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title {
            font-size: 15px; font-weight: 700; color: #0f172a;
        }
        .card-body { padding: 22px; }

        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px; border-radius: 10px;
            font-size: 13.5px; font-weight: 600; cursor: pointer;
            border: none; text-decoration: none; font-family: inherit;
            transition: all var(--transition); line-height: 1;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-deeper));
            box-shadow: 0 6px 18px rgba(37,99,235,0.4);
            transform: translateY(-1px);
        }
        .btn-success {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            box-shadow: 0 4px 12px rgba(22,163,74,0.3);
        }
        .btn-success:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(22,163,74,0.4); }
        .btn-danger {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            box-shadow: 0 4px 12px rgba(220,38,38,0.3);
        }
        .btn-danger:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(220,38,38,0.4); }
        .btn-ghost {
            background: #f1f5f9; color: #475569;
            border: 1px solid #e2e8f0;
        }
        .btn-ghost:hover { background: #e2e8f0; color: #334155; }
        .btn-sm { padding: 7px 13px; font-size: 12.5px; border-radius: 8px; }

        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; font-size: 13px; font-weight: 600;
            color: #374151; margin-bottom: 6px;
        }
        .form-control {
            width: 100%; padding: 10px 14px;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: 14px; font-family: inherit; color: #1e293b;
            background: #fafbff;
            transition: border-color var(--transition), box-shadow var(--transition);
            outline: none;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
            background: white;
        }
        .form-hint { font-size: 12px; color: #94a3b8; margin-top: 5px; }
        .form-error { font-size: 12px; color: #ef4444; margin-top: 5px; display: flex; align-items: center; gap: 4px; }

        .alert {
            padding: 12px 16px; border-radius: 10px;
            font-size: 13.5px; font-weight: 500;
            display: flex; align-items: flex-start; gap: 10px;
            margin-bottom: 18px;
        }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-info { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }

        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 999px;
            font-size: 12px; font-weight: 600; letter-spacing: 0.1px;
        }
        .status-pending { background: #fef9c3; color: #854d0e; }
        .status-confirmed { background: #dcfce7; color: #166534; }
        .status-completed { background: #dbeafe; color: #1e40af; }
        .status-missed { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #f3f4f6; color: #6b7280; }
    </style>
</head>
<body>
    <!-- Mobile Top Bar -->
    <div class="mobile-topbar" id="mobileTopbar">
        <div class="logo-area">
            <div class="logo-circle">DQ</div>
            <span class="brand">DentaQueue</span>
        </div>
        <button class="hamburger-btn" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="page-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <div class="sidebar-logo-img">
                        <img src="/images/logo.png" alt="Logo"
                             onerror="this.parentElement.innerHTML='<span style=\'font-weight:900;font-size:14px;\'>DQ</span>'" />
                    </div>
                    <div>
                        <div class="sidebar-brand">DentaQueue</div>
                        <div class="sidebar-role">Patient Portal</div>
                    </div>
                </div>
                <button class="close-btn" onclick="closeSidebar()" aria-label="Close menu">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>




            <nav class="sidebar-nav">
                <div class="nav-section-label">Navigation</div>

                <a href="{{ route('client.dashboard') }}" onclick="closeSidebar()" id="nav-home">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" fill="none"><path d="M3 12L12 3l9 9v9a1 1 0 0 1-1 1h-5v-5a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v5H4a1 1 0 0 1-1-1v-9z" fill="currentColor"/></svg>
                    </span>
                    <span class="nav-label">Home</span>
                </a>

                <a href="{{ route('client.book') }}" onclick="closeSidebar()" id="nav-book">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="14" x2="8.01" y2="14"/><line x1="12" y1="14" x2="12.01" y2="14"/><line x1="16" y1="14" x2="16.01" y2="14"/></svg>
                    </span>
                    <span class="nav-label">Book Appointment</span>
                </a>

                <a href="{{ route('client.appointments.index') }}" onclick="closeSidebar()" id="nav-appointments">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
                    </span>
                    <span class="nav-label">My Appointments</span>
                </a>

                @php
                    $unreadCount = 0;
                    if (auth()->check()) {
                        $unreadCount = \App\Models\Announcement::where('user_id', auth()->id())->where('read', false)->count();
                    }
                @endphp

                <a href="{{ route('client.notifications') }}" onclick="closeSidebar()" id="nav-notifications">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    </span>
                    <span class="nav-label">Notifications</span>
                    @if($unreadCount > 0)
                        <span class="badge">{{ $unreadCount }}</span>
                    @endif
                </a>

                <div class="nav-section-label">Account</div>

                <a href="{{ route('client.settings.index') }}" onclick="closeSidebar()" id="nav-settings">
                    <span class="nav-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9.6 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06A2 2 0 1 1 4.89 17l.06-.06A1.65 1.65 0 0 0 5.28 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9.4"/></svg>
                    </span>
                    <span class="nav-label">Settings</span>
                </a>

                @auth
                <div class="sidebar-signout">
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button type="submit" class="signout-btn">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
                @endauth
            </nav>

            <div class="sidebar-footer">📞 Contact: 09505555636</div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div style="max-width:920px; margin:0 auto;">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="bottom-nav" id="bottomNav">
        <a href="{{ route('client.dashboard') }}" title="Home" id="bn-home">
            <div class="bn-icon">
                <svg fill="none" viewBox="0 0 24 24"><path d="M3 12L12 3l9 9v9a1 1 0 0 1-1 1h-5v-5a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v5H4a1 1 0 0 1-1-1v-9z" fill="currentColor"/></svg>
            </div>
            <span class="bn-label">Home</span>
        </a>
        <a href="{{ route('client.book') }}" title="Book" id="bn-book">
            <div class="bn-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <span class="bn-label">Book</span>
        </a>
        <a href="{{ route('client.appointments.index') }}" title="Appointments" id="bn-appointments">
            <div class="bn-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
            </div>
            <span class="bn-label">Appts</span>
        </a>
        <a href="{{ route('client.notifications') }}" title="Notifications" id="bn-notifications" style="position:relative;">
            @if(isset($unreadCount) && $unreadCount > 0)
                <div class="notif-dot"></div>
            @endif
            <div class="bn-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </div>
            <span class="bn-label">Alerts</span>
        </a>
        <a href="{{ route('client.settings.index') }}" title="Settings" id="bn-settings">
            <div class="bn-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 8.57 19.4"/></svg>
            </div>
            <span class="bn-label">Settings</span>
        </a>
    </nav>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const btn = document.getElementById('hamburgerBtn');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
            btn.classList.toggle('open');
            document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
        }
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const btn = document.getElementById('hamburgerBtn');
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            btn.classList.remove('open');
            document.body.style.overflow = '';
        }
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) closeSidebar();
        });

        // Active state highlighting
        const path = window.location.pathname;
        const navMap = {
            '/client/dashboard': ['nav-home','bn-home'],
            '/client/book':      ['nav-book','bn-book'],
            '/client/appointments': ['nav-appointments','bn-appointments'],
            '/client/notifications': ['nav-notifications','bn-notifications'],
            '/client/settings':  ['nav-settings','bn-settings'],
        };
        Object.entries(navMap).forEach(([route, ids]) => {
            if (path.startsWith(route)) {
                ids.forEach(id => { const el = document.getElementById(id); if (el) el.classList.add('active'); });
            }
        });
    </script>
</body>
</html>
