@extends('layouts.client')

@section('content')
<style>
    .appt-header { margin-bottom: 24px; }
    .appt-title { font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.8px; }
    .appt-subtitle { font-size: 14px; color: #64748b; margin-top: 4px; }

    .appt-card {
        background: white;
        border-radius: 18px;
        border: 1px solid #e8edf5;
        box-shadow: 0 4px 20px rgba(15,23,42,0.06);
        overflow: hidden;
    }
    .appt-card-header {
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        padding: 20px 24px;
        display: flex; align-items: center; gap: 12px;
    }
    .appt-card-icon {
        width: 44px; height: 44px; border-radius: 12px;
        background: rgba(255,255,255,0.18);
        border: 1px solid rgba(255,255,255,0.25);
        display: flex; align-items: center; justify-content: center;
        color: white;
    }
    .appt-card-title { color: white; font-size: 17px; font-weight: 700; }
    .appt-card-count { color: rgba(255,255,255,0.65); font-size: 13px; margin-top: 2px; }

    /* Table */
    .appt-table-wrap { padding: 16px 20px 20px; }
    .appt-table {
        width: 100%; border-collapse: collapse;
    }
    .appt-table thead tr th {
        text-align: left; padding: 10px 14px;
        font-size: 11.5px; font-weight: 700;
        color: #94a3b8; text-transform: uppercase; letter-spacing: 0.7px;
        border-bottom: 2px solid #f1f5f9;
    }
    .appt-table tbody tr {
        border-bottom: 1px solid #f8fafc;
        transition: background 0.15s;
    }
    .appt-table tbody tr:hover { background: #f8fafc; }
    .appt-table tbody tr:last-child { border-bottom: none; }
    .appt-table td {
        padding: 13px 14px;
        font-size: 13.5px; color: #1e293b;
        vertical-align: middle;
    }
    .appt-id {
        font-weight: 700; color: #64748b;
        font-size: 13px;
        font-family: 'Courier New', monospace;
    }
    .appt-type-chip {
        display: inline-flex; align-items: center; gap: 6px;
        font-weight: 600;
    }
    .type-dot {
        width: 8px; height: 8px; border-radius: 50%;
    }
    .appt-date-cell { color: #64748b; font-size: 13px; }
    .empty-row td { text-align: center; padding: 48px 20px; }

    /* Responsive horizontal scroll on small screens */
    @media (max-width: 640px) {
        .appt-table-wrap { padding: 12px 0; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .appt-table { min-width: 540px; }
        .appt-table-wrap { padding: 12px 16px; }
    }
</style>

<div class="appt-header">
    <div class="appt-title">My Appointments</div>
    <div class="appt-subtitle">Track all your past and upcoming dental visits</div>
</div>

<div class="appt-card">
    <div class="appt-card-header">
        <div class="appt-card-icon">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
        </div>
        <div>
            <div class="appt-card-title">Appointment History</div>
            <div class="appt-card-count">{{ $appointments->count() }} record(s) found</div>
        </div>
    </div>

    <div class="appt-table-wrap">
        <table class="appt-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Scheduled</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $a)
                    <tr>
                        <td>
                            <span class="appt-id">#{{ sprintf('%03d', $a->id) }}</span>
                        </td>
                        <td>
                            <div class="appt-type-chip">
                                @php
                                    $typeColors = [
                                        'Check-up'  => '#2563eb',
                                        'Cleaning'  => '#16a34a',
                                        'Extraction'=> '#dc2626',
                                        'Braces'    => '#7c3aed',
                                    ];
                                    $dot = $typeColors[$a->type] ?? '#64748b';
                                @endphp
                                <span class="type-dot" style="background:{{ $dot }};"></span>
                                {{ $a->type }}
                            </div>
                        </td>
                        <td>
                            @php $s = strtolower($a->status); @endphp
                            <span class="status-badge status-{{ $s }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td class="appt-date-cell">
                            {{ $a->scheduled_at ? $a->scheduled_at->format('M j, Y · g:i A') : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-row">
                            <div style="display:flex;flex-direction:column;align-items:center;gap:10px;color:#94a3b8;">
                                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                                <div>
                                    <div style="font-size:14px;font-weight:600;color:#64748b;margin-bottom:4px;">No appointments yet</div>
                                    <div style="font-size:13px;">Ready to book your first visit?</div>
                                    <div style="margin-top:12px;">
                                        <a href="{{ route('client.book') }}" class="btn btn-primary btn-sm">Book an Appointment</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
