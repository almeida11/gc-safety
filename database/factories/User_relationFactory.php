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
        $id = $this->faker->unique()->numberBetween(3, User::count());
        return [
            'id_company' => $id%2 == 0 ? 2 : 1,
            'id_user' => $id,
            'is_manager' => 0,
        ];
    }
}
