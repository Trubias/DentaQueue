@extends('layouts.admin')

@section('content')
<style>
    .page-header { display:flex; align-items:center; margin-bottom:28px; gap:12px; }
    .page-title  { font-size:22px; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:10px; }
    .page-title svg { color:#3b82f6; }

    .alert-success { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:12px; margin-bottom:20px; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.25); color:#065f46; font-size:13px; font-weight:600; }
    .alert-error   { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:12px; margin-bottom:20px; background:rgba(239,68,68,0.1);  border:1px solid rgba(239,68,68,0.25);  color:#b91c1c; font-size:13px; font-weight:600; }

    .settings-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
    @media(max-width:768px){ .settings-grid { grid-template-columns:1fr; } }

    .settings-card { background:white; border-radius:18px; box-shadow:0 2px 12px rgba(0,0,0,0.06); border:1px solid rgba(226,232,240,0.8); overflow:hidden; }
    .settings-card-header {
        background:linear-gradient(135deg,#1e3a8a,#1e40af);
        padding:18px 22px;
        display:flex; align-items:center; gap:12px;
    }
    .settings-card-header-icon {
        width:38px; height:38px; border-radius:10px;
        background:rgba(255,255,255,0.15);
        display:flex; align-items:center; justify-content:center;
        color:white; flex-shrink:0;
    }
    .settings-card-title { font-size:15px; font-weight:700; color:white; }
    .settings-card-sub   { font-size:11px; color:rgba(255,255,255,0.65); margin-top:2px; }
    .settings-card-body  { padding:22px; }

    .form-group { margin-bottom:16px; }
    .form-label { display:block; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px; margin-bottom:7px; }
    .form-control {
        width:100%; padding:10px 14px; border-radius:10px;
        border:1.5px solid #e2e8f0; font-size:13px; color:#1e293b;
        outline:none; transition:border-color .2s, box-shadow .2s;
        font-family:inherit; background:white;
    }
    .form-control:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.12); }
    .form-file { padding:6px 10px; }

    .btn { display:inline-flex; align-items:center; gap:7px; padding:10px 20px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:transform .15s, box-shadow .15s; }
    .btn:hover { transform:translateY(-1px); }
    .btn-primary { background:linear-gradient(135deg,#1e40af,#3b82f6); color:white; box-shadow:0 4px 12px rgba(59,130,246,0.3); }
    .btn-danger  { background:linear-gradient(135deg,#dc2626,#ef4444); color:white; box-shadow:0 4px 12px rgba(239,68,68,0.3); }
    .btn:hover { box-shadow:0 6px 20px rgba(0,0,0,0.15); }

    /* Avatar preview */
    .avatar-preview {
        width:64px; height:64px; border-radius:50%;
        background:linear-gradient(135deg,#1e40af,#3b82f6);
        display:flex; align-items:center; justify-content:center;
        font-size:24px; font-weight:800; color:white;
        margin-bottom:12px; overflow:hidden;
    }
    .avatar-preview img { width:100%; height:100%; object-fit:cover; }

    /* Session card full-width */
    .session-card { background:white; border-radius:18px; box-shadow:0 2px 12px rgba(0,0,0,0.06); border:1px solid rgba(226,232,240,0.8); overflow:hidden; margin-top:20px; }
    .session-card-inner { padding:22px 22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; }
    .session-info { }
    .session-title { font-size:15px; font-weight:700; color:#1e293b; }
    .session-sub   { font-size:12px; color:#64748b; margin-top:2px; }
</style>

<div class="max-w-5xl mx-auto">

    <div class="page-header">
        <div class="page-title">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" fill="currentColor"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06A2 2 0 1 1 4.3 17l.06-.06A1.65 1.65 0 0 0 4.7 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06A2 2 0 1 1 7 4.3l.06.06A1.65 1.65 0 0 0 8.88 4.7 1.65 1.65 0 0 0 9.88 3.49V3a2 2 0 1 1 4 0v.09A1.65 1.65 0 0 0 15 4.6a1.65 1.65 0 0 0 1.82-.33l.06-.06A2 2 0 1 1 19.7 7l-.06.06A1.65 1.65 0 0 0 19.3 8.88 1.65 1.65 0 0 0 20.51 9.88H21a2 2 0 1 1 0 4h-.09A1.65 1.65 0 0 0 19.4 15z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            Account Settings
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

    <div class="settings-grid">
        {{-- Profile --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-header-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/><path d="M4 20c0-4 3.582-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <div>
                    <div class="settings-card-title">Profile</div>
                    <div class="settings-card-sub">Update your name, email, and avatar</div>
                </div>
            </div>
            <div class="settings-card-body">
                <div class="avatar-preview">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" />
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.settings.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input name="name" value="{{ auth()->user()->name }}" class="form-control" placeholder="Your name" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input name="email" value="{{ auth()->user()->email }}" class="form-control" placeholder="your@email.com" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" name="avatar" accept="image/*" class="form-control form-file" />
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Save Profile
                    </button>
                </form>
            </div>
        </div>

        {{-- Password --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-header-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <div>
                    <div class="settings-card-title">Change Password</div>
                    <div class="settings-card-sub">Keep your account secure</div>
                </div>
            </div>
            <div class="settings-card-body">
                <form method="POST" action="{{ route('admin.settings.password.update') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current" class="form-control" placeholder="••••••••" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" />
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Session --}}
    <div class="session-card">
        <div class="session-card-inner">
            <div class="session-info">
                <div class="session-title">Sign Out</div>
                <div class="session-sub">End your current session and log out of the admin panel</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
