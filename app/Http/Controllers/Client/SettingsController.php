<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminder;
use App\Models\Appointment;

class SettingsController extends Controller
{
    public function index()
    {
        return view('client.settings.index');
    }

    public function updateProfile(Request $request)
    {
        // Conditional validation: allow avatar-only uploads or name/email updates
        $rules = [];
        if ($request->has('name') || $request->has('email')) {
            $rules['name'] = 'required';
            $rules['email'] = 'required|email';
        }
        if ($request->hasFile('avatar')) {
            $rules['avatar'] = 'image|max:2048';
        }

        if (!empty($rules)) {
            $request->validate($rules);
        }

        $user = Auth::user();
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars','public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->back()->with('success','Profile updated.');
    }

    public function sendTestEmail(Request $request)
    {
        $user = Auth::user();

        // Try to find an upcoming appointment to include in the test mail
        $appointment = Appointment::where('user_id', $user->id)->whereNotNull('scheduled_at')->orderBy('scheduled_at')->first();

        if (! $appointment) {
            // create a lightweight Appointment instance (not saved) for testing
            $appointment = new Appointment([
                'fullname' => $user->name,
                'type' => 'Check-up',
                'scheduled_at' => now()->addDays(2),
            ]);
        }

        try {
            Mail::to($user->email)->send(new AppointmentReminder($appointment));
            return redirect()->back()->with('success', 'Test email sent to ' . $user->email);
        } catch (\Exception $e) {
            return redirect()->back()->with('success', 'Could not send email: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Password updated.');
    }
}
