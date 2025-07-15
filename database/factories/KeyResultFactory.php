<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Objective;
use Illuminate\Database\Eloquent\Factories\Factory;

class KeyResultFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'objective_id' => Objective::factory(),
            'owner_id' => User::factory(),
            'target_value' => fake()->numberBetween(50, 1000),
            'current_value' => fake()->numberBetween(0, 50),
            'metric_unit' => fake()->randomElement(['count', 'percentage', 'currency', 'hours']),
            'progress' => 0,
            'status' => 'not_started',
        ];
    }

    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'in_progress',
                'current_value' => fake()->numberBetween(1, $attributes['target_value']),
            ];
        });
    }

    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'current_value' => $attributes['target_value'],
                'progress' => 100,
            ];
        });
    }

    public function blocked(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'blocked',
            ];
        });
    }

    public function percentage(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'target_value' => 100,
                'current_value' => fake()->numberBetween(0, 100),
                'metric_unit' => 'percentage',
            ];
        });
    }

    public function currency(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'target_value' => fake()->numberBetween(10000, 100000),
                'current_value' => fake()->numberBetween(0, 10000),
                'metric_unit' => 'currency',
            ];
        });
    }
} 