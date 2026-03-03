<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentAssigned;

class QueueController extends Controller
{
    public function index()
    {
        // By default show only pending (client queue). To see all include ?all=1
        $query = Appointment::orderBy('created_at');
        if (!request()->has('all')) {
            $query->where('status', 'pending');
        }

        $appointments = $query->get();

        return view('admin.queue.index', compact('appointments'));
    }

    public function show(Appointment $appointment)
    {
        return view('admin.queue.show', compact('appointment'));
    }

    public function assign(Request $request, Appointment $appointment)
    {
        $request->validate([
            'scheduled_at' => ['required', 'date'],
        ]);

        $appointment->status = 'assigned';
        $appointment->scheduled_at = $request->scheduled_at;
        $appointment->save();

        // Create an announcement linked to this appointment so admin can review/send
        if ($appointment->user_id) {
            $announcement = \App\Models\Announcement::create([
                'user_id' => $appointment->user_id,
                'appointment_id' => $appointment->id,
                'title' => 'Your appointment has been assigned',
                'body' => 'Your appointment for ' . ($appointment->type ?? 'service') . ' has been assigned to ' . $appointment->scheduled_at,
            ]);

            // attempt to email the user immediately
            try {
                $user = $appointment->user;
                if ($user && $user->email) {
                    $from = config('mail.from.address', null);
                    $fromName = config('mail.from.name', null);
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AnnouncementMail($announcement, $from, $fromName));
                    $announcement->sent_at = now();
                    $announcement->save();
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to email assigned appointment', ['appointment_id' => $appointment->id, 'error' => $e->getMessage()]);
            }
        }

        return redirect()->route('admin.queue.index')->with('success', 'Appointment assigned.');
    }

    public function requeue(Appointment $appointment)
    {
        $appointment->status = 'pending';
        $appointment->updated_at = now();
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment requeued.');
    }

    /**
     * Delete an appointment from the queue and notify the user.
     */
    public function destroy(Appointment $appointment)
    {
        // Create an in-app announcement for the user if available
        if ($appointment->user_id) {
            try {
                \App\Models\Announcement::create([
                    'user_id' => $appointment->user_id,
                    'appointment_id' => $appointment->id,
                    'title' => 'Your appointment was deleted',
                    'body' => 'Your appointment for ' . ($appointment->type ?? 'service') . ' (#' . sprintf('%03d', $appointment->id) . ") has been deleted by the clinic.",
                    'sent_at' => now(),
                    'read' => false,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to create deletion announcement', ['appointment_id' => $appointment->id, 'error' => $e->getMessage()]);
            }
        }

        // Delete the appointment record
        try {
            $appointment->delete();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete appointment: ' . $e->getMessage());
        }

        return redirect()->route('admin.queue.index')->with('success', 'Appointment deleted and user notified.');
    }

    public function remind(Appointment $appointment)
    {
        if ($appointment->user && $appointment->user->email) {
            \Illuminate\Support\Facades\Mail::to($appointment->user->email)->send(new \App\Mail\AppointmentReminder($appointment));
            return redirect()->back()->with('success', 'Reminder sent.');
        }

        return redirect()->back()->with('error', 'No email found for this appointment.');
    }
}
