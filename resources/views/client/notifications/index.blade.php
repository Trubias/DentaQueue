@extends('layouts.client')

@section('content')
<style>
    .notif-header { margin-bottom: 24px; }
    .notif-title { font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.8px; }
    .notif-subtitle { font-size: 14px; color: #64748b; margin-top: 4px; }

    .notif-card {
        background: white;
        border-radius: 18px;
        border: 1px solid #e8edf5;
        box-shadow: 0 4px 20px rgba(15,23,42,0.06);
        overflow: hidden;
    }
    .notif-card-header {
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        padding: 20px 24px;
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px;
    }
    .notif-card-header-left { display: flex; align-items: center; gap: 12px; }
    .notif-card-icon {
        width: 44px; height: 44px; border-radius: 12px;
        background: rgba(255,255,255,0.18);
        border: 1px solid rgba(255,255,255,0.25);
        display: flex; align-items: center; justify-content: center;
        color: white;
    }
    .notif-card-title { color: white; font-size: 17px; font-weight: 700; }
    .notif-card-count { color: rgba(255,255,255,0.65); font-size: 13px; margin-top: 2px; }
    .notif-unread-pill {
        background: rgba(239,68,68,0.9); color: white;
        font-size: 12px; font-weight: 700;
        padding: 4px 11px; border-radius: 999px;
        box-shadow: 0 2px 8px rgba(239,68,68,0.4);
    }

    /* List */
    .notif-list { padding: 8px 0; }

    .notif-item {
        display: flex; gap: 14px;
        padding: 16px 22px;
        text-decoration: none;
        border-bottom: 1px solid #f8fafc;
        transition: background 0.18s;
        position: relative;
        cursor: pointer;
    }
    .notif-item:last-child { border-bottom: none; }
    .notif-item:hover { background: #f8fafc; }
    .notif-item.unread { background: #f0f7ff; }
    .notif-item.unread:hover { background: #e8f0fe; }

    .notif-item-dot-wrap { padding-top: 4px; flex-shrink: 0; }
    .notif-item-dot {
        width: 9px; height: 9px; border-radius: 50%;
        background: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.18);
    }
    .notif-item-dot.read { background: #e2e8f0; box-shadow: none; }

    .notif-item-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 18px;
    }
    .notif-item-icon.unread-icon { background: #dbeafe; }
    .notif-item-icon.read-icon { background: #f1f5f9; }

    .notif-item-body { flex: 1; min-width: 0; }
    .notif-item-title {
        font-size: 14px; font-weight: 700; color: #1e293b;
        margin-bottom: 4px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .notif-item.read .notif-item-title { font-weight: 500; color: #475569; }
    .notif-item-preview { font-size: 13px; color: #64748b; line-height: 1.5; }
    .notif-item-meta {
        display: flex; align-items: center; justify-content: space-between;
        margin-top: 6px; gap: 8px;
    }
    .notif-item-date { font-size: 11.5px; color: #94a3b8; }
    .notif-item-badge {
        display: inline-flex; align-items: center;
        padding: 2px 9px; border-radius: 999px;
        font-size: 11px; font-weight: 700; flex-shrink: 0;
    }
    .notif-item-badge.new { background: #fee2e2; color: #991b1b; }
    .notif-item-badge.read { background: #f0fdf4; color: #166534; }

    .notif-empty {
        padding: 64px 20px;
        text-align: center; color: #94a3b8;
    }
    .notif-empty svg { margin: 0 auto 14px; opacity: 0.35; }
    .notif-empty h3 { font-size: 16px; font-weight: 600; color: #64748b; margin-bottom: 6px; }
    .notif-empty p { font-size: 13.5px; }
</style>

<div class="notif-header">
    <div class="notif-title">Notifications</div>
    <div class="notif-subtitle">Stay updated on your appointment status and announcements</div>
</div>

<div class="notif-card">
    <div class="notif-card-header">
        <div class="notif-card-header-left">
            <div class="notif-card-icon">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </div>
            <div>
                <div class="notif-card-title">All Notifications</div>
                <div class="notif-card-count">{{ $announcements->count() }} total message(s)</div>
            </div>
        </div>
        @php $unread = $announcements->where('read', false)->count(); @endphp
        @if($unread > 0)
            <div class="notif-unread-pill">{{ $unread }} New</div>
        @endif
    </div>

    @if($announcements->isEmpty())
        <div class="notif-empty">
            <svg width="52" height="52" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <h3>No notifications yet</h3>
            <p>When the clinic sends announcements, they'll appear here.</p>
        </div>
    @else
        <div class="notif-list">
            @foreach($announcements as $an)
                <a href="{{ route('client.notifications.show', $an->id) }}"
                   class="notif-item {{ $an->read ? 'read' : 'unread' }}">
                    <div class="notif-item-dot-wrap">
                        <div class="notif-item-dot {{ $an->read ? 'read' : '' }}"></div>
                    </div>
                    <div class="notif-item-icon {{ $an->read ? 'read-icon' : 'unread-icon' }}">
                        {{ $an->read ? '📬' : '📩' }}
                    </div>
                    <div class="notif-item-body">
                        <div class="notif-item-title">{{ $an->title ?? 'Announcement' }}</div>
                        <div class="notif-item-preview">{{ \Illuminate\Support\Str::limit($an->body, 120) }}</div>
                        <div class="notif-item-meta">
                            <span class="notif-item-date">
                                {{ $an->sent_at ? $an->sent_at->format('M j, Y · g:i A') : '' }}
                            </span>
                            <span class="notif-item-badge {{ $an->read ? 'read' : 'new' }}">
                                {{ $an->read ? '✓ Read' : '● New' }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
