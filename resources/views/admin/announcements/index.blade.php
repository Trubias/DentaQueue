@extends('layouts.admin')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:28px; }
    .page-title { font-size:22px; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:10px; }
    .page-title svg { color:#3b82f6; }

    .alert-success {
        display:flex; align-items:center; gap:10px;
        padding:12px 16px; border-radius:12px; margin-bottom:20px;
        background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.25); color:#065f46; font-size:13px; font-weight:600;
    }

    /* Toolbar */
    .toolbar { display:flex; flex-wrap:wrap; align-items:center; gap:10px; }

    /* Buttons */
    .btn {
        display:inline-flex; align-items:center; gap:7px;
        padding:9px 18px; border-radius:10px;
        font-size:13px; font-weight:600; cursor:pointer; border:none;
        text-decoration:none; transition:transform 0.15s, box-shadow 0.15s; white-space:nowrap;
    }
    .btn:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(0,0,0,0.13); }
    .btn-primary { background:linear-gradient(135deg,#1e40af,#3b82f6); color:white; box-shadow:0 4px 12px rgba(59,130,246,0.3); }
    .btn-success { background:linear-gradient(135deg,#059669,#10b981); color:white; box-shadow:0 4px 12px rgba(16,185,129,0.3); }
    .btn-outline  { background:white; color:#475569; border:1.5px solid #e2e8f0; }

    /* Table card */
    .table-card { background:white; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,0.06); border:1px solid rgba(226,232,240,0.8); overflow:hidden; }
    .table-card table { width:100%; border-collapse:collapse; }
    .table-card thead { background:linear-gradient(135deg,#1e3a8a,#1e40af); }
    .table-card thead th { padding:13px 18px; text-align:left; font-size:12px; font-weight:700; letter-spacing:.5px; color:rgba(255,255,255,0.9); text-transform:uppercase; white-space:nowrap; }
    .table-card tbody tr { border-bottom:1px solid #f1f5f9; transition:background 0.15s; }
    .table-card tbody tr:last-child { border-bottom:none; }
    .table-card tbody tr:hover { background:#f8fafc; }
    .table-card td { padding:13px 18px; font-size:13px; color:#334155; }

    /* Sent badge */
    .sent-badge   { display:inline-block; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; background:rgba(16,185,129,0.12); color:#065f46; }
    .unsent-badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; background:rgba(245,158,11,0.12); color:#b45309; }

    .recipient-chip { display:inline-flex; align-items:center; gap:5px; background:#f1f5f9; border-radius:6px; padding:2px 8px; font-size:12px; color:#475569; }

    .empty-state { display:flex; flex-direction:column; align-items:center; gap:8px; padding:48px 0; color:#94a3b8; font-size:13px; }

    /* Modal */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:1000; align-items:flex-start; justify-content:center; padding-top:80px; }
    .modal-overlay.active { display:flex; }
    .modal-box { background:white; border-radius:20px; padding:28px; width:100%; max-width:520px; box-shadow:0 20px 60px rgba(0,0,0,0.2); margin:0 16px; }
    .modal-title { font-size:16px; font-weight:700; color:#1e293b; margin-bottom:20px; display:flex; align-items:center; gap:8px; }
    .modal-title svg { color:#3b82f6; }
    .form-group { margin-bottom:14px; }
    .form-label { display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
    .form-control { width:100%; padding:10px 14px; border-radius:10px; border:1.5px solid #e2e8f0; font-size:13px; color:#1e293b; outline:none; transition:border-color 0.2s, box-shadow 0.2s; font-family:inherit; background:white; }
    .form-control:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.12); }
    textarea.form-control { resize:vertical; }
    .modal-footer { display:flex; justify-content:flex-end; gap:10px; margin-top:16px; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<div class="max-w-7xl mx-auto">

    <div class="page-header">
        <div class="page-title">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M4 6h16v10H4z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M22 6l-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Announcements
        </div>
        <div class="toolbar">
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-outline">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M12 4v16M4 12h16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                New Announcement
            </a>
            <a href="{{ route('admin.announcements.inventory') }}" class="btn btn-primary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" stroke="currentColor" stroke-width="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" stroke="currentColor" stroke-width="2"/></svg>
                Inventory
            </a>
            <button id="openSendEmailTop" class="btn btn-success">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="2"/><path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Send Email
            </button>
        </div>
    </div>

    @if(session('status'))
        <div class="alert-success">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            {{ session('status') }}
        </div>
    @endif

    <div class="table-card">
        @if($items->isEmpty())
            <div class="empty-state">
                <svg width="42" height="42" fill="none" viewBox="0 0 24 24"><path d="M4 6h16v10H4z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M22 6l-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                No announcements yet. Create one!
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Recipient</th>
                        <th>Appointment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $it)
                        <tr>
                            <td style="font-weight:600;">{{ $it->title }}</td>
                            <td>
                                <span class="recipient-chip">
                                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/></svg>
                                    {{ $it->user ? $it->user->name . ' (' . $it->user->email . ')' : 'All Users' }}
                                </span>
                            </td>
                            <td>{{ $it->appointment ? '#' . sprintf('%03d', $it->appointment->id) : '—' }}</td>
                            <td>
                                @if($it->sent_at)
                                    <span class="sent-badge">✓ Sent {{ $it->sent_at->diffForHumans() }}</span>
                                @else
                                    <span class="unsent-badge">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($it->user && !$it->sent_at)
                                    <button class="btn btn-primary send-email-btn"
                                            style="padding:6px 14px; font-size:12px;"
                                            data-id="{{ $it->id }}"
                                            data-title="{{ htmlentities($it->title) }}"
                                            data-body="{{ htmlentities($it->body) }}"
                                            data-to="{{ $it->user->email }}"
                                            data-recipient-name="{{ $it->user->name }}">
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                        Send
                                    </button>
                                @elseif(!$it->user && !$it->sent_at)
                                    <span style="font-size:11px; color:#94a3b8;">All users</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- Toast container --}}
<div id="dq-toast-container" style="position:fixed;top:1.25rem;right:1.25rem;z-index:9999;pointer-events:none;display:flex;flex-direction:column;gap:8px;"></div>

{{-- Send Modal --}}
<div class="modal-overlay" id="sendModal">
    <div class="modal-box">
        <div class="modal-title">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="2"/><path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Send Announcement Email
        </div>
        <form id="sendForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">From</label>
                <input id="fromInput" class="form-control" readonly/>
            </div>
            <div class="form-group">
                <label class="form-label">To</label>
                <input id="toInput" name="to" class="form-control"/>
            </div>
            <div class="form-group">
                <label class="form-label">Message</label>
                <textarea id="bodyInput" name="body" class="form-control" rows="6"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Attachment (optional)</label>
                <input type="file" name="attachment" class="form-control" style="padding:6px 10px;" />
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelSend" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-success">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Send
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showToast(message, type = 'success', timeout = 4000) {
    const container = document.getElementById('dq-toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.style.cssText = `pointer-events:auto;min-width:220px;background:${type==='success'?'#16a34a':'#dc2626'};color:white;padding:.65rem 1.1rem;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.15);font-size:13px;font-weight:600;opacity:0;transform:translateX(20px);transition:all 200ms ease;`;
    toast.innerText = message;
    container.appendChild(toast);
    requestAnimationFrame(() => { toast.style.opacity = '1'; toast.style.transform = 'translateX(0)'; });
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => container.removeChild(toast), 250); }, timeout);
}

const ALLOWED_EXT = ['pdf','doc','docx','jpg','jpeg','png','txt','zip'];
const MAX_BYTES = 5 * 1024 * 1024;
function validateAttachment(file) {
    if (!file) return true;
    const ext = (file.name || '').split('.').pop().toLowerCase();
    if (!ALLOWED_EXT.includes(ext)) { showToast('Invalid file type. Allowed: ' + ALLOWED_EXT.join(', '), 'error'); return false; }
    if (file.size > MAX_BYTES) { showToast('Attachment too large. Max 5MB.', 'error'); return false; }
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('sendModal');
    const fromInput = document.getElementById('fromInput');
    const toInput = document.getElementById('toInput');
    const bodyInput = document.getElementById('bodyInput');
    const sendForm = document.getElementById('sendForm');

    function openModal(item) {
        const adminEmail = '{{ config("mail.from.address", auth()->user() ? auth()->user()->email : "") }}';
        fromInput.value = adminEmail;
        toInput.value = item.to || '';
        bodyInput.value = item.body || '';
        if (item.id) { sendForm.action = '/admin/announcements/' + item.id + '/send'; toInput.readOnly = true; }
        else { sendForm.action = '{{ route("admin.mail.test") }}'; toInput.readOnly = false; }
        modal.classList.add('active');
    }

    document.querySelectorAll('.send-email-btn').forEach(btn => btn.addEventListener('click', function() {
        openModal({ id: this.dataset.id, body: this.dataset.body, to: this.dataset.to });
    }));

    const openTop = document.getElementById('openSendEmailTop');
    if (openTop) openTop.addEventListener('click', () => openModal({ id: null, body: '' }));

    document.getElementById('cancelSend').addEventListener('click', () => modal.classList.remove('active'));
    modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('active'); });

    sendForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const fileInput = sendForm.querySelector('input[type=file]');
        const file = fileInput && fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
        if (!validateAttachment(file)) return;
        const submitBtn = sendForm.querySelector('button[type=submit]');
        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M21 12a9 9 0 1 1-6.22-8.56" stroke-linecap="round"/></svg> Sending…'; }
        const formData = new FormData(sendForm);
        try {
            const resp = await fetch(sendForm.action, { method:'POST', credentials:'same-origin', body:formData, headers:{'X-Requested-With':'XMLHttpRequest'} });
            const data = await resp.json();
            if (resp.ok && data.success) {
                modal.classList.remove('active');
                showToast('✅ Email has been sent successfully!', 'success', 3500);
                setTimeout(() => { window.location.href = '{{ route("admin.announcements.index") }}'; }, 1500);
            } else {
                showToast(data.message || 'Failed to send', 'error');
                if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Send'; }
            }
        } catch(err) {
            showToast('Failed to send: ' + (err.message || err), 'error');
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Send'; }
        }
    });

    const mailTestForm = document.getElementById('mailTestForm');
    if (mailTestForm) {
        mailTestForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            try {
                const resp = await fetch(mailTestForm.action, { method:'POST', credentials:'same-origin', headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content||''} });
                const data = await resp.json();
                if (resp.ok && data.success) showToast(data.message || 'Test email sent');
                else showToast(data.message || 'Mail test failed','error');
            } catch(err) { showToast('Mail test failed: '+(err.message||err),'error'); }
        });
    }
});
</script>
@endpush

@endsection
