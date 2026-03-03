@extends('layouts.client')

@section('content')
<style>
    .show-back { margin-bottom: 18px; }
    .show-card {
        background: white;
        border-radius: 18px;
        border: 1px solid #e8edf5;
        box-shadow: 0 4px 20px rgba(15,23,42,0.06);
        overflow: hidden;
        max-width: 640px;
    }
    .show-card-header {
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        padding: 22px 26px;
        position: relative; overflow: hidden;
    }
    .show-card-header::before {
        content: ''; position: absolute;
        right: -30px; top: -30px;
        width: 120px; height: 120px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .show-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: rgba(255,255,255,0.18);
        border: 1px solid rgba(255,255,255,0.25);
        color: white; padding: 4px 10px;
        border-radius: 999px; font-size: 11.5px; font-weight: 600;
        margin-bottom: 10px;
    }
    .show-title { font-size: 20px; font-weight: 800; color: white; letter-spacing: -0.4px; line-height: 1.3; }
    .show-date { font-size: 12.5px; color: rgba(255,255,255,0.6); margin-top: 8px; display: flex; align-items: center; gap: 5px; }
    .show-body { padding: 28px 26px; }
    .show-content { font-size: 14.5px; color: #374151; line-height: 1.75; }
    .show-footer { padding: 16px 26px 22px; border-top: 1px solid #f1f5f9; }
</style>

<div class="show-back">
    <a href="{{ route('client.notifications') }}" class="btn btn-ghost btn-sm">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Notifications
    </a>
</div>

<div class="show-card">
    <div class="show-card-header">
        <div class="show-badge">
            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            Clinic Announcement
        </div>
        <div class="show-title">{{ $announcement->title ?? 'Announcement' }}</div>
        @if($announcement->sent_at)
        <div class="show-date">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ $announcement->sent_at->format('F j, Y · g:i A') }}
        </div>
        @endif
    </div>

    <div class="show-body">
        <div class="show-content">
            {!! nl2br(e($announcement->body)) !!}
        </div>
    </div>

    <div class="show-footer">
        <a href="{{ route('client.notifications') }}" class="btn btn-primary btn-sm">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Back to All Notifications
        </a>
    </div>
</div>
@endsection
