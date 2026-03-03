<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Auto-mark past scheduled assigned appointments as 'missed' so they don't appear as upcoming
        Appointment::where('user_id', $user->id)
            ->where('status', 'assigned')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<', now())
            ->update(['status' => 'missed']);

        // Next pending or assigned appointment for this user (exclude missed/completed)
        $next = Appointment::where('user_id', $user->id)
            ->whereIn('status', ['pending','assigned'])
            ->orderBy('created_at')
            ->first();

        // Queue position (if pending)
        $position = null;
        if ($next && $next->status === 'pending') {
            $position = Appointment::where('status', 'pending')
                ->where('created_at', '<=', $next->created_at)
                ->count();
        }

        $appointments = Appointment::where('user_id', $user->id)->orderByDesc('created_at')->get();

        // Only count announcements targeted to this user
        $unreadAnnouncements = Announcement::where('user_id', $user->id)->where('read', false)->count();

        // Recent announcements targeted to this user
        $announcements = Announcement::where('user_id', $user->id)->whereNotNull('sent_at')->orderByDesc('sent_at')->limit(3)->get();

        return view('client.dashboard', compact('user', 'next', 'position', 'appointments', 'unreadAnnouncements', 'announcements'));
    }
}
