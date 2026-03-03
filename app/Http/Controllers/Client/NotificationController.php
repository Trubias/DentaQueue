<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Only return UNREAD announcements targeted to this user (clear will hide them)
        $announcements = Announcement::where('user_id', $user->id)->where('read', false)->orderByDesc('sent_at')->get();

        return view('client.notifications.index', compact('announcements'));
    }

    public function show(Announcement $announcement)
    {
        $user = Auth::user();
        // Only allow viewing announcements targeted to this user
        if ($announcement->user_id !== $user->id) {
            abort(403, 'Not authorized to view this announcement.');
        }

        // mark as read for this user (note: per-record field)
        if (! $announcement->read) {
            $announcement->read = true;
            $announcement->save();
        }

        return view('client.notifications.show', compact('announcement'));
    }

    /**
     * Mark all announcements for the current user as read.
     */
    public function clear(Request $request)
    {
        $user = Auth::user();
        Announcement::where('user_id', $user->id)->where('read', false)->update(['read' => true]);
        return redirect()->back()->with('success', 'Notifications cleared.');
    }
}
