@extends('layouts.admin')

@section('content')
<style>
    .back-link { display:inline-flex; align-items:center; gap:6px; color:#64748b; font-size:13px; font-weight:600; text-decoration:none; margin-bottom:20px; transition:color .15s; }
    .back-link:hover { color:#1e40af; }

    .form-card { background:white; border-radius:20px; box-shadow:0 4px 20px rgba(0,0,0,0.07); border:1px solid rgba(226,232,240,0.8); overflow:hidden; max-width:680px; }
    .form-card-header { background:linear-gradient(135deg,#1e3a8a,#1e40af); padding:22px 26px; display:flex; align-items:center; gap:14px; }
    .form-card-icon { width:42px; height:42px; border-radius:11px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; color:white; flex-shrink:0; }
    .form-card-title { font-size:17px; font-weight:700; color:white; }
    .form-card-sub   { font-size:11px; color:rgba(255,255,255,0.6); margin-top:3px; }
    .form-card-body  { padding:26px; }

    .form-group { margin-bottom:18px; }
    .form-label { display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:7px; }
    .form-control { width:100%; padding:10px 14px; border-radius:10px; border:1.5px solid #e2e8f0; font-size:13px; color:#1e293b; outline:none; transition:border-color .2s, box-shadow .2s; font-family:inherit; background:white; }
    .form-control:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.12); }
    textarea.form-control { resize:vertical; }
    select.form-control { appearance:auto; }
    .form-file { padding:7px 10px; }

    .recipient-hint { display:flex; align-items:center; gap:6px; margin-top:6px; font-size:12px; color:#64748b; }
    .recipient-hint svg { color:#3b82f6; flex-shrink:0; }

    .btn { display:inline-flex; align-items:center; gap:7px; padding:10px 22px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:transform .15s, box-shadow .15s; text-decoration:none; }
    .btn:hover { transform:translateY(-1px); }
    .btn-primary { background:linear-gradient(135deg,#059669,#10b981); color:white; box-shadow:0 4px 12px rgba(16,185,129,0.3); }
    .btn-outline  { background:white; color:#64748b; border:1.5px solid #e2e8f0; }
    .form-footer { display:flex; align-items:center; gap:10px; border-top:1px solid #f1f5f9; padding-top:18px; margin-top:4px; }
</style>

<div class="max-w-2xl mx-auto">
    <a href="{{ route('admin.announcements.index') }}" class="back-link">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Announcements
    </a>

    <div class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M4 6h16v10H4z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M22 6l-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div>
                <div class="form-card-title">Create Announcement</div>
                <div class="form-card-sub">Drafts are saved; send emails separately from the list</div>
            </div>
        </div>

        <div class="form-card-body">
            <form method="POST" action="{{ route('admin.announcements.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input name="title" class="form-control" placeholder="e.g. Appointment Reminder" required />
                </div>
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea name="body" class="form-control" rows="5" placeholder="Write your announcement message…"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Recipient</label>
                    <select id="recipientSelect" name="user_id" class="form-control">
                        <option value="">All Users</option>
                        @foreach(\App\Models\User::orderBy('name')->get() as $u)
                            <option value="{{ $u->id }}" data-email="{{ $u->email }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                    <div class="recipient-hint">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="2"/><path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Will be sent to: <strong id="recipientEmail">All Users</strong>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Attachment <span style="text-transform:none; font-weight:500;">(optional, max 5MB)</span></label>
                    <input type="file" name="attachment" class="form-control form-file" />
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Save Announcement
                    </button>
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function(){
    var sel = document.getElementById('recipientSelect');
    var out = document.getElementById('recipientEmail');
    if (!sel) return;
    function update(){
        var opt = sel.options[sel.selectedIndex];
        out.textContent = opt && opt.dataset && opt.dataset.email ? opt.dataset.email : 'All Users';
    }
    sel.addEventListener('change', update);
    update();
})();
</script>
@endpush

    </div>
@endsection
