<?php

namespace Rh36\EmailApiPackage\Tests\Feature;

use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Rh36\EmailApiPackage\Models\EmailLog;
use Rh36\EmailApiPackage\Models\EmailTemplate;
use Rh36\EmailApiPackage\Tests\BaseTestCase;
use Rh36\EmailApiPackage\Tests\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Bus;
use Rh36\EmailApiPackage\Events\EmailWasCreated;
use Rh36\EmailApiPackage\Jobs\SendByPostmark;

use Faker;

class CreateEmailLogRouteTest extends BaseTestCase
{
    use RefreshDatabase;

    /** @test */
    function guests_can_not_create_emails()
    {
        // We're starting from an unauthenticated state
        $this->assertFalse(auth()->check());

        $user = User::factory()->create();
        $faker = Faker\Factory::create();

        $from = $faker->email;
        $to = $faker->email;
        $subject = implode(' ', $faker->words);
        $payload = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'use_template' => 0,
            'plain_content' => 'The is a plain content email',
            'template_id' => null,
            'template_data' => null,
        ];

        $this->postJson(route('emails.store'), $payload)
            ->assertForbidden();
    }

    /** @test */
    function authenticated_users_can_create_an_email_with_template()
    {
        // To make sure we don't start with a template
        $this->assertCount(0, EmailLog::all());

        $user = User::factory()->create();
        $template = EmailTemplate::factory()->create([
            'user_id' => $user->id,
        ]);
        $faker = Faker\Factory::create();

        $from = $faker->email;
        $to = $faker->email;
        $subject = implode(' ', $faker->words);
        $payload = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'use_template' => 1,
            'plain_content' => '',
            'template_id' => $template->id,
            'template_data' => json_encode(['planet' => 'Earth']),
        ];

        $response = $this->actingAs($user)->postJson(route('emails.store'), $payload);

        $this->assertCount(1, EmailLog::all());

        tap(EmailLog::first(), function ($email) use ($response, $user, $template, $payload) {
            $this->assertEquals($payload['from'], $email->from);
            $this->assertEquals($payload['to'], $email->to);

            $this->assertEquals($payload['subject'], $email->subject);
            $this->assertEquals(1, $email->use_template);
            $this->assertEmpty($email->plain_content);
            $this->assertEquals($template->id, $email->template_id);
            $this->assertEquals(json_encode(['planet' => 'Earth']), $email->template_data);

            $this->assertTrue($email->user->is($user));

            $response->assertStatus(200)
                ->assertJson([
                    'status' => 'OK',
                    'message' => 'Email queued.'
                ]);
        });
    }

    /** @test */
    function authenticated_users_can_create_an_email_without_template()
    {
        // To make sure we don't start with a template
        $this->assertCount(0, EmailLog::all());

        $user = User::factory()->create();
        $faker = Faker\Factory::create();

        $from = $faker->email;
        $to = $faker->email;
        $subject = implode(' ', $faker->words);
        $payload = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'use_template' => 0,
            'plain_content' => 'The is a plain content email',
            'template_id' => null,
            'template_data' => null,
        ];

        $response = $this->actingAs($user)->postJson(route('emails.store'), $payload);

        $this->assertCount(1, EmailLog::all());

        tap(EmailLog::first(), function ($email) use ($response, $user, $payload) {
            $this->assertEquals($payload['from'], $email->from);
            $this->assertEquals($payload['to'], $email->to);

            $this->assertEquals($payload['subject'], $email->subject);
            $this->assertEquals(0, $email->use_template);
            $this->assertEquals('The is a plain content email', $email->plain_content);
            $this->assertEmpty($email->template_id);
            $this->assertEmpty($email->template_data);

            $this->assertTrue($email->user->is($user));

            $response->assertStatus(200)
                ->assertJson([
                    'status' => 'OK',
                    'message' => 'Email queued.'
                ]);
        });
    }

    /** @test */
    function an_email_requires_a_from_a_to_and_a_subject()
    {
        $this->assertCount(0, EmailLog::all());

        $user = User::factory()->create();
        $faker = Faker\Factory::create();

        $from = $faker->email;
        $to = $faker->email;
        $subject = implode(' ', $faker->words);

        $payload1 = [
            'from' => '',
            'to' => $to,
            'subject' => $subject,
            'use_template' => 0,
            'plain_content' => 'The is a plain content email',
            'template_id' => null,
            'template_data' => null,
        ];
        $response1 = $this->actingAs($user)->postJson(route('emails.store'), $payload1);
        $this->assertCount(0, EmailLog::all());
        $response1->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.from', ['The from field is required.']);

        $payload2 = [
            'from' => $from,
            'to' => '',
            'subject' => $subject,
            'use_template' => 0,
            'plain_content' => 'The is a plain content email',
            'template_id' => null,
            'template_data' => null,
        ];
        $response2 = $this->actingAs($user)->postJson(route('emails.store'), $payload2);
        $this->assertCount(0, EmailLog::all());
        $response2->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.to', ['The to field is required.']);

        $payload3 = [
            'from' => $from,
            'to' => $to,
            'subject' => '',
            'use_template' => 0,
            'plain_content' => 'The is a plain content email',
            'template_id' => null,
            'template_data' => null,
        ];
        $response3 = $this->actingAs($user)->postJson(route('emails.store'), $payload3);
        $this->assertCount(0, EmailLog::all());
        $response3->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.subject', ['The subject field is required.']);
    }

    /** @test */
    function from_and_to_field_should_be_emails()
    {
        $this->assertCount(0, EmailLog::all());

        $user = User::factory()->create();
        $faker = Faker\Factory::create();

        $from = $faker->email;
        $to = $faker->email;
        $subject = implode(' ', $faker->words);

        $payload1 = [
            'from' => 'not_an_email',
            'to' => $to,
            'subject' => $subject,
            'use_template' => 0,
            'plain_content' => 'The is a plain content email',
            'template_id' => null,
            'template_data' => null,
        ];
        $response1 = $this->actingAs($user)->postJson(route('emails.store'), $payload1);
        $this->assertCount(0, EmailLog::all());
        $response1->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.from', ['The from must be a valid email address.']);

        $payload2 = [
            'from' => $from,
            'to' => 'not_an_email',
            'subject' => $subject,
            'use_template' => 0,
            'plain_content' => 'The is a plain content email',
            'template_id' => null,
            'template_data' => null,
        ];
        $response2 = $this->actingAs($user)->postJson(route('emails.store'), $payload2);
        $this->assertCount(0, EmailLog::all());
        $response2->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.to', ['The to must be a valid email address.']);
    }

    /** @test */
    function when_use_template_equals_to_one_template_id_and_data_are_required()
    {
        $this->assertCount(0, EmailLog::all());

        $user = User::factory()->create();
        $faker = Faker\Factory::create();

        $from = $faker->email;
        $to = $faker->email;
        $subject = implode(' ', $faker->words);

        $payload1 = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'use_template' => 1,
            'plain_content' => '',
            'template_id' => null,
            'template_data' => json_encode(['planet' => 'Earth']),
        ];
        $response1 = $this->actingAs($user)->postJson(route('emails.store'), $payload1);
        $this->assertCount(0, EmailLog::all());
        $response1->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.template_id', ['The template id field is required.']);


        $payload2 = [
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'use_template' => 1,
            'plain_content' => '',
            'template_id' => 1,
            'template_data' => null,
        ];
        $response2 = $this->actingAs($user)->postJson(route('emails.store'), $payload2);
        $this->assertCount(0, EmailLog::all());
        $response2->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.template_data', ['The template data field is required.']);
    }

    /** @test */
    function when_use_template_equals_to_zero_plain_content_is_required()
    {
        $this->assertCount(0, EmailLog::all());

        $user = User::factory()->create();
        $faker = Faker\Factory::create();

        $from = $faker->email;
        $to = $faker->email;
        $subject = implode(' ', $faker->words);

        $payload = [
            'from' => '',
            'to' => $to,
            'subject' => $subject,
            'use_template' => 0,
            'plain_content' => null,
            'template_id' => 1,
            'template_data' => json_encode(['planet' => 'Earth']),
        ];
        $response = $this->actingAs($user)->postJson(route('emails.store'), $payload);
        $this->assertCount(0, EmailLog::all());
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonPath('errors.plain_content', ['The plain content field is required.']);
    }
}
