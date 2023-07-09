<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'cpf' => $this->faker->numberBetween($min = 100000000, $max = 999999999),
            'admission' => now(),
            'responsibility' => 'Engineer',
            'sector' => 'Maintenance',
            'id_company' => $this->faker->numberBetween($min = 1, $max = 2),
            'active' => 1,
        ];
    }
}
