<?php

namespace Tests\Unit;

use App\Notifications\SystemNotification;
use Tests\TestCase;

class SystemNotificationTest extends TestCase
{
    public function test_via_returns_only_database_by_default(): void
    {
        $notification = new SystemNotification(title: 'Título', message: 'Mensaje');

        $this->assertSame(['database'], $notification->via((object) []));
    }

    public function test_via_includes_mail_when_send_email_is_true(): void
    {
        $notification = new SystemNotification(title: 'Título', message: 'Mensaje', sendEmail: true);

        $this->assertSame(['database', 'mail'], $notification->via((object) []));
    }

    public function test_to_mail_includes_action_when_url_is_set(): void
    {
        $notification = new SystemNotification(
            title: 'Título',
            message: 'Mensaje',
            url: 'https://example.test/ver',
            sendEmail: true,
        );

        $mail = $notification->toMail((object) []);

        $this->assertStringContainsString('https://example.test/ver', $mail->actionUrl);
    }
}
