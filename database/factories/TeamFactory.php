<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'description' => fake()->paragraph(),
            'leader_id' => User::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function ($team) {
            // Add leader as a team member with admin role
            $team->members()->attach($team->leader_id, ['role' => 'admin']);

            // Add 3-5 random members
            $members = User::factory()
                ->count(fake()->numberBetween(3, 5))
                ->create();

            foreach ($members as $member) {
                $team->members()->attach($member->id, ['role' => 'member']);
            }
        });
    }

    public function withMembers(int $count = 3): static
    {
        return $this->afterCreating(function ($team) use ($count) {
            $members = User::factory()
                ->count($count)
                ->create();

            foreach ($members as $member) {
                $team->members()->attach($member->id, ['role' => 'member']);
            }
        });
    }

    public function withManagerAndMembers(int $memberCount = 3): static
    {
        return $this->afterCreating(function ($team) use ($memberCount) {
            // Create and add a manager
            $manager = User::factory()->create();
            $manager->assignRole('manager');
            $team->members()->attach($manager->id, ['role' => 'admin']);

            // Create and add members
            $members = User::factory()
                ->count($memberCount)
                ->create();

            foreach ($members as $member) {
                $member->assignRole('member');
                $team->members()->attach($member->id, ['role' => 'member']);
            }
        });
    }
} 