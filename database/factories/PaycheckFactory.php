<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paycheck>
 */
final class PaycheckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'net_amount' => fake()->numberBetween(35_000, 48_000),
            'payed_at' => Carbon::now(),
        ];
    }
}
