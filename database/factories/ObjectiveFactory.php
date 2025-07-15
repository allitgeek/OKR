<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObjectiveFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'user_id' => User::factory(),
            'team_id' => null,
            'start_date' => fake()->dateTimeBetween('now', '+7 days'),
            'end_date' => fake()->dateTimeBetween('+30 days', '+90 days'),
            'status' => 'not_started',
            'time_period' => fake()->randomElement(['monthly', 'quarterly', 'yearly']),
            'progress' => 0,
        ];
    }

    public function withTeam(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'team_id' => Team::factory(),
            ];
        });
    }

    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'in_progress',
                'progress' => fake()->numberBetween(1, 99),
            ];
        });
    }

    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'progress' => 100,
            ];
        });
    }

    public function archived(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'archived',
            ];
        });
    }

    public function monthly(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'time_period' => 'monthly',
                'end_date' => fake()->dateTimeBetween('+15 days', '+30 days'),
            ];
        });
    }

    public function quarterly(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'time_period' => 'quarterly',
                'end_date' => fake()->dateTimeBetween('+60 days', '+90 days'),
            ];
        });
    }

    public function yearly(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'time_period' => 'yearly',
                'end_date' => fake()->dateTimeBetween('+270 days', '+365 days'),
            ];
        });
    }
} 