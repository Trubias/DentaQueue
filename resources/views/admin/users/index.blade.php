@extends('layouts.admin')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:28px; }
    .page-title  { font-size:22px; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:10px; }
    .page-title svg { color:#3b82f6; }

    .alert-success { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:12px; margin-bottom:20px; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.25); color:#065f46; font-size:13px; font-weight:600; }
    .alert-error   { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:12px; margin-bottom:20px; background:rgba(239,68,68,0.1);  border:1px solid rgba(239,68,68,0.25);  color:#b91c1c; font-size:13px; font-weight:600; }

    /* Search bar */
    .search-bar { display:flex; gap:8px; margin-bottom:20px; }
    .search-input { flex:1; padding:10px 16px; border-radius:10px; border:1.5px solid #e2e8f0; font-size:13px; color:#1e293b; outline:none; font-family:inherit; max-width:380px; transition:border-color .2s; }
    .search-input:focus { border-color:#3b82f6; }
    .btn-search { display:inline-flex; align-items:center; gap:7px; padding:10px 18px; border-radius:10px; background:linear-gradient(135deg,#1e40af,#3b82f6); color:white; font-size:13px; font-weight:600; border:none; cursor:pointer; box-shadow:0 4px 10px rgba(59,130,246,0.3); transition:transform .15s; }
    .btn-search:hover { transform:translateY(-1px); }

    /* Table */
    .table-card { background:white; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,0.06); border:1px solid rgba(226,232,240,0.8); overflow:hidden; }
    .table-card table { width:100%; border-collapse:collapse; }
    .table-card thead { background:linear-gradient(135deg,#1e3a8a,#1e40af); }
    .table-card thead th { padding:13px 18px; text-align:left; font-size:11px; font-weight:700; letter-spacing:.5px; color:rgba(255,255,255,0.85); text-transform:uppercase; white-space:nowrap; }
    .table-card tbody tr { border-bottom:1px solid #f1f5f9; transition:background .15s; }
    .table-card tbody tr:last-child { border-bottom:none; }
    .table-card tbody tr:hover { background:#f8fafc; }
    .table-card td { padding:12px 18px; font-size:13px; color:#334155; }

    /* Avatar */
    .user-avatar { width:36px; height:36px; border-radius:50%; object-fit:cover; }
    .user-initials { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#1e40af,#3b82f6); color:white; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; flex-shrink:0; }
    .user-cell { display:flex; align-items:center; gap:10px; }

    /* Role badge */
    .role-badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; }
    .role-admin  { background:rgba(139,92,246,0.12); color:#6d28d9; }
    .role-client { background:rgba(59,130,246,0.12); color:#1d4ed8; }

    /* ID badge */
    .uid-badge { background:#f1f5f9; border-radius:6px; padding:2px 8px; font-size:11px; font-weight:700; font-family:monospace; color:#475569; }

    .btn-delete {
        display:inline-flex; align-items:center; gap:6px; padding:6px 12px;
        border-radius:8px; background:rgba(239,68,68,0.1); color:#dc2626;
        border:none; font-size:12px; font-weight:600; cursor:pointer;
        transition:background .15s, transform .15s;
    }
    .btn-delete:hover { background:rgba(239,68,68,0.18); transform:translateY(-1px); }

    .pagination-wrap { padding:16px 18px; border-top:1px solid #f1f5f9; }
</style>

<div class="max-w-7xl mx-auto">

    <div class="page-header">
        <div class="page-title">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Users Management
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-error">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <form class="search-bar" method="GET">
        <input type="text" name="q" placeholder="Search by name, email, or UID…" class="search-input" value="{{ request('q') }}" />
        <button type="submit" class="btn-search">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Search
        </button>
    </form>

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
                @foreach($users as $u)
                    <tr>
                        <td><span class="uid-badge">{{ $u->display_id }}</span></td>
                        <td>
                            <div class="user-cell">
                                @if($u->avatar)
                                    <img src="{{ asset('storage/'.$u->avatar) }}" class="user-avatar" alt="avatar" />
                                @else
                                    <div class="user-initials">{{ strtoupper(substr($u->name,0,1)) }}</div>
                                @endif
                                <span style="font-weight:600;">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td style="color:#64748b;">{{ $u->email }}</td>
                        <td>
                            <span class="role-badge role-{{ $u->role }}">{{ ucfirst($u->role) }}</span>
                        </td>
                        <td>
                            @if($u->role !== 'admin')
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Delete this user?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                        Delete
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
        <div class="pagination-wrap">{{ $users->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
