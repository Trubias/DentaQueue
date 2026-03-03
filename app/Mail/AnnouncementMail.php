<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Announcement;

class AnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $announcement;
    public $fromEmail;
    public $fromName;

    public function __construct(Announcement $announcement, $fromEmail = null, $fromName = null)
    {
        $this->announcement = $announcement;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public function build()
    {
        $mail = $this->subject($this->announcement->title)
                     ->view('emails.announcement')
                     ->with(['announcement' => $this->announcement]);

        if ($this->fromEmail) {
            $mail->from($this->fromEmail, $this->fromName ?: null);
        }

        if ($this->announcement->attachment) {
            $mail->attach(storage_path('app/public/' . $this->announcement->attachment));
        }

        return $mail;
    }
}
