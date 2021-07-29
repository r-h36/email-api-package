<?php

namespace Rh36\EmailApiPackage\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Rh36\EmailApiPackage\Models\EmailTemplate;
use Rh36\EmailApiPackage\Tests\BaseTestCase;
use Rh36\EmailApiPackage\Tests\User;

class EmailTemplateTest extends BaseTestCase
{
    use RefreshDatabase;

    /** @test */
    function an_email_template_has_a_name()
    {
        $name = 'My new template';

        $temp = EmailTemplate::factory()->create(['template_name' => $name]);
        $this->assertEquals($name, $temp->template_name);
    }

    /** @test */
    function an_email_template_has_a_template_body()
    {
        $blade = 'Hello, {{ $planet }}!';

        $temp = EmailTemplate::factory()->create(['template_body' => $blade]);
        $this->assertEquals($blade, $temp->template_body);
    }


    /** @test */
    function an_email_template_belongs_to_a_user()
    {
        $user = User::factory()->create();

        $user->emailTemplates()->create([
            'template_name' => 'My second template',
            'template_body' => 'Hello, {{ $planet }}!',
        ]);

        $this->assertCount(1, EmailTemplate::all());
        $this->assertCount(1, $user->emailTemplates);


        tap($user->emailTemplates()->first(), function ($emailTemplate) use ($user) {
            $this->assertEquals('My second template', $emailTemplate->template_name);
            $this->assertEquals('Hello, {{ $planet }}!', $emailTemplate->template_body);
            $this->assertTrue($emailTemplate->user->is($user));
        });
    }
}
