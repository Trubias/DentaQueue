@extends('layouts.admin')

@section('content')
<style>
    .inv-page { max-width: 1100px; margin: 0 auto; }

    /* Header */
    .inv-header {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 14px; margin-bottom: 28px;
    }
    .inv-title-group { display: flex; align-items: center; gap: 12px; }
    .inv-icon-box {
        width: 46px; height: 46px; border-radius: 13px;
        background: linear-gradient(135deg, #1e40af, #3b82f6);
        display: flex; align-items: center; justify-content: center;
        color: white; flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(59,130,246,0.35);
    }
    .inv-title { font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; }
    .inv-subtitle { font-size: 13px; color: #64748b; margin-top: 2px; }

    .inv-toolbar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

    /* Back link */
    .back-link {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 600; color: #3b82f6;
        text-decoration: none; padding: 8px 14px;
        border-radius: 9px; border: 1.5px solid #bfdbfe;
        background: #eff6ff;
        transition: all 0.15s;
    }
    .back-link:hover { background: #dbeafe; color: #1d4ed8; border-color: #93c5fd; }

    /* Year select */
    .year-control {
        display: flex; align-items: center; gap: 8px;
        background: white; border: 1.5px solid #e2e8f0;
        border-radius: 10px; padding: 6px 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
    .year-control label { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .4px; white-space: nowrap; }
    .year-select {
        border: none; outline: none; font-size: 13px; font-weight: 600;
        color: #1e293b; background: transparent; cursor: pointer;
        padding: 0 18px 0 0;
        appearance: none; -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0 center;
    }

    /* Export button */
    .btn-export {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 18px; border-radius: 10px;
        font-size: 13px; font-weight: 600; cursor: pointer;
        text-decoration: none; white-space: nowrap;
        background: linear-gradient(135deg, #059669, #10b981);
        color: white;
        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        border: none; transition: transform 0.15s, box-shadow 0.15s;
    }
    .btn-export:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(16,185,129,0.4); }

    /* Year Section */
    .year-section { margin-bottom: 28px; }
    .year-badge {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #1e3a8a, #1e40af);
        color: white; font-size: 14px; font-weight: 700;
        padding: 6px 16px; border-radius: 8px;
        margin-bottom: 14px;
        box-shadow: 0 3px 10px rgba(30,64,175,0.3);
    }

    /* Table card */
    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        border: 1px solid rgba(226,232,240,0.8);
        overflow: hidden;
    }
    .table-card table { width: 100%; border-collapse: collapse; }
    .table-card thead { background: linear-gradient(135deg, #1e3a8a, #1e40af); }
    .table-card thead th {
        padding: 12px 18px; text-align: left;
        font-size: 11px; font-weight: 700; letter-spacing: .6px;
        color: rgba(255,255,255,0.88); text-transform: uppercase;
        white-space: nowrap;
    }
    .table-card tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.13s; }
    .table-card tbody tr:last-child { border-bottom: none; }
    .table-card tbody tr:hover { background: #f8fafc; }
    .table-card td { padding: 12px 18px; font-size: 13px; color: #334155; }

    .title-cell { font-weight: 600; color: #1e293b; max-width: 280px; }
    .title-cell .title-text { display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 260px; }

    .recipient-chip {
        display: inline-flex; align-items: center; gap: 5px;
        background: #f1f5f9; border-radius: 6px;
        padding: 3px 9px; font-size: 11.5px; color: #475569;
    }

    .appt-badge {
        display: inline-flex; align-items: center; gap: 4px;
        background: #dbeafe; color: #1d4ed8;
        border-radius: 6px; padding: 3px 9px;
        font-size: 11.5px; font-weight: 600;
    }

    .sent-time {
        font-size: 12px; color: #64748b;
        display: flex; align-items: center; gap: 5px;
    }
    .sent-dot { width: 7px; height: 7px; border-radius: 50%; background: #10b981; flex-shrink: 0; }

    .empty-state {
        display: flex; flex-direction: column; align-items: center;
        gap: 10px; padding: 56px 24px; color: #94a3b8;
        font-size: 14px; font-weight: 500;
    }
    .empty-state svg { color: #cbd5e1; }

    /* Stats bar */
    .stats-bar {
        display: flex; align-items: center; gap: 6px;
        padding: 10px 18px; background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        font-size: 12px; color: #64748b; font-weight: 500;
    }
    .stats-count {
        font-weight: 700; color: #1e293b;
    }

    @media (max-width: 640px) {
        .inv-header { flex-direction: column; align-items: flex-start; }
        .table-card td, .table-card thead th { padding: 10px 12px; font-size: 12px; }
    }
</style>

<div class="inv-page">

    <!-- Header -->
    <div class="inv-header">
        <div class="inv-title-group">
            <div class="inv-icon-box">
                <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
            </div>
            <div>
                <div class="inv-title">Announcements Inventory</div>
                <div class="inv-subtitle">Record of all sent email announcements</div>
            </div>
        </div>
        <div class="inv-toolbar">
            <a href="{{ route('admin.announcements.index') }}" class="back-link">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Back to Announcements
            </a>
            <form method="GET" action="{{ route('admin.announcements.inventory') }}" style="display:contents;">
                <div class="year-control">
                    <label for="yearSelect">Year</label>
                    @php
                        $years = collect($groups->keys())->sortDesc();
                        $selected = request()->query('year', null);
                    @endphp
                    <select id="yearSelect" name="year" onchange="this.form.submit()" class="year-select">
                        <option value="">All</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" @if($selected == $y) selected @endif>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            <a href="{{ route('admin.announcements.inventory.exportAppointments', ['year' => request()->query('year', now()->year)]) }}" class="btn-export">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Export Appointments
            </a>
        </div>
    </div>

    @if($groups->isEmpty())
        <div class="table-card">
            <div class="empty-state">
                <svg width="48" height="48" fill="none" viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" stroke="currentColor" stroke-width="1.5"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" stroke="currentColor" stroke-width="1.5"/></svg>
                No sent announcements yet.
            </div>
        </div>
    @else
        @foreach($groups as $year => $items)
            @if(request()->query('year') && request()->query('year') != $year) @continue @endif
            <div class="year-section">
                <div class="year-badge">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ $year }}
                </div>
                <div class="table-card">
                    <div class="stats-bar">
                        <span class="stats-count">{{ count($items) }}</span> email{{ count($items) !== 1 ? 's' : '' }} sent in {{ $year }}
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Recipient</th>
                                <th>Appointment</th>
                                <th>Sent At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $it)
                                <tr>
                                    <td class="title-cell">
                                        <span class="title-text" title="{{ $it->title }}">{{ $it->title }}</span>
                                    </td>
                                    <td>
                                        <span class="recipient-chip">
                                            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                            @if($it->user)
                                                {{ $it->user->name }} ({{ $it->user->email }})
                                            @elseif($it->recipient_email)
                                                {{ $it->recipient_email }}
                                            @else
                                                All Users
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if($it->appointment)
                                            <span class="appt-badge">
                                                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                                                #{{ sprintf('%03d', $it->appointment->id) }} — {{ $it->appointment->fullname }}
                                            </span>
                                        @else
                                            <span style="color:#94a3b8; font-size:12px;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($it->sent_at)
                                            <div class="sent-time">
                                                <span class="sent-dot"></span>
                                                {{ $it->sent_at->toDateTimeString() }}
                                            </div>
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
        @endforeach
    @endif

</div>
@endsection
