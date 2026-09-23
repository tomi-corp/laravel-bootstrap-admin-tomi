<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class TestSmtpCommand extends Command
{
    protected $signature = 'smtp:test
        {email : Alamat email tujuan untuk menerima email test}
        {--mailer=smtp : Mailer yang dipakai, default smtp}
        {--subject=SMTP Test TOMI : Subject email test}';

    protected $description = 'Kirim email test untuk memvalidasi konfigurasi SMTP tanpa menampilkan credential.';

    public function handle(): int
    {
        $to = (string) $this->argument('email');
        $mailer = (string) $this->option('mailer');
        $subject = (string) $this->option('subject');

        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->error('Alamat email tujuan tidak valid.');

            return self::FAILURE;
        }

        $this->info('Mengirim email test SMTP...');
        $this->line('Mailer     : '.$mailer);
        $this->line('Host       : '.(config("mail.mailers.{$mailer}.host") ?? '-'));
        $this->line('Port       : '.(config("mail.mailers.{$mailer}.port") ?? '-'));
        $this->line('Encryption : '.(config("mail.mailers.{$mailer}.encryption") ?? '-'));
        $this->line('From       : '.config('mail.from.address').' ('.config('mail.from.name').')');
        $this->line('To         : '.$to);

        try {
            Mail::mailer($mailer)->raw(
                "Email test SMTP TOMI berhasil dikirim.\n\nWaktu: ".now()->format('Y-m-d H:i:s'),
                function ($message) use ($to, $subject) {
                    $message->to($to)->subject($subject);
                }
            );
        } catch (Throwable $exception) {
            $this->error('Gagal mengirim email test SMTP.');
            $this->line($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Email test SMTP berhasil dikirim.');

        return self::SUCCESS;
    }
}
