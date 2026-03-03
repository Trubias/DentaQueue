@extends('layouts.admin')

@section('content')
<style>
    .back-link { display:inline-flex; align-items:center; gap:6px; color:#64748b; font-size:13px; font-weight:600; text-decoration:none; margin-bottom:20px; transition:color .15s; }
    .back-link:hover { color:#1e40af; }
    .page-title { font-size:22px; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:10px; margin-bottom:24px; }
    .page-title svg { color:#3b82f6; }

    .alert-success { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:12px; margin-bottom:20px; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.25); color:#065f46; font-size:13px; font-weight:600; }
    .alert-error   { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:12px; margin-bottom:20px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25); color:#b91c1c; font-size:13px; font-weight:600; }

    /* User badge */
    .user-banner {
        background:linear-gradient(135deg,#1e3a8a,#1e40af);
        border-radius:16px; padding:20px 24px;
        display:flex; align-items:center; gap:16px;
        margin-bottom:20px;
    }
    .user-banner-avatar {
        width:52px; height:52px; border-radius:50%;
        background:rgba(255,255,255,0.15);
        display:flex; align-items:center; justify-content:center;
        font-size:20px; font-weight:800; color:white; flex-shrink:0; overflow:hidden;
    }
    .user-banner-avatar img { width:100%; height:100%; object-fit:cover; }
    .user-banner-name { font-size:17px; font-weight:700; color:white; }
    .user-banner-sub  { font-size:12px; color:rgba(255,255,255,0.65); margin-top:2px; }

    /* Table */
    .table-card { background:white; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,0.06); border:1px solid rgba(226,232,240,0.8); overflow:hidden; }
    .table-card table { width:100%; border-collapse:collapse; }
    .table-card thead { background:linear-gradient(135deg,#1e3a8a,#1e40af); }
    .table-card thead th { padding:13px 18px; text-align:left; font-size:11px; font-weight:700; letter-spacing:.5px; color:rgba(255,255,255,0.85); text-transform:uppercase; white-space:nowrap; }
    .table-card tbody tr { border-bottom:1px solid #f1f5f9; transition:background .15s; }
    .table-card tbody tr:last-child { border-bottom:none; }
    .table-card tbody tr:hover { background:#f8fafc; }
    .table-card td { padding:12px 18px; font-size:13px; color:#334155; }

    .appt-id { display:inline-flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#1e40af,#3b82f6); color:white; font-size:11px; font-weight:700; padding:3px 10px; border-radius:6px; }
    .status-badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; }
    .status-pending  { background:rgba(245,158,11,0.12);  color:#b45309; }
    .status-assigned { background:rgba(59,130,246,0.12);  color:#1d4ed8; }
    .status-done     { background:rgba(16,185,129,0.12);  color:#065f46; }
    .status-cancelled{ background:rgba(239,68,68,0.12);   color:#b91c1c; }

    .btn-done {
        display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:8px;
        background:linear-gradient(135deg,#059669,#10b981); color:white;
        border:none; font-size:12px; font-weight:600; cursor:pointer;
        box-shadow:0 3px 8px rgba(16,185,129,0.3); transition:transform .15s;
    }
    .btn-done:hover { transform:translateY(-1px); }
</style>

<div class="max-w-6xl mx-auto">
    <a href="{{ route('admin.schedule.index') }}" class="back-link">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Appointments
    </a>

    <div class="user-banner">
        <div class="user-banner-avatar">
            @if($user->avatar)
                <img src="{{ asset('storage/'.$user->avatar) }}" alt="user" />
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
        </div>
        <div>
            <div class="user-banner-name">{{ $user->name }}</div>
            <div class="user-banner-sub">{{ $user->email }} · {{ $appointments->count() }} appointment(s)</div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error"><svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 8v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>{{ session('error') }}</div>
    @endif

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Scheduled</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $a)
                    <tr>
                        <td><span class="appt-id">#{{ sprintf('%03d', $a->id) }}</span></td>
                        <td style="font-weight:600;">{{ $a->type }}</td>
                        <td>
                            @php $s = strtolower($a->status); @endphp
                            <span class="status-badge status-{{ $s }}">{{ ucfirst($a->status) }}</span>
                        </td>
                        <td>{{ $a->scheduled_at ? $a->scheduled_at->format('M j, Y — g:i A') : '—' }}</td>
                        <td>
                            @if($a->scheduled_at && $a->status === 'assigned')
                                <form method="POST" action="{{ route('admin.users.appointments.done', [$user, $a]) }}" onsubmit="return confirm('Mark appointment as done?');">
                                    @csrf
                                    <button type="submit" class="btn-done">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Done
                                    </button>
                                </form>
                            @else
                                <span style="color:#94a3b8; font-size:12px;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
