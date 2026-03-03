<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendAnnouncementTest extends Command
{
    protected $signature = 'announcement:send-test {email} {--subject=Test Announcement} {--body=This is a test from DentaQueue}';
    protected $description = 'Send a test announcement email and create an in-app Announcement record for the recipient';

    public function handle()
    {
        $to = $this->argument('email');
        $subject = $this->option('subject');
        $body = $this->option('body');

        $adminName = auth()->user()->name ?? config('mail.from.name', 'Admin');
        $from = config('mail.from.address', auth()->user()->email ?? null);

        $this->info("Sending announcement to: {$to}");

        $testAnnouncement = Announcement::make([
            'title' => $subject,
            'body' => $body,
            'attachment' => null,
        ]);

        try {
            Mail::to($to)->send(new \App\Mail\AnnouncementMail($testAnnouncement, $from, $adminName));

            // persist announcement record
            $recipientUser = User::where('email', $to)->first();
            if ($recipientUser) {
                $rec = Announcement::create([
                    'user_id' => $recipientUser->id,
                    'title' => $subject,
                    'body' => $body,
                    'sent_at' => now(),
                ]);
            } else {
                $rec = Announcement::create([
                    'user_id' => null,
                    'recipient_email' => $to,
                    'title' => $subject,
                    'body' => $body,
                    'sent_at' => now(),
                ]);
            }

            $this->info('Mail sent (or written to log) and announcement record created with id: ' . $rec->id);
            return 0;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Test announcement send failed', ['error' => $e->getMessage()]);
            $this->error('Send failed: ' . $e->getMessage());
            // still create DB record so recipient sees it in-app
            $rec = Announcement::create([
                'user_id' => null,
                'recipient_email' => $to,
                'title' => $subject,
                'body' => $body,
                'sent_at' => now(),
            ]);
            $this->info('Announcement persisted to DB with id: ' . $rec->id);
            return 2;
        }
    }
}
