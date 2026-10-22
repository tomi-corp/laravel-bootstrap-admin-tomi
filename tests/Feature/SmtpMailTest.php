<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SmtpMailTest extends TestCase
{
    public function test_smtp_mailer_configuration_is_available(): void
    {
        $smtp = config('mail.mailers.smtp');

        $this->assertIsArray($smtp);
        $this->assertSame('smtp', $smtp['transport']);
        $this->assertNotEmpty($smtp['host']);
        $this->assertIsNumeric($smtp['port']);
        $this->assertGreaterThan(0, (int) $smtp['port']);
        $this->assertContains($smtp['encryption'], [null, '', 'tls', 'ssl']);
    }

    public function test_smtp_integration_can_send_a_probe_email_when_enabled(): void
    {
        if (filter_var(env('SMTP_INTEGRATION_TEST', false), FILTER_VALIDATE_BOOLEAN) !== true) {
            $this->markTestSkipped('Set SMTP_INTEGRATION_TEST=true to run the live SMTP test.');
        }

        $recipient = env('SMTP_TEST_TO');

        $this->assertNotEmpty(
            $recipient,
            'SMTP_TEST_TO must contain the recipient address for the live SMTP test.'
        );
        $this->assertNotFalse(
            filter_var($recipient, FILTER_VALIDATE_EMAIL),
            'SMTP_TEST_TO must contain a valid email address.'
        );

        Mail::mailer('smtp')->raw(
            'SMTP integration test from '.config('app.name'),
            function ($message) use ($recipient): void {
                $message->to($recipient)->subject('SMTP integration test');
            }
        );

        $this->addToAssertionCount(1);
    }
}
