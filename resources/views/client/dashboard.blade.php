@extends('layouts.client')

@section('content')
<style>
    /* ── Dashboard Hero ── */
    .dash-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #0ea5e9 100%);
        border-radius: 18px;
        padding: 28px 28px 24px;
        color: white;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(37,99,235,0.35);
    }
    .dash-hero::before {
        content: '';
        position: absolute; top: -40px; right: -40px;
        width: 180px; height: 180px;
        background: rgba(255,255,255,0.07);
        border-radius: 50%;
    }
    .dash-hero::after {
        content: '';
        position: absolute; bottom: -60px; right: 80px;
        width: 140px; height: 140px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .hero-top { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
    .hero-user { display: flex; align-items: center; gap: 14px; }
    .hero-avatar {
        width: 54px; height: 54px; border-radius: 50%;
        border: 3px solid rgba(255,255,255,0.35);
        background: linear-gradient(135deg, rgba(255,255,255,0.3), rgba(255,255,255,0.1));
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 22px; color: white;
        overflow: hidden; flex-shrink: 0;
    }
    .hero-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .hero-greeting { font-size: 13px; color: rgba(255,255,255,0.65); font-weight: 400; }
    .hero-name { font-size: 22px; font-weight: 800; letter-spacing: -0.5px; margin-top: 2px; }
    .hero-actions { display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1; }
    .hero-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 16px; border-radius: 10px;
        font-size: 13px; font-weight: 600; cursor: pointer;
        border: none; text-decoration: none; font-family: inherit;
        transition: all 0.2s ease; line-height: 1;
    }
    .hero-btn-white {
        background: white; color: #1e3a8a;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .hero-btn-white:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,0,0,0.2); }
    .hero-btn-outline {
        background: rgba(255,255,255,0.15);
        color: white;
        border: 1.5px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(4px);
    }
    .hero-btn-outline:hover { background: rgba(255,255,255,0.25); }

    /* ── Alert banner ── */
    .unread-banner {
        background: linear-gradient(90deg, #fef3c7, #fef9c3);
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 12px 16px;
        display: flex; align-items: center; gap: 10px;
        font-size: 13.5px; font-weight: 500; color: #92400e;
        margin-bottom: 20px;
    }
    .unread-banner svg { flex-shrink: 0; }

    /* ── Grid ── */
    .dash-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    @media (max-width: 680px) { .dash-grid { grid-template-columns: 1fr; } }

    /* ── Upcoming Card ── */
    .upcoming-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(15,23,42,0.05);
    }
    .upcoming-header {
        padding: 16px 20px 14px;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 10px;
    }
    .upcoming-icon {
        width: 36px; height: 36px; border-radius: 10px;
        background: linear-gradient(135deg, #dbeafe, #eff6ff);
        display: flex; align-items: center; justify-content: center;
        color: #2563eb;
    }
    .upcoming-title { font-size: 14px; font-weight: 700; color: #0f172a; }
    .upcoming-body { padding: 18px 20px; }
    .appt-info-row {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #f8fafc;
    }
    .appt-info-row:last-child { border-bottom: none; }
    .appt-info-label { font-size: 12px; color: #94a3b8; font-weight: 500; min-width: 60px; padding-top: 1px; }
    .appt-info-value { font-size: 13.5px; font-weight: 600; color: #1e293b; }
    .countdown-chip {
        display: inline-flex; align-items: center; gap: 5px;
        background: #f0fdf4; color: #15803d;
        border: 1px solid #bbf7d0;
        padding: 3px 10px; border-radius: 999px;
        font-size: 12px; font-weight: 600;
    }
    .appt-actions { display: flex; gap: 8px; margin-top: 14px; }
    .queue-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        color: #5b21b6; padding: 5px 12px;
        border-radius: 999px; font-size: 12.5px; font-weight: 700;
        margin-top: 10px;
    }
    .empty-state {
        text-align: center; padding: 24px 16px;
        color: #94a3b8;
    }
    .empty-state svg { margin: 0 auto 10px; opacity: 0.4; }
    .empty-state p { font-size: 13.5px; }
    .empty-state a { color: #2563eb; font-weight: 600; text-decoration: none; }
    .empty-state a:hover { text-decoration: underline; }

    /* ── Announcements Card ── */
    .ann-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(15,23,42,0.05);
        display: flex; flex-direction: column;
    }
    .ann-header {
        padding: 16px 20px 14px;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 10px;
    }
    .ann-icon {
        width: 36px; height: 36px; border-radius: 10px;
        background: linear-gradient(135deg, #fce7f3, #fdf2f8);
        display: flex; align-items: center; justify-content: center;
        color: #db2777;
    }
    .ann-title { font-size: 14px; font-weight: 700; color: #0f172a; }
    .ann-list { flex: 1; overflow-y: auto; max-height: 220px; padding: 10px 16px; scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent; }
    .ann-item {
        padding: 11px 0;
        border-bottom: 1px solid #f8fafc;
        cursor: pointer;
    }
    .ann-item:last-child { border-bottom: none; }
    .ann-item-title { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 3px; }
    .ann-item-body { font-size: 12px; color: #64748b; line-height: 1.5; }
    .ann-item-date { font-size: 11px; color: #94a3b8; margin-top: 4px; }

    /* ── Recent Appts ── */
    .recent-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e8edf5;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(15,23,42,0.05);
        margin-top: 18px;
    }
    .recent-header {
        padding: 16px 20px 14px;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 10px;
    }
    .recent-icon {
        width: 36px; height: 36px; border-radius: 10px;
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        display: flex; align-items: center; justify-content: center;
        color: #16a34a;
    }
    .recent-list { padding: 6px 12px 10px; }
    .recent-row {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 8px; border-radius: 10px;
        transition: background 0.15s;
        cursor: default;
    }
    .recent-row:hover { background: #f8fafc; }
    .recent-num { font-size: 12px; font-weight: 700; color: #94a3b8; min-width: 36px; }
    .recent-type { font-size: 13.5px; font-weight: 600; color: #1e293b; flex: 1; }
    .recent-date { font-size: 12px; color: #94a3b8; }
</style>

<!-- Hero -->
<div class="dash-hero">
    <div class="hero-top">
        <div class="hero-user">
            <div class="hero-avatar">
                @if(auth()->user() && auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="User" />
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div>
                <div class="hero-greeting">Good day 👋</div>
                <div class="hero-name">Welcome, {{ $user->name }}</div>
            </div>
        </div>
        <div class="hero-actions">
            <a href="{{ route('client.book') }}" class="hero-btn hero-btn-white">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Book Appointment
            </a>
            <a href="{{ route('client.appointments.index') }}" class="hero-btn hero-btn-outline">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                My Appointments
            </a>
        </div>
    </div>
</div>

@if(!empty($unreadAnnouncements) && $unreadAnnouncements > 0)
<div class="unread-banner">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
    You have <strong>&nbsp;{{ $unreadAnnouncements }} new message(s)&nbsp;</strong>. <a href="{{ route('client.notifications') }}" style="color:#b45309; font-weight:700; text-decoration:none; margin-left:4px;">View Notifications →</a>
</div>
@endif

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:18px;">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    {{ session('success') }}
</div>
@endif

<!-- Main Grid -->
<div class="dash-grid">
    <!-- Upcoming Appointment -->
    <div class="upcoming-card">
        <div class="upcoming-header">
            <div class="upcoming-icon">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <span class="upcoming-title">Upcoming Appointment</span>
        </div>
        <div class="upcoming-body">
            @if($next)
                <div class="appt-info-row">
                    <span class="appt-info-label">Type</span>
                    <span class="appt-info-value">{{ $next->type }}</span>
                </div>
                <div class="appt-info-row">
                    <span class="appt-info-label">Date</span>
                    <span class="appt-info-value">{{ $next->scheduled_at ? $next->scheduled_at->format('M j, Y \a\t g:i A') : 'TBD' }}</span>
                </div>
                @if($next->scheduled_at)
                <div class="appt-info-row">
                    <span class="appt-info-label">In</span>
                    <span>
                        <span class="countdown-chip">
                            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ \Carbon\Carbon::now()->diffForHumans($next->scheduled_at, true) }} {{ (\Carbon\Carbon::now()->lt($next->scheduled_at) ? 'from now' : 'ago') }}
                        </span>
                    </span>
                </div>
                @endif
                <div class="appt-info-row">
                    <span class="appt-info-label">Status</span>
                    <span>
                        @php $s = $next->status; @endphp
                        <span class="status-badge status-{{ $s }}">{{ ucfirst($s) }}</span>
                    </span>
                </div>
                @if($position)
                    <div class="queue-badge">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Queue Position: #{{ $position }}
                    </div>
                @endif
                <div class="appt-actions">
                    <a href="{{ route('client.appointments.edit', $next) }}" class="btn btn-primary btn-sm">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('client.appointments.destroy', $next) }}" onsubmit="return confirm('Cancel this appointment?');" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                            Cancel
                        </button>
                    </form>
                </div>
            @else
                <div class="empty-state">
                    <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <p>No upcoming appointments.<br><a href="{{ route('client.book') }}">Book one now →</a></p>
                </div>
            @endif
        </div>
    </div>

    <!-- Announcements -->
    <div class="ann-card">
        <div class="ann-header">
            <div class="ann-icon">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </div>
            <span class="ann-title">Latest Announcements</span>
        </div>
        <div class="ann-list" id="announcementsList">
            @if(isset($announcements) && $announcements->count())
                @foreach($announcements as $an)
                    <div class="ann-item" onclick="window.location='{{ route('client.notifications') }}'">
                        <div class="ann-item-title">{{ $an->title ?? 'Announcement' }}</div>
                        <div class="ann-item-body">{{ \Illuminate\Support\Str::limit($an->body, 100) }}</div>
                        <div class="ann-item-date">{{ $an->sent_at ? $an->sent_at->format('M j, Y') : '' }}</div>
                    </div>
                @endforeach
            @else
                <div class="empty-state" style="padding:28px 16px;">
                    <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/></svg>
                    <p>No announcements at this time.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Appointments -->
<div class="recent-card">
    <div class="recent-header">
        <div class="recent-icon">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <span class="ann-title">Recent Appointments</span>
    </div>
    <div class="recent-list">
        @forelse($appointments as $a)
            <div class="recent-row">
                <span class="recent-num">#{{ sprintf('%03d', $a->id) }}</span>
                <span class="recent-type">{{ $a->type }}</span>
                @php $s = $a->status; @endphp
                <span class="status-badge status-{{ $s }}">{{ ucfirst($s) }}</span>
                @if($a->scheduled_at)
                    <span class="recent-date">{{ $a->scheduled_at->format('M j, g:i A') }}</span>
                @endif
            </div>
        @empty
            <div class="empty-state" style="padding:24px;">
                <p>No appointment history yet.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
