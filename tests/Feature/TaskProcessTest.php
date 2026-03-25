<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskProcessTest extends TestCase
{
    use RefreshDatabase;

    public function test_processing_todo_task_moves_it_to_in_progress(): void
    //todo postane "in_progress"
    {
        $task = Task::create([
            'title' => 'Process me',
            'status' => 'todo',
            'priority' => 'medium',
        ]);

        $response = $this->postJson("/api/tasks/{$task->id}/process");

        $response
            ->assertOk()
            ->assertJsonPath('task.status', 'in_progress');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_done_task_cannot_be_moved_back_to_todo(): void
    //ce je task "done", ne more nazaj v "todo"
    {
        $task = Task::create([
            'title' => 'Completed task',
            'status' => 'done',
            'priority' => 'medium',
            'due_date' => now()->subDay()->toDateString(),
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'status' => 'todo',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }
}