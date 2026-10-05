<?php

namespace Modules\Communication\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Communication\Models\CommunicationMessage;

class CommunicationMessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = CommunicationMessage::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'message' => fake()->paragraph(),
            'audience' => 'all',
            'channels' => ['database'],
            'recipient_count' => 1,
            'sent_at' => now(),
        ];
    }
}
