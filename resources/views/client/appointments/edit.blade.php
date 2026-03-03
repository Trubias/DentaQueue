@extends('layouts.client')

@section('content')
<style>
    .edit-header { margin-bottom: 24px; }
    .edit-title {
        font-size: 26px; font-weight: 800;
        color: #0f172a; letter-spacing: -0.8px;
    }
    .edit-subtitle { font-size: 14px; color: #64748b; margin-top: 4px; }

    .edit-card {
        background: white;
        border-radius: 18px;
        border: 1px solid #e8edf5;
        box-shadow: 0 4px 20px rgba(15,23,42,0.07);
        overflow: hidden;
        max-width: 580px;
    }
    .edit-card-header {
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        padding: 22px 26px;
        display: flex; align-items: center; gap: 14px;
    }
    .edit-card-icon {
        width: 48px; height: 48px; border-radius: 13px;
        background: rgba(255,255,255,0.18);
        border: 1px solid rgba(255,255,255,0.25);
        display: flex; align-items: center; justify-content: center;
        color: white; flex-shrink: 0;
    }
    .edit-card-title { color: white; font-size: 18px; font-weight: 700; letter-spacing: -0.3px; }
    .edit-card-subtitle { color: rgba(255,255,255,0.65); font-size: 12.5px; margin-top: 2px; }

    .edit-body { padding: 28px 26px; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 500px) { .form-row { grid-template-columns: 1fr; } }

    .type-select-wrapper { position: relative; }
    .type-select-wrapper .select-icon {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; pointer-events: none;
    }
    .type-select-wrapper select.form-control {
        padding-left: 40px;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 40px;
        cursor: pointer;
    }

    .appt-number-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.22);
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.85);
        margin-top: 6px;
    }

    .edit-footer {
        padding: 18px 26px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex; gap: 10px; flex-wrap: wrap;
    }

    .field-icon-wrapper { position: relative; }
    .field-icon-wrapper .field-icon {
        position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
        color: #94a3b8; pointer-events: none;
    }
    .field-icon-wrapper input.form-control { padding-left: 40px; }

    .sex-select-wrapper { position: relative; }
    .sex-select-wrapper select.form-control {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 40px;
        cursor: pointer;
    }

    .info-notice {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 11px;
        padding: 11px 15px;
        display: flex; align-items: center; gap: 9px;
        font-size: 12.5px; color: #1e40af;
        margin-bottom: 22px;
    }
</style>

<div class="edit-header">
    <div class="edit-title">Edit Appointment</div>
    <div class="edit-subtitle">Update the details for your scheduled visit</div>
</div>

@if(session('error'))
<div class="alert alert-danger" style="max-width:580px; margin-bottom:18px;">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    {{ session('error') }}
</div>
@endif

<div class="edit-card">
    <div class="edit-card-header">
        <div class="edit-card-icon">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
        </div>
        <div>
            <div class="edit-card-title">Edit Appointment</div>
            <div class="appt-number-badge">
                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
                #{{ sprintf('%03d', $appointment->id) }}
            </div>
            <div class="edit-card-subtitle" style="margin-top:4px;">Patient Portal — DentaQueue</div>
        </div>
    </div>

    <form method="POST" action="{{ route('client.appointments.update', $appointment) }}">
        @csrf
        @method('PUT')

        <div class="edit-body">
            <div class="info-notice">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Changes will be applied immediately. Only pending appointments can be edited.
            </div>

            <!-- Full Name -->
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <div class="field-icon-wrapper">
                    <span class="field-icon">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <input type="text" name="fullname" value="{{ old('fullname', $appointment->fullname) }}" class="form-control" placeholder="Your full name" required>
                </div>
                @error('fullname')<div class="form-error"><svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>@enderror
            </div>

            <!-- Age & Sex -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Age</label>
                    <div class="field-icon-wrapper">
                        <span class="field-icon">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </span>
                        <input type="number" name="age" value="{{ old('age', $appointment->age) }}" class="form-control" placeholder="Age" min="1" max="120">
                    </div>
                    @error('age')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Sex</label>
                    <div class="sex-select-wrapper">
                        <select name="sex" class="form-control">
                            <option value="">Select</option>
                            <option value="Male" {{ old('sex', $appointment->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex', $appointment->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('sex', $appointment->sex) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    @error('sex')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <!-- Appointment Type -->
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Appointment Type</label>
                <div class="type-select-wrapper">
                    <span class="select-icon">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2a7 7 0 0 1 7 7c0 5-7 13-7 13S5 14 5 9a7 7 0 0 1 7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
                    </span>
                    <select name="type" class="form-control" required>
                        <option value="Check-up" {{ old('type', $appointment->type) == 'Check-up' ? 'selected' : '' }}>🦷 Check-up</option>
                        <option value="Cleaning" {{ old('type', $appointment->type) == 'Cleaning' ? 'selected' : '' }}>✨ Teeth Cleaning</option>
                        <option value="Extraction" {{ old('type', $appointment->type) == 'Extraction' ? 'selected' : '' }}>🦷 Extraction</option>
                        <option value="Braces" {{ old('type', $appointment->type) == 'Braces' ? 'selected' : '' }}>😁 Braces Installation</option>
                    </select>
                </div>
                @error('type')<div class="form-error" style="margin-top:6px;">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="edit-footer">
            <button type="submit" class="btn btn-primary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Save Changes
            </button>
            <a href="{{ route('client.dashboard') }}" class="btn btn-ghost">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
