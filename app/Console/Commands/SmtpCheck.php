<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SmtpCheck extends Command
{
    protected $signature = 'smtp:check';
    protected $description = 'Check SMTP connection using current config';

    public function handle()
    {
        $smtp = config('mail.mailers.smtp', []);

        $host = $smtp['host'] ?? env('MAIL_HOST');
        $port = $smtp['port'] ?? env('MAIL_PORT');
        $encryption = $smtp['encryption'] ?? env('MAIL_ENCRYPTION');
        $username = $smtp['username'] ?? env('MAIL_USERNAME');
        $password = $smtp['password'] ?? env('MAIL_PASSWORD');

        $this->info("Using host={$host}, port={$port}, encryption={$encryption}, username={$username}");

        try {
            $transport = new \Swift_SmtpTransport($host, (int) $port);
            if ($encryption) $transport->setEncryption($encryption);
            if ($username) $transport->setUsername($username);
            if ($password) $transport->setPassword($password);

            $transport->start();
            $transport->stop();

            $this->info('SMTP connection and authentication succeeded.');
            return 0;
        } catch (\Exception $e) {
            $this->error('SMTP check failed: ' . $e->getMessage());
            return 2;
        }
    }
}
