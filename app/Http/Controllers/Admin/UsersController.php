<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Announcement;
use App\Models\Appointment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('uid', 'like', "%{$q}%");
        }

        $users = $query->orderBy('id')->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->back()->with('error', 'Cannot delete admin accounts.');
        }

        // Delete announcements belonging to this user and remove their attachments
        $announcements = Announcement::where('user_id', $user->id)->get();
        foreach ($announcements as $a) {
            if ($a->attachment) {
                try {
                    Storage::disk('public')->delete($a->attachment);
                } catch (\Exception $e) {
                    // ignore storage deletion errors but log if needed
                }
            }
            $a->delete();
        }

        // Do NOT delete appointments; allow the DB foreign key to nullify user_id (appointments preserved)
        // Finally delete the user
        $user->delete();

        return redirect()->back()->with('success', 'User deleted. Related announcements removed; appointments retained.');
    }

    /**
     * Show appointments for a specific user to the admin.
     */
    public function appointments(User $user)
    {
        $appointments = Appointment::where('user_id', $user->id)->orderByDesc('scheduled_at')->get();
        return view('admin.users.appointments', compact('user', 'appointments'));
    }

    /**
     * Mark a user's appointment as completed (admin action).
     */
    public function appointmentDone(User $user, Appointment $appointment)
    {
        if ($appointment->user_id !== $user->id) {
            return Redirect::back()->with('error', 'Appointment does not belong to this user.');
        }

        $appointment->status = 'completed';
        $appointment->save();

        return Redirect::back()->with('success', 'Appointment marked as done.');
    }
}
