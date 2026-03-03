@extends('layouts.client')

@section('content')
<style>
    .settings-header { margin-bottom: 24px; }
    .settings-title { font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.8px; }
    .settings-subtitle { font-size: 14px; color: #64748b; margin-top: 4px; }

    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        max-width: 800px;
    }
    @media (max-width: 680px) { .settings-grid { grid-template-columns: 1fr; } }

    .settings-card {
        background: white;
        border-radius: 18px;
        border: 1px solid #e8edf5;
        box-shadow: 0 4px 20px rgba(15,23,42,0.06);
        overflow: hidden;
    }
    .settings-card-header {
        padding: 18px 22px 16px;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 12px;
    }
    .settings-card-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .settings-card-icon.profile { background: linear-gradient(135deg, #dbeafe, #eff6ff); color: #2563eb; }
    .settings-card-icon.password { background: linear-gradient(135deg, #fce7f3, #fdf2f8); color: #db2777; }
    .settings-card-title { font-size: 15px; font-weight: 700; color: #0f172a; }
    .settings-card-subtitle { font-size: 12px; color: #94a3b8; margin-top: 2px; }
    .settings-card-body { padding: 22px; }

    /* Avatar preview */
    .avatar-section {
        display: flex; align-items: center; gap: 16px;
        margin-bottom: 20px;
        padding: 14px; background: #f8faff;
        border-radius: 12px; border: 1px solid #e8edf5;
    }
    .avatar-preview {
        width: 58px; height: 58px; border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #0ea5e9);
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 22px; color: white;
        border: 3px solid white; box-shadow: 0 4px 12px rgba(37,99,235,0.2);
        overflow: hidden; flex-shrink: 0;
    }
    .avatar-preview img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-info { flex: 1; min-width: 0; }
    .avatar-name { font-size: 15px; font-weight: 700; color: #1e293b; }
    .avatar-email { font-size: 12.5px; color: #94a3b8; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* File upload button */
    .file-upload-area {
        position: relative; margin-top: 6px;
    }
    .file-upload-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 14px; border-radius: 10px;
        background: #f1f5f9; color: #475569;
        border: 1.5px dashed #cbd5e1;
        font-size: 13px; font-weight: 500;
        cursor: pointer; font-family: inherit;
        transition: all 0.18s ease; width: 100%;
        justify-content: center;
    }
    .file-upload-btn:hover { background: #e8f0fe; border-color: #93c5fd; color: #2563eb; }
    .file-upload-btn input[type="file"] {
        position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%;
    }

    /* Password strength */
    .pw-requirements {
        margin-top: 8px; padding: 10px 12px;
        background: #f8faff; border-radius: 10px;
        border: 1px solid #e8edf5;
    }
    .pw-req { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #94a3b8; margin-bottom: 3px; }
    .pw-req:last-child { margin-bottom: 0; }
    .pw-dot { width: 6px; height: 6px; border-radius: 50%; background: #cbd5e1; flex-shrink: 0; }

    .settings-footer {
        padding: 16px 22px;
        border-top: 1px solid #f1f5f9;
        display: flex; gap: 8px;
    }
</style>

<div class="settings-header">
    <div class="settings-title">Settings</div>
    <div class="settings-subtitle">Manage your profile and account preferences</div>
</div>

@if(session('success'))
<div class="alert alert-success" style="max-width:800px; margin-bottom:18px;">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger" style="max-width:800px; margin-bottom:18px;">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<div class="settings-grid">
    <!-- Profile Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon profile">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <div class="settings-card-title">Profile Info</div>
                <div class="settings-card-subtitle">Update your personal details</div>
            </div>
        </div>
        <form method="POST" action="{{ route('client.settings.profile.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="settings-card-body">
                <!-- Avatar preview -->
                <div class="avatar-section">
                    <div class="avatar-preview" id="avatarPreview">
                        @if(auth()->user() && auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" id="avatarImg">
                        @else
                            <span id="avatarInitial">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="avatar-info">
                        <div class="avatar-name">{{ auth()->user()->name }}</div>
                        <div class="avatar-email">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input name="name" value="{{ auth()->user()->name }}" class="form-control" placeholder="Your full name">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input name="email" type="email" value="{{ auth()->user()->email }}" class="form-control" placeholder="your@email.com">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Profile Picture</label>
                    <div class="file-upload-area">
                        <label class="file-upload-btn">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <span id="fileLabel">Upload new photo</span>
                            <input type="file" name="avatar" accept="image/*" onchange="previewAvatar(this)">
                        </label>
                    </div>
                    <div class="form-hint">JPG, PNG, GIF up to 2MB</div>
                </div>
            </div>
            <div class="settings-footer">
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Save Profile
                </button>
            </div>
        </form>
    </div>

    <!-- Password Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon password">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div>
                <div class="settings-card-title">Change Password</div>
                <div class="settings-card-subtitle">Update your account security</div>
            </div>
        </div>
        <form method="POST" action="{{ route('client.settings.password.update') }}">
            @csrf
            <div class="settings-card-body">
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <div style="position:relative;">
                        <input type="password" name="current" class="form-control" placeholder="Enter current password" id="pw-current" style="padding-right:42px;">
                        <button type="button" onclick="togglePw('pw-current','eye-current')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;">
                            <svg id="eye-current" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" class="form-control" placeholder="At least 8 characters" id="pw-new" style="padding-right:42px;">
                        <button type="button" onclick="togglePw('pw-new','eye-new')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;">
                            <svg id="eye-new" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Confirm New Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat new password" id="pw-confirm" style="padding-right:42px;">
                        <button type="button" onclick="togglePw('pw-confirm','eye-confirm')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;">
                            <svg id="eye-confirm" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="pw-requirements" style="margin-top:10px;">
                        <div class="pw-req"><div class="pw-dot"></div> Minimum 8 characters</div>
                        <div class="pw-req"><div class="pw-dot"></div> Mix of letters and numbers recommended</div>
                    </div>
                </div>
            </div>
            <div class="settings-footer">
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('avatarPreview');
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">';
            };
            reader.readAsDataURL(input.files[0]);
            document.getElementById('fileLabel').textContent = input.files[0].name;
        }
    }

    function togglePw(inputId, eyeId) {
        var input = document.getElementById(inputId);
        var eye = document.getElementById(eyeId);
        if (input.type === 'password') {
            input.type = 'text';
            eye.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>';
        } else {
            input.type = 'password';
            eye.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
        }
    }
</script>
@endsection
