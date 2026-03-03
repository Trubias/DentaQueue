<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Mail\AppointmentAssigned;

class AnnouncementController extends Controller
{
    public function index()
    {
        // only show unsent announcements in the main admin list
        $items = Announcement::with('user','appointment')->whereNull('sent_at')->orderByDesc('created_at')->get();
        return view('admin.announcements.index', compact('items'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'user_id' => 'nullable|exists:users,id',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,txt,zip|max:5120',
        ]);

        $data = [
            'user_id' => $request->user_id,
            'title' => $request->title,
            'body' => $request->body,
        ];

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('announcements','public');
            $data['attachment'] = $path;
        }

        $announcement = Announcement::create($data);

        // If a single recipient was selected, auto-send the announcement and mark it as sent
        if ($announcement->user_id) {
            $recipient = User::find($announcement->user_id);
            if ($recipient && $recipient->email) {
                try {
                    $from = config('mail.from.address', auth()->user()->email ?? null);
                    $fromName = config('mail.from.name', auth()->user()->name ?? null);

                    // send email
                    \Mail::to($recipient->email)->send(new \App\Mail\AnnouncementMail($announcement, $from, $fromName));

                    // mark as sent so it appears in inventory
                    $announcement->sent_at = now();
                    $announcement->save();
                } catch (\Exception $e) {
                    // log the error and keep the announcement saved (not sent)
                    Log::error('Announcement auto-send failed', ['id' => $announcement->id, 'error' => $e->getMessage()]);
                    session()->flash('error', 'Saved but failed to send email: ' . $e->getMessage());
                    return redirect()->route('admin.announcements.index');
                }
            }
        }

        session()->flash('status', 'Announcement saved.');
        return redirect()->route('admin.announcements.index');
    }

    public function send(Request $request, Announcement $announcement)
    {
        $user = $announcement->user;
        if (!$user || !$user->email) {
            return redirect()->back()->with('error', 'No recipient email.');
        }

        // Allow admin to override message and upload a different attachment when sending
        $body = $request->input('body', $announcement->body);
        $attachmentPath = $announcement->attachment;

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('announcements','public');
            $attachmentPath = $path;
        }

        // Update DB record so client will see the announcement in their inbox even if mail delivery fails
        $announcement->body = $body;
        if ($attachmentPath && $attachmentPath !== $announcement->attachment) {
            $announcement->attachment = $attachmentPath;
        }
        // mark as sent in-app (so inbox shows it)
        $announcement->sent_at = now();
        $announcement->save();

        // Use a temporary announcement clone for the mail content
        $temp = clone $announcement;

        $emailSent = false;
        $emailError = null;

        try {
            $from = config('mail.from.address', auth()->user()->email ?? null);
            $fromName = config('mail.from.name', auth()->user()->name ?? null);

            // attempt to send email
            \Mail::to($user->email)->send(new \App\Mail\AnnouncementMail($temp, $from, $fromName));
            $emailSent = true;
        } catch (\Exception $e) {
            $emailErrorFull = $e->getMessage();
            // Log full error server-side for debugging
            Log::error('Announcement email send failed', ['announcement_id' => $announcement->id, 'error' => $emailErrorFull]);

            // Friendly error for UI
            if (stripos($emailErrorFull, 'getaddrinfo') !== false || stripos($emailErrorFull, 'Connection could not be established') !== false || stripos($emailErrorFull, 'unable to connect') !== false) {
                $emailError = 'SMTP host unreachable. Check MAIL_HOST and ensure your mail server is running.';
            } else {
                $emailError = 'Email delivery failed. See server logs for details.';
            }

            // Fallback: write the email to the log so the message is preserved for debugging/dev
            try {
                \Mail::mailer('log')->to($user->email)->send(new \App\Mail\AnnouncementMail($temp, $from ?? null, $fromName ?? null));
                Log::info('Announcement email written to log as fallback', ['announcement_id' => $announcement->id]);
            } catch (\Exception $e2) {
                Log::error('Fallback log mailer failed', ['error' => $e2->getMessage()]);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            if ($emailSent) {
                return response()->json(['success' => true, 'message' => 'Announcement saved and email sent.', 'email_sent' => true]);
            }
            return response()->json(['success' => true, 'message' => 'Announcement saved to inbox. Email delivery failed: ' . $emailError, 'email_sent' => false, 'email_error' => $emailError]);
        }

        // For non-AJAX requests always redirect to Inventory so sent items leave the main list
        return redirect()->route('admin.announcements.inventory')->with($emailSent ? 'success' : 'status', $emailSent ? 'Announcement sent.' : 'Saved to inbox but failed to send email: ' . $emailError);
    }

    /**
     * Send a quick test email to the current admin to verify mail settings.
     * Returns JSON when called via AJAX.
     */
    public function mailTest(Request $request)
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->email) {
            return response()->json(['success' => false, 'message' => 'No admin email available'], 400);
        }
        $to = $request->input('to', $admin->email);
        $body = $request->input('body', 'This is a test email from DentaQueue to verify your mail configuration.');
        $subject = $request->input('subject', 'Message from Admin');

        $from = $admin->email;
        $fromName = $admin->name ?? 'Admin';

        try {
            // Build a non-persisted Announcement model instance so AnnouncementMail receives the correct type
            $testAnnouncement = Announcement::make([
                'title' => $subject,
                'body' => $body,
                'attachment' => null,
            ]);

            \Mail::to($to)->send(new \App\Mail\AnnouncementMail($testAnnouncement, $from, $fromName));

            // Persist announcement for recipient: if matched to a user attach user_id,
            // otherwise save a record with recipient_email so admin/inventory can track it.
            $recipientUser = User::where('email', $to)->first();
            if ($recipientUser) {
                Announcement::create([
                    'user_id' => $recipientUser->id,
                    'title' => $subject,
                    'body' => $body,
                    'sent_at' => now(),
                ]);
            } else {
                Announcement::create([
                    'user_id' => null,
                    'recipient_email' => $to,
                    'title' => $subject,
                    'body' => $body,
                    'sent_at' => now(),
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Email dispatched (or written to log).']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail test failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Mail send failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Inventory view: show all sent announcements grouped by year.
     */
    public function inventory(Request $request)
    {
        $items = Announcement::with('user','appointment')
            ->whereNotNull('sent_at')
            ->orderByDesc('sent_at')
            ->get()
            ->groupBy(function($a){ return $a->sent_at ? $a->sent_at->format('Y') : 'Unknown'; });

        return view('admin.announcements.inventory', ['groups' => $items]);
    }

    /**
     * Export appointments CSV for a given year (defaults to current year).
     */
    public function exportAppointments(Request $request)
    {
        $year = $request->query('year', now()->year);

        $appointments = \App\Models\Appointment::whereYear('scheduled_at', $year)->orderBy('scheduled_at')->get();

        $filename = "appointments_{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($appointments) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','User ID','Fullname','Age','Sex','Type','Status','Scheduled At','Note','Created At']);
            foreach ($appointments as $a) {
                fputcsv($out, [
                    $a->id,
                    $a->user_id,
                    $a->fullname,
                    $a->age,
                    $a->sex,
                    $a->type,
                    $a->status,
                    $a->scheduled_at ? $a->scheduled_at->toDateTimeString() : '',
                    $a->note,
                    $a->created_at ? $a->created_at->toDateTimeString() : '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete an announcement (admin).
     */
    public function destroy(Announcement $announcement)
    {
        // remove attachment file if present
        if ($announcement->attachment) {
            try {
                Storage::disk('public')->delete($announcement->attachment);
            } catch (\Exception $e) {
                Log::warning('Failed to remove announcement attachment', ['id' => $announcement->id, 'error' => $e->getMessage()]);
            }
        }

        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted.');
    }
}
