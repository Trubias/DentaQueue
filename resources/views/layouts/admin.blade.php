<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin — DentaQueue</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
        }

        /* ──────────── MOBILE TOP BAR ──────────── */
        .mobile-topbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 16px;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: white;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 16px rgba(15,23,42,0.4);
        }
        .mobile-topbar .logo-area { display: flex; align-items: center; gap: 10px; }
        .mobile-topbar .logo-circle {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 13px; box-shadow: 0 2px 8px rgba(59,130,246,0.4);
        }
        .mobile-topbar .brand { font-weight: 700; font-size: 17px; }
        .hamburger-btn {
            background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);
            border-radius: 10px; width: 40px; height: 40px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 5px; cursor: pointer; transition: background 0.2s;
        }
        .hamburger-btn:hover { background: rgba(255,255,255,0.2); }
        .hamburger-btn span {
            display: block; width: 20px; height: 2px;
            background: white; border-radius: 2px; transition: transform 0.3s, opacity 0.3s;
        }
        .hamburger-btn.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger-btn.open span:nth-child(2) { opacity: 0; }
        .hamburger-btn.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* ──────────── SIDEBAR OVERLAY ──────────── */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 200; backdrop-filter: blur(3px);
        }
        .sidebar-overlay.active { display: block; }

        /* ──────────── SIDEBAR ──────────── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: 268px; height: 100%;
            background: linear-gradient(180deg, #0f172a 0%, #1e3a8a 60%, #1e40af 100%);
            color: white; display: flex; flex-direction: column;
            z-index: 300;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
            overflow-y: auto; overflow-x: hidden;
        }
        .sidebar::before {
            content: '';
            position: absolute; top: -60px; right: -60px;
            width: 200px; height: 200px; border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,0.25) 0%, transparent 70%);
            pointer-events: none;
        }
        .sidebar.open { transform: translateX(0); }

        /* Sidebar Header */
        .sidebar-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 22px 18px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            position: relative; z-index: 1;
        }
        .sidebar-logo { display: flex; align-items: center; gap: 12px; }
        .sidebar-logo-img {
            width: 46px; height: 46px; border-radius: 13px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 15px; flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(59,130,246,0.5); overflow: hidden;
        }
        .sidebar-logo-img img { width: 100%; height: 100%; object-fit: contain; }
        .sidebar-brand { font-weight: 800; font-size: 17px; letter-spacing: -.3px; }
        .sidebar-role {
            display: inline-block; margin-top: 4px;
            font-size: 10px; font-weight: 700; letter-spacing: .7px;
            text-transform: uppercase; color: rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.08); padding: 2px 8px;
            border-radius: 999px;
        }
        .close-btn {
            background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);
            border-radius: 9px; width: 34px; height: 34px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: white; transition: background 0.2s; flex-shrink: 0;
        }
        .close-btn:hover { background: rgba(255,255,255,0.18); }

        /* Nav */
        nav.sidebar-nav { flex: 1; padding: 16px 14px; position: relative; z-index: 1; }
        .nav-section-label {
            font-size: 10px; font-weight: 700; letter-spacing: 1.2px;
            color: rgba(255,255,255,0.35); text-transform: uppercase;
            padding: 0 10px; margin: 18px 0 6px;
            display: flex; align-items: center; gap: 8px;
        }
        .nav-section-label::after {
            content: ''; flex: 1; height: 1px;
            background: rgba(255,255,255,0.08);
        }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 11px;
            padding: 11px 12px; border-radius: 12px;
            color: rgba(255,255,255,0.7); text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            transition: all 0.18s ease;
            margin-bottom: 3px; position: relative;
        }
        .sidebar-nav a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(2px);
        }
        .sidebar-nav a.active {
            background: rgba(255,255,255,0.14);
            color: white; font-weight: 600;
            box-shadow: inset 3px 0 0 #60a5fa;
        }
        .sidebar-nav a.active svg { color: #93c5fd; }
        .sidebar-nav a svg { flex-shrink: 0; transition: color 0.18s; }
        .nav-icon-wrap {
            width: 30px; height: 30px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: background 0.18s;
        }
        .sidebar-nav a:hover .nav-icon-wrap,
        .sidebar-nav a.active .nav-icon-wrap {
            background: rgba(255,255,255,0.12);
        }

        /* Admin user card at bottom */
        .sidebar-user-card {
            margin: 12px 14px 18px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px; padding: 13px 14px;
            display: flex; align-items: center; gap: 12px;
            position: relative; z-index: 1;
        }
        .sidebar-user-avatar {
            width: 38px; height: 38px; border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 800; color: white; flex-shrink: 0;
            overflow: hidden;
        }
        .sidebar-user-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .sidebar-user-name { font-size: 13px; font-weight: 700; color: white; }
        .sidebar-user-role { font-size: 11px; color: rgba(255,255,255,0.5); margin-top: 1px; }
        .sidebar-user-logout {
            margin-left: auto; background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 8px; padding: 5px 9px;
            color: #fca5a5; font-size: 11px; font-weight: 600;
            cursor: pointer; white-space: nowrap; font-family: inherit;
            transition: background 0.2s;
        }
        .sidebar-user-logout:hover { background: rgba(239,68,68,0.25); }

        /* ──────────── MAIN CONTENT ──────────── */
        .page-wrapper { min-height: 100vh; display: flex; flex-direction: column; }
        .main-content { flex: 1; padding: 20px 16px; max-width: 100%; }

        /* ──────────── DESKTOP ──────────── */
        @media (min-width: 1024px) {
            .mobile-topbar { display: none; }
            .sidebar {
                position: sticky; top: 0; left: auto;
                transform: none; width: 262px; height: 100vh; flex-shrink: 0;
            }
            .sidebar-overlay { display: none !important; }
            .close-btn { display: none; }
            .page-wrapper { flex-direction: row; }
            .main-content { padding: 28px 36px; }
        }
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
                             onerror="this.parentElement.innerHTML='<span style=\'font-weight:800;font-size:15px;\'>DQ</span>'"
                        />
                    </div>
                    <div>
                        <div class="sidebar-brand">DentaQueue</div>
                        <span class="sidebar-role">Admin Panel</span>
                    </div>
                </div>
                <button class="close-btn" onclick="closeSidebar()" aria-label="Close menu">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <nav class="sidebar-nav" id="sidebarNav">
                <div class="nav-section-label">Main</div>
                <a href="{{ route('admin.dashboard') }}" onclick="closeSidebar()">
                    <div class="nav-icon-wrap">
                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24"><path d="M3 11.5L12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V11.5z" fill="currentColor"/></svg>
                    </div>
                    Dashboard
                </a>
                <a href="{{ route('admin.queue.index') }}" onclick="closeSidebar()">
                    <div class="nav-icon-wrap">
                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24"><path d="M3 6h18M7 12h10M10 18h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                    Client Queue
                </a>

                <div class="nav-section-label">Management</div>
                <a href="{{ route('admin.schedule.index') }}" onclick="closeSidebar()">
                    <div class="nav-icon-wrap">
                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                    Appointments
                </a>
                <a href="{{ route('admin.announcements.index') }}" onclick="closeSidebar()">
                    <div class="nav-icon-wrap">
                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24"><path d="M4 6h16v10H4z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M22 6l-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                    Announcements
                </a>
                <a href="{{ route('admin.reports.index') }}" onclick="closeSidebar()">
                    <div class="nav-icon-wrap">
                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24"><path d="M3 3v18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M9 17V9M15 17v-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </div>
                    Reports
                </a>
                <a href="{{ route('admin.users.index') }}" onclick="closeSidebar()">
                    <div class="nav-icon-wrap">
                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24"><path d="M16 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM6 11c1.657 0 3-1.343 3-3S7.657 5 6 5 3 6.343 3 8s1.343 3 3 3z" fill="currentColor"/><path d="M2 20c0-3.314 2.686-6 6-6h8c3.314 0 6 2.686 6 6" stroke="currentColor" stroke-linecap="round"/></svg>
                    </div>
                    Users Management
                </a>

                <div class="nav-section-label">System</div>
                <a href="{{ route('admin.settings.index') }}" onclick="closeSidebar()">
                    <div class="nav-icon-wrap">
                        <svg width="17" height="17" fill="none" viewBox="0 0 24 24"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" fill="currentColor"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06A2 2 0 1 1 2.27 17.9l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </div>
                    Settings
                </a>
            </nav>

            <!-- Admin User Card -->
            @auth
            <div class="sidebar-user-card">
                <div class="sidebar-user-avatar">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="avatar" />
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    @endif
                </div>
                <div style="min-width:0; flex:1;">
                    <div class="sidebar-user-name" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">Administrator</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="sidebar-user-logout">Sign out</button>
                </form>
            </div>
            @endauth
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    @stack('scripts')

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

        // Active link highlighting
        document.addEventListener('DOMContentLoaded', function() {
            const path = window.location.pathname;
            document.querySelectorAll('#sidebarNav a').forEach(link => {
                const href = link.getAttribute('href');
                if (href && path.startsWith(href) && href !== '/') {
                    link.classList.add('active');
                } else if (href === window.location.pathname) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
