<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Announcement;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);

        $monthly = DB::table('appointments')
            ->select(DB::raw("MONTH(scheduled_at) as month"), DB::raw('count(*) as total'))
            ->whereYear('scheduled_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $byType = DB::table('appointments')
            ->select('type', DB::raw('count(*) as total'))
            ->whereYear('scheduled_at', $year)
            ->groupBy('type')
            ->get();

        $reminders = DB::table('announcements')
            ->select(DB::raw("MONTH(sent_at) as month"), DB::raw('count(*) as sent'))
            ->whereNotNull('sent_at')
            ->whereYear('sent_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // No-show stats
        $noShows = DB::table('appointments')
            ->select(DB::raw("MONTH(scheduled_at) as month"), DB::raw('count(*) as total'))
            ->whereYear('scheduled_at', $year)
            ->where('status', 'no_show')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // No-shows where there was at least one announcement sent before scheduled time
        $noShowsWithReminder = DB::table('appointments as a')
            ->select(DB::raw("MONTH(a.scheduled_at) as month"), DB::raw('count(distinct a.id) as total'))
            ->join('announcements as n', function($join) {
                $join->on('n.appointment_id', '=', 'a.id')
                     ->whereNotNull('n.sent_at')
                     ->whereColumn('n.sent_at','<','a.scheduled_at');
            })
            ->whereYear('a.scheduled_at', $year)
            ->where('a.status', 'no_show')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        return view('admin.reports.index', compact('year','monthly','byType','reminders','noShows','noShowsWithReminder'));
    }

    public function exportCsv(Request $request)
    {
        $type = $request->get('type', 'appointments');
        $year = $request->get('year', Carbon::now()->year);

        if ($type === 'appointments') {
            $rows = DB::table('appointments')->whereYear('scheduled_at', $year)->get();
            $filename = "appointments_{$year}.csv";
        } else {
            $rows = Announcement::whereYear('created_at', $year)->get();
            $filename = "announcements_{$year}.csv";
        }

        $csv = [];
        if ($rows->count()) {
            $columns = array_keys((array)$rows->first());
            $csv[] = implode(',', $columns);
            foreach ($rows as $r) {
                $line = [];
                foreach ($columns as $c) $line[] = '"' . str_replace('"','""', data_get($r,$c)) . '"';
                $csv[] = implode(',', $line);
            }
        }

        $content = implode("\n", $csv);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    // PDF export stub (can be implemented with dompdf or snappy later)
    public function exportPdf(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);

        // Prepare the same data as index
        $request->merge(['year'=>$year]);
        $resp = $this->index($request);

        // collect data for view
        $monthly = DB::table('appointments')
            ->select(DB::raw("MONTH(scheduled_at) as month"), DB::raw('count(*) as total'))
            ->whereYear('scheduled_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $byType = DB::table('appointments')
            ->select('type', DB::raw('count(*) as total'))
            ->whereYear('scheduled_at', $year)
            ->groupBy('type')
            ->get();

        $reminders = DB::table('announcements')
            ->select(DB::raw("MONTH(sent_at) as month"), DB::raw('count(*) as sent'))
            ->whereNotNull('sent_at')
            ->whereYear('sent_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $noShows = DB::table('appointments')
            ->select(DB::raw("MONTH(scheduled_at) as month"), DB::raw('count(*) as total'))
            ->whereYear('scheduled_at', $year)
            ->where('status', 'no_show')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // include full appointment and announcement lists for the PDF
        $appointments = DB::table('appointments')->whereYear('scheduled_at', $year)->orderBy('scheduled_at')->get();
        $announcements = Announcement::whereYear('created_at', $year)->orderBy('created_at')->get();

        $data = compact('year','monthly','byType','reminders','noShows','appointments','announcements');

        if (class_exists('\\Barryvdh\\DomPDF\\Facade\\Pdf')) {
            $pdf = \PDF::loadView('admin.reports.pdf', $data);
            return $pdf->download("reports_{$year}.pdf");
        }

        return view('admin.reports.pdf', $data);
    }
}
