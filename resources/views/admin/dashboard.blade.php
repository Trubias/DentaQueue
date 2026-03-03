@extends('layouts.admin')

@section('content')
<style>
    /* ── Page Header ── */
    .page-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 12px; margin-bottom: 28px;
    }
    .page-header-left { display: flex; align-items: center; gap: 14px; }
    .admin-avatar {
        width: 52px; height: 52px; border-radius: 50%;
        background: linear-gradient(135deg, #1e40af, #3b82f6);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; font-weight: 800; color: white;
        box-shadow: 0 4px 14px rgba(59,130,246,0.4); flex-shrink: 0;
        overflow: hidden;
    }
    .admin-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .page-title { font-size: 24px; font-weight: 800; color: #0f172a; line-height: 1.2; }
    .page-subtitle { font-size: 13px; color: #64748b; margin-top: 2px; }
    .signout-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 18px; border-radius: 10px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white; font-size: 13px; font-weight: 600;
        border: none; cursor: pointer;
        box-shadow: 0 4px 12px rgba(239,68,68,0.3);
        transition: transform 0.15s, box-shadow 0.15s;
        text-decoration: none;
    }
    .signout-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(239,68,68,0.4); }

    /* ── Stat Cards ── */
    .stats-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px; margin-bottom: 24px;
    }
    .stat-card {
        background: white; border-radius: 16px;
        padding: 20px 22px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid rgba(226,232,240,0.8);
        display: flex; align-items: center; gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon.blue   { background: rgba(59,130,246,0.12); color: #2563eb; }
    .stat-icon.green  { background: rgba(16,185,129,0.12); color: #059669; }
    .stat-icon.purple { background: rgba(139,92,246,0.12); color: #7c3aed; }
    .stat-value { font-size: 28px; font-weight: 800; color: #0f172a; line-height: 1; }
    .stat-label { font-size: 12px; color: #64748b; font-weight: 500; margin-top: 3px; }

    /* ── Section Cards ── */
    .section-grid {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 16px; margin-bottom: 16px;
    }
    @media(max-width:768px){ .section-grid { grid-template-columns: 1fr; } }

    .card {
        background: white; border-radius: 16px; padding: 22px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid rgba(226,232,240,0.8);
    }
    .card-title {
        font-size: 15px; font-weight: 700; color: #1e293b;
        margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
    }
    .card-title svg { color: #3b82f6; }

    /* upcoming list */
    .appt-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
    .appt-item {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 12px; border-radius: 10px;
        background: #f8fafc; border: 1px solid #f1f5f9;
        font-size: 13px; color: #334155;
    }
    .appt-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #3b82f6; flex-shrink: 0;
    }
    .appt-time { font-size: 11px; color: #64748b; margin-left: auto; white-space: nowrap; }

    /* queue list */
    .queue-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
    .queue-item {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 12px; border-radius: 10px;
        background: #f8fafc; border: 1px solid #f1f5f9;
        font-size: 13px; color: #334155;
    }
    .queue-num {
        width: 30px; height: 30px; border-radius: 8px;
        background: linear-gradient(135deg,#1e40af,#3b82f6);
        color: white; font-size: 11px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .queue-type {
        margin-left: auto;
        font-size: 11px; font-weight: 600; color: #64748b;
        background: #e2e8f0; padding: 2px 8px; border-radius: 999px;
    }

    /* empty state */
    .empty-state {
        display: flex; flex-direction: column; align-items: center;
        gap: 8px; padding: 24px 0; color: #94a3b8; font-size: 13px;
    }
    .empty-state svg { color: #cbd5e1; }

    /* Quick Actions */
    .quick-actions-grid {
        display: flex; flex-wrap: wrap; gap: 12px;
    }
    .quick-action-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 18px; border-radius: 10px;
        font-size: 13px; font-weight: 600;
        text-decoration: none;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .quick-action-btn.primary {
        background: linear-gradient(135deg,#1e40af,#3b82f6);
        color: white;
        box-shadow: 0 4px 12px rgba(59,130,246,0.3);
    }
    .quick-action-btn.secondary {
        background: #f1f5f9; color: #1e40af;
        border: 1px solid #e2e8f0;
    }
    .quick-action-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); }
</style>

<div class="max-w-7xl mx-auto">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-left">
            <div class="admin-avatar">
                @if(auth()->user() && auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Admin" />
                @else
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                @endif
            </div>
            <div>
                <div class="page-title">Admin Dashboard</div>
                <div class="page-subtitle">Welcome back, {{ auth()->user()->name ?? 'Admin' }} 👋</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="signout-btn">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Sign Out
            </button>
        </form>
    </div>

    {{-- Stat Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['today_appointments'] }}</div>
                <div class="stat-label">Today's Appointments</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM22 20v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['daily_clients'] }}</div>
                <div class="stat-label">Daily Clients</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M3 6h18M7 12h10M10 18h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['queue_length'] }}</div>
                <div class="stat-label">Queue Length</div>
            </div>
        </div>
    </div>

    {{-- Upcoming + Queue --}}
    <div class="section-grid">
        {{-- Upcoming Appointments --}}
        <div class="card">
            <div class="card-title">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Upcoming Appointments
            </div>
            @if($upcoming->isEmpty())
                <div class="empty-state">
                    <svg width="36" height="36" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M8 2v4M16 2v4M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    No upcoming appointments
                </div>
            @else
                <ul class="appt-list">
                    @foreach($upcoming as $u)
                        <li class="appt-item">
                            <span class="appt-dot"></span>
                            <div>
                                <div style="font-weight:600;">{{ $u->fullname }}</div>
                                <div style="font-size:11px;color:#64748b;">{{ $u->type }}</div>
                            </div>
                            <span class="appt-time">{{ $u->scheduled_at->format('M j, g:i A') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Next in Queue --}}
        <div class="card">
            <div class="card-title">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M3 6h18M7 12h10M10 18h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Next in Queue
            </div>
            @if($recent_pending->isEmpty())
                <div class="empty-state">
                    <svg width="36" height="36" fill="none" viewBox="0 0 24 24"><path d="M3 6h18M7 12h10M10 18h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    Queue is empty
                </div>
            @else
                <ul class="queue-list">
                    @foreach($recent_pending as $r)
                        <li class="queue-item">
                            <div class="queue-num">#{{ sprintf('%03d', $r->id) }}</div>
                            <div style="font-weight:600;">{{ $r->fullname }}</div>
                            <span class="queue-type">{{ $r->type }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card">
        <div class="card-title">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Quick Actions
        </div>
        <div class="quick-actions-grid">
            <a href="{{ route('admin.queue.index') }}" class="quick-action-btn primary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24"><path d="M3 6h18M7 12h10M10 18h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Manage Queue & Assign Slots
            </a>
            <a href="{{ route('admin.reports.index') }}" class="quick-action-btn secondary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24"><path d="M3 3v18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M9 17V9M15 17v-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                View Monthly Reports
            </a>
            <a href="{{ route('admin.announcements.index') }}" class="quick-action-btn secondary">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24"><path d="M4 6h16v10H4z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M22 6l-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Announcements
            </a>
        </div>
    </div>

</div>
@endsection
