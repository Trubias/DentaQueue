@extends('layouts.admin')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:28px; }
    .page-title  { font-size:22px; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:10px; }
    .page-title svg { color:#3b82f6; }
    .search-bar { display:flex; gap:8px; margin-bottom:20px; }
    .search-input { flex:1; padding:10px 16px; border-radius:10px; border:1.5px solid #e2e8f0; font-size:13px; color:#1e293b; outline:none; font-family:inherit; max-width:380px; transition:border-color .2s; }
    .search-input:focus { border-color:#3b82f6; }
    .btn-search { display:inline-flex; align-items:center; gap:7px; padding:10px 18px; border-radius:10px; background:linear-gradient(135deg,#1e40af,#3b82f6); color:white; font-size:13px; font-weight:600; border:none; cursor:pointer; box-shadow:0 4px 10px rgba(59,130,246,0.3); transition:transform .15s; }
    .btn-search:hover { transform:translateY(-1px); }

    .table-card { background:white; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,0.06); border:1px solid rgba(226,232,240,0.8); overflow:hidden; }
    .table-card table { width:100%; border-collapse:collapse; }
    .table-card thead { background:linear-gradient(135deg,#1e3a8a,#1e40af); }
    .table-card thead th { padding:13px 18px; text-align:left; font-size:11px; font-weight:700; letter-spacing:.5px; color:rgba(255,255,255,0.85); text-transform:uppercase; white-space:nowrap; }
    .table-card tbody tr { border-bottom:1px solid #f1f5f9; transition:background .15s; }
    .table-card tbody tr:last-child { border-bottom:none; }
    .table-card tbody tr:hover { background:#f8fafc; }
    .table-card td { padding:12px 18px; font-size:13px; color:#334155; }

    .user-cell { display:flex; align-items:center; gap:10px; }
    .user-initials { width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,#1e40af,#3b82f6); color:white; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; flex-shrink:0; }
    .user-avatar { width:34px; height:34px; border-radius:50%; object-fit:cover; }
    .uid-badge { background:#f1f5f9; border-radius:6px; padding:2px 8px; font-size:11px; font-weight:700; font-family:monospace; color:#475569; }
    .role-badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; background:rgba(59,130,246,0.12); color:#1d4ed8; }
    .btn-view { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:8px; background:linear-gradient(135deg,#1e40af,#3b82f6); color:white; font-size:12px; font-weight:600; text-decoration:none; box-shadow:0 3px 8px rgba(59,130,246,0.3); transition:transform .15s; }
    .btn-view:hover { transform:translateY(-1px); }
    .pagination-wrap { padding:16px 18px; border-top:1px solid #f1f5f9; }
</style>

<div class="max-w-7xl mx-auto">
    <div class="page-header">
        <div class="page-title">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            User Appointments
        </div>
    </div>

    <form class="search-bar" method="GET">
        <input type="text" name="q" class="search-input" placeholder="Search by name, email, or UID…" value="{{ request('q') }}" />
        <button type="submit" class="btn-search">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Search
        </button>
    </form>

    @php
        $search = request('q');
        $clients = \App\Models\User::where('role', '!=', 'admin')
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('uid', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10);
    @endphp

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>UID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $c)
                    <tr>
                        <td><span class="uid-badge">{{ $c->display_id }}</span></td>
                        <td>
                            <div class="user-cell">
                                @if($c->avatar)
                                    <img src="{{ asset('storage/'.$c->avatar) }}" class="user-avatar" alt="avatar" />
                                @else
                                    <div class="user-initials">{{ strtoupper(substr($c->name, 0, 1)) }}</div>
                                @endif
                                <span style="font-weight:600;">{{ $c->name }}</span>
                            </div>
                        </td>
                        <td style="color:#64748b;">{{ $c->email }}</td>
                        <td><span class="role-badge">{{ ucfirst($c->role) }}</span></td>
                        <td>
                            <a href="{{ route('admin.users.appointments', $c) }}" class="btn-view">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                Appointments
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination-wrap">{{ $clients->withQueryString()->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: false,
            events: '{{ route('admin.schedule.events') }}',
            selectable: true,
            select: function(info) {
                var title = prompt('Create appointment title (client name)');
                if (!title) return;
                fetch('{{ route('admin.schedule.events.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                    body: JSON.stringify({ title, start: info.startStr })
                }).then(r => r.ok ? r.json() : r.json().then(j => { throw j; }))
                  .then(data => calendar.addEvent({ id: data.id, title, start: info.startStr }))
                  .catch(err => alert(err.error || 'Could not create'));
            },
            eventClick: function(info) { if (info.event.id) window.location = '/admin/queue/' + info.event.id; },
            editable: true,
            eventDrop: function(info) {
                fetch('/admin/schedule/events/' + info.event.id + '/move', {
                    method: 'POST',
                    headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                    body: JSON.stringify({ start: info.event.start.toISOString() })
                }).then(r => { if (!r.ok) return r.json().then(j => { throw j; }); })
                  .catch(err => { alert(err.error || 'Could not move'); info.revert(); });
            }
        });
        calendar.render();
    });
</script>
@endpush
