<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $creator;
    protected User $assignee;
    protected Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->creator = User::factory()->create();
        $this->creator->assignRole('manager');

        $this->assignee = User::factory()->create();
        $this->assignee->assignRole('member');

        // Create a task
        $this->task = Task::create([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'creator_id' => $this->creator->id,
            'assignee_id' => $this->assignee->id,
            'priority' => 'medium',
            'status' => 'assigned',
            'due_date' => now()->addDays(7),
        ]);
    }

    public function test_assignee_can_accept_task(): void
    {
        $response = $this->actingAs($this->assignee)
            ->postJson("/api/tasks/{$this->task->id}/accept");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'accepted',
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('task_acceptances', [
            'task_id' => $this->task->id,
            'user_id' => $this->assignee->id,
            'status' => 'accepted',
        ]);
    }

    public function test_assignee_can_reject_task(): void
    {
        $response = $this->actingAs($this->assignee)
            ->postJson("/api/tasks/{$this->task->id}/reject", [
                'reason' => 'Not enough information provided',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'rejected',
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'status' => 'rejected',
        ]);

        $this->assertDatabaseHas('task_acceptances', [
            'task_id' => $this->task->id,
            'user_id' => $this->assignee->id,
            'status' => 'rejected',
            'reason' => 'Not enough information provided',
        ]);
    }

    public function test_non_assignee_cannot_accept_task(): void
    {
        $otherUser = User::factory()->create();
        $otherUser->assignRole('member');

        $response = $this->actingAs($otherUser)
            ->postJson("/api/tasks/{$this->task->id}/accept");

        $response->assertStatus(403);

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'status' => 'assigned',
        ]);
    }

    public function test_completed_task_cannot_be_accepted(): void
    {
        $this->task->update(['status' => 'completed']);

        $response = $this->actingAs($this->assignee)
            ->postJson("/api/tasks/{$this->task->id}/accept");

        $response->assertStatus(422);

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'status' => 'completed',
        ]);
    }
} 