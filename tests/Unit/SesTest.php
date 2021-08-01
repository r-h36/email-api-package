<?php

namespace Rh36\EmailApiPackage\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Rh36\EmailApiPackage\Tests\BaseTestCase;
use Aws\Ses\SesClient;
use Rh36\EmailApiPackage\Services\SesService;
use Rh36\EmailApiPackage\Models\EmailLog;

class SesTest extends BaseTestCase
{
    use RefreshDatabase, WithoutEvents;

    /** @test */
    function an_email_can_be_delivered_by_ses()
    {
        $sesClient = new SesClient([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
        $sesService = new SesService($sesClient);

        $emailLog = EmailLog::factory()->create([
            'from' => env('TEST_EMAIL'),
            'to' => env('TEST_EMAIL'),
            'cc' => null,
            'bcc' => null,
            'replyto' => null,
        ]);

        $response = $sesService->deliver($emailLog);

        $this->assertEquals(200, $response['@metadata']['statusCode']);
    }
}
