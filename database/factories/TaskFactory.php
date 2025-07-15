<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\KeyResult;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'key_result_id' => null,
            'creator_id' => User::factory(),
            'assignee_id' => User::factory(),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => 'assigned',
            'due_date' => fake()->dateTimeBetween('now', '+30 days'),
        ];
    }

    public function withKeyResult(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'key_result_id' => KeyResult::factory(),
            ];
        });
    }

    public function assigned(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'assigned',
            ];
        });
    }

    public function accepted(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'accepted',
                'accepted_at' => now(),
            ];
        });
    }

    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
            ];
        });
    }

    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'completed_at' => now(),
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

    public function highPriority(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 'high',
            ];
        });
    }

    public function urgent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'priority' => 'urgent',
            ];
        });
    }
} 