<?php

namespace Rh36\EmailApiPackage\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Rh36\EmailApiPackage\Models\EmailLog;
use Rh36\EmailApiPackage\Tests\Unit\BaseTestCase;
use Rh36\EmailApiPackage\Tests\User;

class EmailLogTest extends BaseTestCase
{
    use RefreshDatabase;

    /** @test */
    function an_email_log_has_a_subject()
    {
        $emailLog = EmailLog::factory()->create(['subject' => 'New Email Subject']);
        $this->assertEquals('New Email Subject', $emailLog->subject);
    }

    /** @test */
    function an_email_log_has_a_from_email_address()
    {
        $emailLog = EmailLog::factory()->create(['from' => 'testfrom@outlook.com']);
        $this->assertEquals('testfrom@outlook.com', $emailLog->from);
    }

    /** @test */
    function an_email_log_has_a_to_email_address()
    {
        $emailLog = EmailLog::factory()->create(['to' => 'testto@outlook.com']);
        $this->assertEquals('testto@outlook.com', $emailLog->to);
    }


    /** @test */
    function an_email_log_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $plainContent = 'This is a test email with plain text content without template';

        $user->emailLogs()->create([
            'subject' => 'Second email subject',
            'template_id' => null,
            'template_data' => null,
            'use_template' => 0,
            'plain_content' => $plainContent,
            'from' => 'thisisfrom@gmail.com',
            'to' => 'thisisto@yahoo.com',
        ]);

        $this->assertCount(1, EmailLog::all());
        $this->assertCount(1, $user->emailLogs);


        tap($user->emailLogs()->first(), function ($emailLog) use ($user, $plainContent) {
            $this->assertEquals('Second email subject', $emailLog->subject);
            $this->assertNull($emailLog->template_id);
            $this->assertNull($emailLog->template_data);
            $this->assertEquals(0, $emailLog->use_template);
            $this->assertEquals($plainContent, $emailLog->plain_content);
            $this->assertTrue($emailLog->user->is($user));
        });
    }

    /** @test */
    function an_email_log_can_use_a_template()
    {
        $user = User::factory()->create();

        $user->emailTemplates()->create([
            'template_name' => 'My third template',
            'template_body' => 'Hello, {{ $planet }}!',
        ]);

        $firstTemplate = $user->emailTemplates()->first();

        $user->emailLogs()->create([
            'subject' => 'Third email subject',
            'template_id' => $firstTemplate->id,
            'template_data' => json_encode(['planet' => 'Sanctum']),
            'use_template' => 1,
            'plain_content' => null,
            'from' => 'thisisfrom@gmail.com',
            'to' => 'thisisto@yahoo.com',
        ]);

        tap($user->emailLogs()->first(), function ($emailLog) use ($user, $firstTemplate) {
            $this->assertEquals('Third email subject', $emailLog->subject);
            $this->assertEquals($firstTemplate->id, $emailLog->template_id);
            $this->assertEquals(json_encode(['planet' => 'Sanctum']), $emailLog->template_data);
            $this->assertEquals(1, $emailLog->use_template);
            $this->assertNull($emailLog->plain_content);
            $this->assertTrue($emailLog->user->is($user));
        });
    }
}
