<?php

namespace Rh36\EmailApiPackage\Database\Factories;

use Rh36\EmailApiPackage\Models\EmailTemplate;
use Rh36\EmailApiPackage\Models\EmailLog;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rh36\EmailApiPackage\Tests\User;

class EmailLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailLog::class;

    public function definition()
    {
        $user = User::factory()->create();
        $template = EmailTemplate::factory()->create();

        return [
            'user_id' => $user->id,
            'template_id' => $template->id,
            'template_data' => json_encode(['planet' => 'Earth']),
            'use_template' => 1,
            'plain_content' => null,
            'subject' => implode(' ',  $this->faker->words()),

            'from' => env('TEST_EMAIL'),
            'to' => env('TEST_EMAIL'),

            'cc' => null,
            'bcc' => null,
            'replyto' => null,
        ];
    }
}
