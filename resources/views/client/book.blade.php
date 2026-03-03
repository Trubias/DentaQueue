@extends('layouts.client')

@section('content')
<style>
    .book-header {
        margin-bottom: 24px;
    }
    .book-title {
        font-size: 26px; font-weight: 800;
        color: #0f172a; letter-spacing: -0.8px;
    }
    .book-subtitle { font-size: 14px; color: #64748b; margin-top: 4px; }

    .book-card {
        background: white;
        border-radius: 18px;
        border: 1px solid #e8edf5;
        box-shadow: 0 4px 20px rgba(15,23,42,0.07);
        overflow: hidden;
        max-width: 580px;
    }
    .book-card-header {
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        padding: 22px 26px;
        display: flex; align-items: center; gap: 12px;
    }
    .book-card-icon {
        width: 44px; height: 44px; border-radius: 12px;
        background: rgba(255,255,255,0.18);
        border: 1px solid rgba(255,255,255,0.25);
        display: flex; align-items: center; justify-content: center;
        color: white;
    }
    .book-card-title { color: white; font-size: 17px; font-weight: 700; letter-spacing: -0.3px; }
    .book-card-subtitle { color: rgba(255,255,255,0.65); font-size: 12.5px; margin-top: 2px; }
    .book-card-body { padding: 28px 26px; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 500px) { .form-row { grid-template-columns: 1fr; } }

    .type-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
        margin-top: 6px;
    }
    @media (max-width: 500px) { .type-grid { grid-template-columns: 1fr; } }

    .type-option { position: relative; }
    .type-option input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
    .type-label {
        display: flex; align-items: center; gap: 12px;
        padding: 13px 15px; border-radius: 12px;
        border: 2px solid #e2e8f0;
        cursor: pointer; transition: all 0.18s ease;
        background: #fafbff;
    }
    .type-label:hover { border-color: #93c5fd; background: #eff6ff; }
    .type-option input:checked + .type-label {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }
    .type-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 18px;
    }
    .type-name { font-size: 13.5px; font-weight: 600; color: #1e293b; }
    .type-desc { font-size: 11.5px; color: #94a3b8; margin-top: 1px; }

    .check-icon {
        margin-left: auto;
        width: 20px; height: 20px; border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; transition: all 0.18s;
    }
    .type-option input:checked + .type-label .check-icon {
        background: #2563eb; border-color: #2563eb; color: white;
    }

    .book-footer {
        padding: 18px 26px 22px;
        border-top: 1px solid #f1f5f9;
        display: flex; gap: 10px; align-items: center;
        flex-wrap: wrap;
    }
    .active-notice {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 14px 18px;
        display: flex; align-items: flex-start; gap: 10px;
        font-size: 13.5px; color: #92400e;
        margin-bottom: 22px;
    }
</style>

<div class="book-header">
    <div class="book-title">Book an Appointment</div>
    <div class="book-subtitle">Fill in the form below to schedule your visit</div>
</div>

@if(session('error'))
<div class="alert alert-danger" style="max-width:580px; margin-bottom:18px;">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    {{ session('error') }}
</div>
@endif

@if(!empty($active))
<div class="active-notice" style="max-width:580px;">
    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="margin-top:1px;flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <div>
        <strong>Active Appointment Exists</strong><br>
        You can only have one active appointment at a time. Please manage your existing appointment before booking a new one.
    </div>
</div>
@endif

<div class="book-card">
    <div class="book-card-header">
        <div class="book-card-icon">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="14" x2="8.01" y2="14"/><line x1="12" y1="14" x2="12.01" y2="14"/></svg>
        </div>
        <div>
            <div class="book-card-title">New Appointment</div>
            <div class="book-card-subtitle">Patient Portal — DentaQueue</div>
        </div>
    </div>

    <form method="POST" action="{{ route('client.book.store') }}" id="bookForm" data-active="{{ !empty($active) ? '1' : '0' }}">
        @csrf
        <div class="book-card-body">
            <!-- Full Name -->
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input name="fullname" class="form-control" placeholder="Your full name" value="{{ old('fullname', auth()->user()->name) }}">
                @error('fullname')<div class="form-error"><svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>{{ $message }}</div>@enderror
            </div>

            <!-- Age & Sex -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Age</label>
                    <input name="age" type="number" min="1" max="120" class="form-control" placeholder="e.g. 25" value="{{ old('age', auth()->user()->age) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Sex</label>
                    <select name="sex" class="form-control">
                        <option value="">Select</option>
                        <option value="Male" {{ old('sex', auth()->user()->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('sex', auth()->user()->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('sex', auth()->user()->sex) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <!-- Appointment Type -->
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Appointment Type</label>
                <div class="type-grid">
                    <div class="type-option">
                        <input type="radio" name="type" id="type-checkup" value="Check-up" {{ old('type','Check-up')=='Check-up'?'checked':'' }}>
                        <label class="type-label" for="type-checkup">
                            <div class="type-icon" style="background:#dbeafe;">🦷</div>
                            <div>
                                <div class="type-name">Check-up</div>
                                <div class="type-desc">General dental exam</div>
                            </div>
                            <div class="check-icon">
                                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                        </label>
                    </div>
                    <div class="type-option">
                        <input type="radio" name="type" id="type-cleaning" value="Cleaning" {{ old('type')=='Cleaning'?'checked':'' }}>
                        <label class="type-label" for="type-cleaning">
                            <div class="type-icon" style="background:#d1fae5;">✨</div>
                            <div>
                                <div class="type-name">Teeth Cleaning</div>
                                <div class="type-desc">Professional cleaning</div>
                            </div>
                            <div class="check-icon">
                                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                        </label>
                    </div>
                    <div class="type-option">
                        <input type="radio" name="type" id="type-extraction" value="Extraction" {{ old('type')=='Extraction'?'checked':'' }}>
                        <label class="type-label" for="type-extraction">
                            <div class="type-icon" style="background:#fee2e2;">🦷</div>
                            <div>
                                <div class="type-name">Extraction</div>
                                <div class="type-desc">Tooth removal</div>
                            </div>
                            <div class="check-icon">
                                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                        </label>
                    </div>
                    <div class="type-option">
                        <input type="radio" name="type" id="type-braces" value="Braces" {{ old('type')=='Braces'?'checked':'' }}>
                        <label class="type-label" for="type-braces">
                            <div class="type-icon" style="background:#ede9fe;">😁</div>
                            <div>
                                <div class="type-name">Braces</div>
                                <div class="type-desc">Braces installation</div>
                            </div>
                            <div class="check-icon">
                                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                        </label>
                    </div>
                </div>
                @error('type')<div class="form-error" style="margin-top:8px;"><svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="book-footer">
            <button type="submit" class="btn btn-primary" id="submitBtn" {{ !empty($active) ? 'disabled style=opacity:.5;cursor:not-allowed;' : '' }}>
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Submit Request
            </button>
            <a href="{{ route('client.dashboard') }}" class="btn btn-ghost">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Back to Home
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.getElementById('bookForm');
        if (!form) return;
        form.addEventListener('submit', function(e){
            if (form.getAttribute('data-active') === '1') {
                e.preventDefault();
                alert('You already have an active appointment. Please manage it before booking another.');
            }
        });
    });
</script>
@endsection
