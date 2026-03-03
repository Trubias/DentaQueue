@extends('layouts.admin')

@section('content')
<style>
    .back-link {
        display:inline-flex; align-items:center; gap:6px;
        color:#64748b; font-size:13px; font-weight:600;
        text-decoration:none; margin-bottom:20px;
        transition:color 0.15s;
    }
    .back-link:hover { color:#1e40af; }

    .detail-card {
        background:white; border-radius:20px;
        box-shadow:0 4px 20px rgba(0,0,0,0.08);
        border:1px solid rgba(226,232,240,0.8);
        overflow:hidden; max-width:720px;
    }
    .detail-card-header {
        background:linear-gradient(135deg,#1e3a8a,#1e40af);
        padding:24px 28px;
        display:flex; align-items:center; justify-content:space-between;
    }
    .appt-num-badge {
        display:inline-flex; align-items:center; justify-content:center;
        background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25);
        border-radius:10px; padding:6px 16px;
        font-size:15px; font-weight:800; color:white; letter-spacing:1px;
    }
    .header-title { font-size:18px; font-weight:700; color:white; }
    .header-sub { font-size:12px; color:rgba(255,255,255,0.65); margin-top:3px; }

    /* Status badge */
    .status-badge {
        display:inline-block; padding:5px 14px; border-radius:999px;
        font-size:12px; font-weight:700;
    }
    .status-pending   { background:rgba(245,158,11,0.15); color:#d97706; }
    .status-assigned  { background:rgba(59,130,246,0.15); color:#1d4ed8; }
    .status-done      { background:rgba(16,185,129,0.15); color:#059669; }
    .status-cancelled { background:rgba(239,68,68,0.15);  color:#dc2626; }

    /* Detail fields */
    .detail-body { padding:28px; }
    .detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    @media(max-width:600px){ .detail-grid { grid-template-columns:1fr; } }
    .detail-field {
        background:#f8fafc; border-radius:12px;
        padding:14px 16px; border:1px solid #f1f5f9;
    }
    .detail-label { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
    .detail-value { font-size:14px; font-weight:600; color:#1e293b; }

    .detail-divider { height:1px; background:#f1f5f9; margin:20px 0; }

    /* Action buttons */
    .actions-row { display:flex; flex-wrap:wrap; gap:12px; }
    .btn {
        display:inline-flex; align-items:center; gap:8px;
        padding:10px 20px; border-radius:10px;
        font-size:13px; font-weight:600; cursor:pointer;
        border:none; transition:transform 0.15s, box-shadow 0.15s;
        text-decoration:none;
    }
    .btn-primary {
        background:linear-gradient(135deg,#1e40af,#3b82f6);
        color:white; box-shadow:0 4px 12px rgba(59,130,246,0.3);
    }
    .btn-danger {
        background:linear-gradient(135deg,#dc2626,#ef4444);
        color:white; box-shadow:0 4px 12px rgba(239,68,68,0.3);
    }
    .btn-outline {
        background:white; color:#64748b;
        border:1px solid #e2e8f0;
    }
    .btn:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(0,0,0,0.15); }

    /* Modal */
    .modal-overlay {
        display:none; position:fixed; inset:0;
        background:rgba(0,0,0,0.5); backdrop-filter:blur(4px);
        z-index:1000; align-items:center; justify-content:center;
    }
    .modal-overlay.active { display:flex; }
    .modal-box {
        background:white; border-radius:20px;
        padding:28px; width:100%; max-width:440px;
        box-shadow:0 20px 60px rgba(0,0,0,0.2);
        margin:16px;
    }
    .modal-title { font-size:16px; font-weight:700; color:#1e293b; margin-bottom:20px; display:flex; align-items:center; gap:8px; }
    .modal-title svg { color:#3b82f6; }
    .form-group { margin-bottom:16px; }
    .form-label { display:block; font-size:12px; font-weight:700; color:#64748b; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px; }
    .form-control {
        width:100%; padding:10px 14px; border-radius:10px;
        border:1.5px solid #e2e8f0; font-size:14px; color:#1e293b;
        outline:none; transition:border-color 0.2s, box-shadow 0.2s;
        font-family:inherit; background:white;
    }
    .form-control:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.12); }
    .modal-footer { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
</style>

<div class="max-w-3xl mx-auto">

    <a href="{{ route('admin.queue.index') }}" class="back-link">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Queue
    </a>

    <div class="detail-card">
        {{-- Card Header --}}
        <div class="detail-card-header">
            <div>
                <div class="header-title">Appointment Details</div>
                <div class="header-sub">Client record — manage and assign below</div>
            </div>
            <span class="appt-num-badge">#{{ sprintf('%03d', $appointment->id) }}</span>
        </div>

        {{-- Detail Body --}}
        <div class="detail-body">
            <div class="detail-grid">
                <div class="detail-field" style="grid-column:1/-1;">
                    <div class="detail-label">Full Name</div>
                    <div class="detail-value" style="font-size:16px;">{{ $appointment->fullname }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Age</div>
                    <div class="detail-value">{{ $appointment->age }} yrs</div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Sex</div>
                    <div class="detail-value">{{ $appointment->sex }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Type</div>
                    <div class="detail-value">{{ $appointment->type }}</div>
                </div>
                <div class="detail-field">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        @php $s = strtolower($appointment->status); @endphp
                        <span class="status-badge status-{{ $s }}">{{ ucfirst($appointment->status) }}</span>
                    </div>
                </div>
                <div class="detail-field" style="grid-column:1/-1;">
                    <div class="detail-label">Scheduled At</div>
                    <div class="detail-value">{{ $appointment->scheduled_at ? $appointment->scheduled_at->format('F j, Y — g:i A') : '—' }}</div>
                </div>
            </div>

            <div class="detail-divider"></div>

            {{-- Actions --}}
            <div class="actions-row">
                <button type="button" id="openAssignModal" class="btn btn-primary">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24"><path d="M8 7V3M16 7V3M3 11h18M5 21h14a2 2 0 0 0 2-2V7H3v12a2 2 0 0 0 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Assign Slot
                </button>
                <form method="POST" action="{{ route('admin.queue.delete', $appointment) }}" onsubmit="return confirm('Delete this appointment? This will notify the user.');" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Assign Modal --}}
<div class="modal-overlay" id="assignModal">
    <div class="modal-box">
        <div class="modal-title">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Assign Appointment
        </div>
        <form method="POST" action="{{ route('admin.queue.assign', $appointment) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Date &amp; Time</label>
                <input type="datetime-local" name="scheduled_at" class="form-control" required />
            </div>
            <div class="modal-footer">
                <button type="button" id="closeAssign" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Confirm Assign
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('openAssignModal').addEventListener('click', function(){
        document.getElementById('assignModal').classList.add('active');
    });
    document.getElementById('closeAssign').addEventListener('click', function(){
        document.getElementById('assignModal').classList.remove('active');
    });
    document.getElementById('assignModal').addEventListener('click', function(e){
        if (e.target === this) this.classList.remove('active');
    });
</script>
@endsection
