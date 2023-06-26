<?php

namespace Database\Factories;

use App\Models\User_relation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User_relation>
 */
class User_relationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_company' => 1,
            'id_user' => $this->faker->unique()->numberBetween(3, User::count()),
            'is_manager' => 0,
        ];
    }
}
