<?php

namespace Modules\Communication\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Communication\Models\Conversation;

class ConversationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Conversation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
