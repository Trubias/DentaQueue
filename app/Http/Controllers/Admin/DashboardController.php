<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $today_appointments = Appointment::whereDate('created_at', $today)->count();
        $daily_clients = Appointment::whereDate('created_at', $today)->distinct('user_id')->count('user_id');
        $queue_length = Appointment::where('status', 'pending')->count();

        // Show only appointments that are assigned and not completed/missed
        $upcoming = Appointment::whereNotNull('scheduled_at')
            ->where('scheduled_at', '>=', now())
            ->where('status', 'assigned')
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $recent_pending = Appointment::where('status', 'pending')->orderBy('created_at')->limit(10)->get();

        $stats = [
            'today_appointments' => $today_appointments,
            'daily_clients' => $daily_clients,
            'queue_length' => $queue_length,
        ];

        return view('admin.dashboard', compact('stats', 'upcoming', 'recent_pending'));
    }
}
