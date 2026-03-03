<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;

use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        // Placeholder: calendar integration will go here (FullCalendar etc.)
        return view('admin.schedule.index');
    }

    public function events(Request $request)
    {
        $appointments = Appointment::whereNotNull('scheduled_at')->get();

        $events = $appointments->map(function ($a) {
            return [
                'id' => $a->id,
                'title' => sprintf('#%03d — %s', $a->id, $a->fullname),
                'start' => $a->scheduled_at ? $a->scheduled_at->toIso8601String() : null,
                'allDay' => false,
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'type' => 'nullable|string',
        ]);

        $dt = \Carbon\Carbon::parse($request->start);
        // Block Sundays
        if ($dt->isSunday()) {
            return response()->json(['error' => 'Sundays are unavailable'], 422);
        }

        // Business hours: 09:00 - 17:00
        $hour = (int) $dt->format('H');
        if ($hour < 9 || $hour >= 18) {
            return response()->json(['error' => 'Outside business hours (09:00-17:59)'], 422);
        }

        $appointment = Appointment::create([
            'fullname' => $request->title,
            'type' => $request->type ?? 'General',
            'status' => 'assigned',
            'scheduled_at' => $dt,
        ]);

        return response()->json(['id' => $appointment->id]);
    }

    public function updateEvent(Request $request, Appointment $appointment)
    {
        $request->validate(['start' => 'required|date']);
        $dt = \Carbon\Carbon::parse($request->start);

        if ($dt->isSunday()) {
            return response()->json(['error' => 'Sundays are unavailable'], 422);
        }

        $hour = (int) $dt->format('H');
        if ($hour < 9 || $hour >= 18) {
            return response()->json(['error' => 'Outside business hours (09:00-17:59)'], 422);
        }

        $appointment->scheduled_at = $dt;
        $appointment->status = 'assigned';
        $appointment->save();

        return response()->json(['success' => true]);
    }

    public function destroyEvent(Appointment $appointment)
    {
        $appointment->status = 'cancelled';
        $appointment->save();

        return response()->json(['success' => true]);
    }
}
