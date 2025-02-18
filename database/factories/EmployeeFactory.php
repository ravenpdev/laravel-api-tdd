<?php

declare(strict_types=1);

namespace Database\Factories;

use App\enums\PaymentTypes;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
final class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'job_title' => fake()->jobTitle,
            'payment_type' => PaymentTypes::Salary->value,
            'salary' => fake()->numberBetween(50_000, 100_000),
            'hourly_rate' => null,
        ];
    }

    public function salary(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_type' => PaymentTypes::Salary->value,
                'salary' => fake()->numberBetween(50_000, 100_000),
            ];
        });
    }

    public function hourly(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_type' => PaymentTypes::HourlyRate->value,
                'hourly_rate' => fake()->numberBetween(20, 40),
            ];
        });
    }
}
