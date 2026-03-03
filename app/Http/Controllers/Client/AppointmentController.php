<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $appointments = Appointment::where('user_id', $user->id)->orderByDesc('created_at')->get();

        return view('client.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $user = Auth::user();
        $active = Appointment::where('user_id', $user->id)->whereIn('status', ['pending', 'assigned'])->first();
        return view('client.book', compact('active'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:1'],
            'sex' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:255'],
        ]);

        $user = Auth::user();

        // Prevent multiple active bookings per client (pending or assigned)
        $hasActive = Appointment::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'assigned'])
            ->exists();

        if ($hasActive) {
            return redirect()->back()->with('error', 'You already have an active booking. Please edit or delete it before creating a new one.');
        }

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'fullname' => $request->fullname,
            'age' => $request->age,
            'sex' => $request->sex,
            'type' => $request->type,
            'status' => 'pending',
        ]);

        return redirect()->route('client.dashboard')->with('success', 'Your appointment request has been queued. Your ID: ' . sprintf('%03d', $appointment->id));
    }

    /**
     * Show edit form for a client's appointment.
     */
    public function edit(Appointment $appointment)
    {
        $user = Auth::user();
        if ($appointment->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        return view('client.appointments.edit', compact('appointment'));
    }

    /**
     * Update a client's appointment (allow small edits before assignment).
     */
    public function update(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        if ($appointment->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:1'],
            'sex' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:255'],
        ]);

        // Only allow editing if appointment is not completed
        if (in_array($appointment->status, ['completed'])) {
            return redirect()->back()->with('error', 'Completed appointments cannot be edited.');
        }

        $appointment->update($request->only(['fullname','age','sex','type']));

        return redirect()->route('client.dashboard')->with('success', 'Appointment updated.');
    }

    /**
     * Destroy an appointment created by the client.
     */
    public function destroy(Appointment $appointment)
    {
        $user = Auth::user();
        if ($appointment->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        try {
            $appointment->delete();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete appointment.');
        }

        return redirect()->route('client.dashboard')->with('success', 'You have been cancel your book Appointment');
    }

    /**
     * Mark appointment as done/completed by the client.
     */
    public function done(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        if ($appointment->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $appointment->status = 'completed';
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment marked as done.');
    }
}
