@extends('layouts.admin')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:28px; }
    .page-title { font-size:22px; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:10px; }
    .page-title svg { color:#3b82f6; }

    /* Summary cards */
    .summary-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:14px; margin-bottom:24px; }
    .sum-card { background:white; border-radius:14px; padding:18px 20px; box-shadow:0 2px 10px rgba(0,0,0,0.05); border:1px solid #f1f5f9; display:flex; align-items:center; gap:14px; }
    .sum-icon { width:42px; height:42px; border-radius:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .sum-icon.blue   { background:rgba(59,130,246,0.12); color:#2563eb; }
    .sum-icon.rose   { background:rgba(239,68,68,0.12);  color:#dc2626; }
    .sum-icon.purple { background:rgba(139,92,246,0.12); color:#7c3aed; }
    .sum-value { font-size:24px; font-weight:800; color:#0f172a; line-height:1; }
    .sum-label { font-size:11px; color:#64748b; font-weight:500; margin-top:2px; }

    /* Controls card */
    .controls-card { background:white; border-radius:14px; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.05); border:1px solid #f1f5f9; margin-bottom:20px; display:flex; flex-wrap:wrap; align-items:flex-end; justify-content:space-between; gap:14px; }
    .form-group { display:flex; flex-direction:column; gap:5px; }
    .form-label { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.5px; }
    .form-row { display:flex; align-items:center; gap:8px; }
    .form-control { padding:9px 14px; border-radius:10px; border:1.5px solid #e2e8f0; font-size:13px; color:#1e293b; outline:none; font-family:inherit; transition:border-color .2s; background:white; }
    .form-control:focus { border-color:#3b82f6; }

    .btn { display:inline-flex; align-items:center; gap:7px; padding:9px 16px; border-radius:10px; font-size:12px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:transform .15s, box-shadow .15s; white-space:nowrap; }
    .btn:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(0,0,0,0.12); }
    .btn-primary { background:linear-gradient(135deg,#1e40af,#3b82f6); color:white; box-shadow:0 4px 10px rgba(59,130,246,0.3); }
    .btn-dark    { background:linear-gradient(135deg,#1e293b,#334155); color:white; box-shadow:0 4px 10px rgba(30,41,59,0.3); }

    /* Chart card */
    .chart-card { background:white; border-radius:16px; padding:24px; box-shadow:0 2px 12px rgba(0,0,0,0.06); border:1px solid rgba(226,232,240,0.8); margin-bottom:20px; }
    .chart-title { font-size:14px; font-weight:700; color:#1e293b; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
    .chart-title svg { color:#3b82f6; }

    /* Data tables */
    .tables-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
    @media(max-width:900px){ .tables-grid { grid-template-columns:1fr 1fr; } }
    @media(max-width:600px){ .tables-grid { grid-template-columns:1fr; } }

    .data-table-card { background:white; border-radius:14px; box-shadow:0 2px 10px rgba(0,0,0,0.05); border:1px solid #f1f5f9; overflow:hidden; }
    .data-table-header { background:linear-gradient(135deg,#1e3a8a,#1e40af); padding:11px 16px; font-size:12px; font-weight:700; color:white; letter-spacing:.5px; text-transform:uppercase; }
    .data-table-card table { width:100%; border-collapse:collapse; }
    .data-table-card td { padding:9px 14px; font-size:12px; color:#334155; border-bottom:1px solid #f8fafc; }
    .data-table-card tr:last-child td { border-bottom:none; }
    .data-table-card td:last-child { text-align:right; font-weight:700; color:#1e40af; }

    .export-row { display:flex; flex-wrap:wrap; gap:10px; }
</style>

<div class="max-w-7xl mx-auto">

    <div class="page-header">
        <div class="page-title">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M3 3v18h18" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><path d="M9 17V9M15 17v-6M21 17v-2" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
            Analytics & Reports
        </div>
        <div class="export-row">
            <a href="{{ route('admin.reports.export', ['type'=>'appointments','year'=>$year]) }}" class="btn btn-dark">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Appointments CSV
            </a>
            <a href="{{ route('admin.reports.export', ['type'=>'announcements','year'=>$year]) }}" class="btn btn-dark">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Announcements CSV
            </a>
        </div>
    </div>

    {{-- Summary counts --}}
    @php
        $totalAppts = $monthly->sum('total');
        $totalNoShows = $noShows->sum('total');
        $totalReminders = $reminders->sum('sent');
    @endphp
    <div class="summary-grid">
        <div class="sum-card">
            <div class="sum-icon blue">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div><div class="sum-value">{{ $totalAppts }}</div><div class="sum-label">Total Appointments</div></div>
        </div>
        <div class="sum-card">
            <div class="sum-icon rose">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 8v4l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div><div class="sum-value">{{ $totalNoShows }}</div><div class="sum-label">No-Shows</div></div>
        </div>
        <div class="sum-card">
            <div class="sum-icon purple">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="2"/><path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div><div class="sum-value">{{ $totalReminders }}</div><div class="sum-label">Reminders Sent</div></div>
        </div>
    </div>

    {{-- Year Filter --}}
    <div class="controls-card">
        <form method="GET" class="form-row">
            <div class="form-group">
                <label class="form-label">Filter by Year</label>
                <div class="form-row">
                    <input type="number" name="year" value="{{ $year }}" class="form-control" style="width:110px;" min="2020" max="2099" />
                    <button type="submit" class="btn btn-primary">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        View {{ $year }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Chart --}}
    <div class="chart-card">
        <div class="chart-title">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M3 3v18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M9 17V9M15 17v-6M21 17v-2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Monthly Appointment Overview — {{ $year }}
        </div>
        <canvas id="monthlyChart" height="100"></canvas>
    </div>

    {{-- Data tables --}}
    <div class="tables-grid">
        <div class="data-table-card">
            <div class="data-table-header">Monthly Totals</div>
            <table>
                @for($m=1; $m<=12; $m++)
                    <tr>
                        <td>{{ DateTime::createFromFormat('!m', $m)->format('M') }}</td>
                        <td>{{ $monthly->has($m) ? $monthly[$m]->total : 0 }}</td>
                    </tr>
                @endfor
            </table>
        </div>
        <div class="data-table-card">
            <div class="data-table-header">By Type</div>
            <table>
                @foreach($byType as $t)
                    <tr>
                        <td>{{ $t->type }}</td>
                        <td>{{ $t->total }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
        <div class="data-table-card">
            <div class="data-table-header">Reminders Sent</div>
            <table>
                @for($m=1; $m<=12; $m++)
                    <tr>
                        <td>{{ DateTime::createFromFormat('!m', $m)->format('M') }}</td>
                        <td>{{ $reminders->has($m) ? $reminders[$m]->sent : 0 }}</td>
                    </tr>
                @endfor
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const months = [@for($m=1;$m<=12;$m++)'{{ DateTime::createFromFormat('!m', $m)->format('F') }}',@endfor];
const monthlyData = [@for($m=1;$m<=12;$m++){{ $monthly->has($m) ? $monthly[$m]->total : 0 }},@endfor];
const noShowData  = [@for($m=1;$m<=12;$m++){{ $noShows->has($m) ? $noShows[$m]->total : 0 }},@endfor];
const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: months,
        datasets: [
            { label: 'Appointments', data: monthlyData, backgroundColor: 'rgba(59,130,246,0.7)', borderRadius: 6, borderSkipped: false },
            { label: 'No-shows',     data: noShowData,  backgroundColor: 'rgba(239,68,68,0.6)',  borderRadius: 6, borderSkipped: false }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            x: { grid: { display: false } },
            y: { grid: { color: '#f1f5f9' }, ticks: { stepSize: 1 } }
        }
    }
});
</script>
@endpush
@endsection
