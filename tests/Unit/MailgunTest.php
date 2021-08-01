<?php

namespace Rh36\EmailApiPackage\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Rh36\EmailApiPackage\Tests\BaseTestCase;
use Mailgun\Mailgun;
use Rh36\EmailApiPackage\Services\MailgunService;
use Rh36\EmailApiPackage\Models\EmailLog;

class MailgunTest extends BaseTestCase
{
    use RefreshDatabase, WithoutEvents;

    /** @test */
    function an_email_can_be_delivered_by_mailgun()
    {
        $mg = Mailgun::create(env('MAILGUN_SECRET'));
        $mgservice = new MailgunService($mg);

        $emailLog = EmailLog::factory()->create([
            'from' => env('TEST_EMAIL'),
            'to' => env('TEST_EMAIL'),
            'cc' => null,
            'bcc' => null,
            'replyto' => null,
        ]);

        $response = $mgservice->deliver($emailLog);
        $this->assertEquals('Queued. Thank you.', $response->getMessage());
    }
}
