<?php

namespace Rh36\EmailApiPackage\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Rh36\EmailApiPackage\Models\EmailLog;
use Rh36\EmailApiPackage\Tests\BaseTestCase;

use Rh36\EmailApiPackage\Tests\User;
use Illuminate\Support\Facades\Queue;
use Rh36\EmailApiPackage\Jobs\SendByPostmark;

use Faker;

class EmailJobTest extends BaseTestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_postmark_job_is_pushed_into_queue_when_a_new_email_is_created()
    {
        Queue::fake();

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

        $this->actingAs($user)->postJson(route('emails.store'), $payload);
        $email = EmailLog::first();

        Queue::assertPushed(SendByPostmark::class, function ($job) use ($email) {
            return $job->emailLog->id === $email->id;
        });
    }
}
