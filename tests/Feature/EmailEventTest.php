<?php

namespace Rh36\EmailApiPackage\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Rh36\EmailApiPackage\Models\EmailLog;
use Rh36\EmailApiPackage\Tests\BaseTestCase;
use Rh36\EmailApiPackage\Tests\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Bus;
use Rh36\EmailApiPackage\Events\EmailWasCreated;
use Rh36\EmailApiPackage\Listeners\EnqueueEmail;
use Rh36\EmailApiPackage\Jobs\SendByPostmark;

use Faker;

class EmailEventTest extends BaseTestCase
{
    use RefreshDatabase;

    /** @test */
    function an_event_is_emitted_when_a_new_email_is_created()
    {
        Event::fake();

        $user = User::factory()->create();
        $faker = Faker\Factory::create();

        $from = env('TEST_EMAIL');
        $to = env('TEST_EMAIL');
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

        $this->actingAs($user)->postJson(route('emails.store'), $payload);
        $email = EmailLog::first();

        Event::assertDispatched(EmailWasCreated::class, function ($event) use ($email) {
            return $event->emailLog->id === $email->id;
        });
    }

    /** @test */
    function the_email_created_event_is_handled_whenever_a_email_is_created()
    {
        Bus::fake();

        $user = User::factory()->create();
        $faker = Faker\Factory::create();
        $from = env('TEST_EMAIL');
        $to = env('TEST_EMAIL');
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

        $this->actingAs($user)->postJson(route('emails.store'), $payload);
        $email = EmailLog::first();

        Bus::assertDispatched(SendByPostmark::class, function ($job) use ($email) {
            return $job->emailLog->id === $email->id;
        });
    }
}
