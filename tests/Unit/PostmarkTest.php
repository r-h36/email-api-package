<?php

namespace Rh36\EmailApiPackage\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Rh36\EmailApiPackage\Tests\Unit\BaseTestCase;
use Rh36\EmailApiPackage\Services\PostmarkService;
use Postmark\PostmarkClient;
use Rh36\EmailApiPackage\Models\EmailLog;

class PostmarkTest extends BaseTestCase
{
    use RefreshDatabase;

    /** @test */
    function an_email_body_can_be_generated()
    {
        $client = new PostmarkClient('');
        $postMark = new PostmarkService($client);

        $emailLog = EmailLog::factory()->create([
            'template_data' => json_encode(['planet' => 'John'])
        ]);

        $bodyArr = $postMark->composeBody($emailLog);

        $this->assertIsArray($bodyArr);
        $this->assertArrayHasKey('HtmlBody', $bodyArr);
        $this->assertArrayHasKey('TextBody', $bodyArr);

        $this->assertStringContainsString('John', $bodyArr['HtmlBody']);
        $this->assertStringContainsString('Hello, John', $bodyArr['TextBody']);
    }

    /** @test */
    function an_email_can_be_delivered_by_postmark()
    {

        $client = new PostmarkClient(env('POSTMARK_TOKEN'));
        $postMark = new PostmarkService($client);

        $emailLog = EmailLog::factory()->create([
            'from' => 'ray@engaging.io',
            'to' => 'ray@engaging.io',
            'cc' => null,
            'bcc' => null,
            'replyto' => null,
        ]);

        $response = $postMark->deliver($emailLog);

        $this->assertEquals('OK', $response->message);
    }
}
