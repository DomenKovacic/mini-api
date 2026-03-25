<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_task_successfully(): void
    // preverba ce api vrne 201
    // task pride nazaj z pricakovanim JSON-om
    //task je shranjen v bazo
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Test task',
            'priority' => 'medium',
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'title' => 'Test task',
                'priority' => 'medium',
                'status' => 'todo',
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test task',
            'priority' => 'medium',
            'status' => 'todo',
        ]);
    }

    public function test_it_rejects_high_priority_task_without_due_date(): void
    //preverba api vrne 422
    //validation error - due_date
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Invalid high priority task',
            'priority' => 'high',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['due_date']);
    }
}