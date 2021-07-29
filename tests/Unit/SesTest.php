<?php

namespace Rh36\EmailApiPackage\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Rh36\EmailApiPackage\Tests\BaseTestCase;
use Aws\Ses\SesClient;
use Rh36\EmailApiPackage\Services\SesService;
use Rh36\EmailApiPackage\Models\EmailLog;

class SesTest extends BaseTestCase
{
    use RefreshDatabase;

    /** @test */
    function an_email_can_be_delivered_by_ses()
    {
        $SesClient = new SesClient([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
        $sesService = new SesService($SesClient);

        $emailLog = EmailLog::factory()->create([
            'from' => 'ray@engaging.io',
            'to' => 'ray@engaging.io',
            'cc' => null,
            'bcc' => null,
            'replyto' => null,
        ]);

        $response = $sesService->deliver($emailLog);

        $this->assertEquals(200, $response['@metadata']['statusCode']);
    }
}
