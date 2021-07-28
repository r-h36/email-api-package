<?php

namespace Rh36\EmailApiPackage\Database\Factories;

use Rh36\EmailApiPackage\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Rh36\EmailApiPackage\Tests\User;

class EmailTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailTemplate::class;

    public function definition()
    {
        $user = User::factory()->create();

        return [
            'user_id' => $user->id,
            'template_name' => $this->faker->word,
            'template_body' => 'Hello, {{ $planet }}!',
        ];
    }
}
