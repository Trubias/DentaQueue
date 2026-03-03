@extends('layouts.admin')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:28px; }
    .page-title { font-size:22px; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:10px; }
    .page-title svg { color:#3b82f6; }

    /* Alert */
    .alert-success {
        display:flex; align-items:center; gap:10px;
        padding:12px 16px; border-radius:12px; margin-bottom:20px;
        background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.25);
        color:#065f46; font-size:13px; font-weight:600;
    }

    /* Table card */
    .table-card {
        background:white; border-radius:16px;
        box-shadow:0 2px 12px rgba(0,0,0,0.06);
        border:1px solid rgba(226,232,240,0.8);
        overflow:hidden;
    }
    .table-card table { width:100%; border-collapse:collapse; }
    .table-card thead { background:linear-gradient(135deg,#1e3a8a,#1e40af); }
    .table-card thead th {
        padding:13px 18px; text-align:left;
        font-size:12px; font-weight:700; letter-spacing:.5px;
        color:rgba(255,255,255,0.9); text-transform:uppercase; white-space:nowrap;
    }
    .table-card tbody tr {
        border-bottom:1px solid #f1f5f9;
        transition:background 0.15s;
    }
    .table-card tbody tr:last-child { border-bottom:none; }
    .table-card tbody tr:hover { background:#f8fafc; }
    .table-card td {
        padding:13px 18px; font-size:13px; color:#334155;
    }

    /* Queue number badge */
    .queue-id {
        display:inline-flex; align-items:center; justify-content:center;
        width:40px; height:24px; border-radius:6px;
        background:linear-gradient(135deg,#1e40af,#3b82f6);
        color:white; font-size:11px; font-weight:700;
    }
    /* Status badge */
    .status-badge {
        display:inline-block; padding:3px 10px; border-radius:999px;
        font-size:11px; font-weight:700;
    }
    .status-pending  { background:rgba(245,158,11,0.12); color:#b45309; }
    .status-assigned { background:rgba(59,130,246,0.12); color:#1d4ed8; }
    .status-done     { background:rgba(16,185,129,0.12); color:#065f46; }
    .status-cancelled{ background:rgba(239,68,68,0.12);  color:#b91c1c; }

    /* View link */
    .btn-view {
        display:inline-flex; align-items:center; gap:6px;
        padding:6px 14px; border-radius:8px;
        background:rgba(59,130,246,0.1); color:#1d4ed8;
        font-size:12px; font-weight:600; text-decoration:none;
        transition:background 0.15s, transform 0.15s;
    }
    .btn-view:hover { background:rgba(59,130,246,0.2); transform:translateY(-1px); }

    .empty-state {
        display:flex; flex-direction:column; align-items:center; gap:8px;
        padding:48px 0; color:#94a3b8; font-size:13px;
    }
</style>

<div class="max-w-7xl mx-auto">

    <div class="page-header">
        <div class="page-title">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M3 6h18M7 12h10M10 18h4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
            Client Queue
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="table-card">
        @if($appointments->isEmpty())
            <div class="empty-state">
                <svg width="42" height="42" fill="none" viewBox="0 0 24 24"><path d="M3 6h18M7 12h10M10 18h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                No clients in the queue yet.
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fullname</th>
                        <th>Age</th>
                        <th>Sex</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $a)
                        <tr>
                            <td><span class="queue-id">{{ sprintf('%03d', $a->id) }}</span></td>
                            <td style="font-weight:600;">{{ $a->fullname }}</td>
                            <td>{{ $a->age }}</td>
                            <td>{{ $a->sex }}</td>
                            <td>{{ $a->type }}</td>
                            <td>
                                @php $s = strtolower($a->status); @endphp
                                <span class="status-badge status-{{ $s }}">{{ ucfirst($a->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.queue.show', $a) }}" class="btn-view">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
