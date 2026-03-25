<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        Task::query()->delete();
        //počisti obstoječe taske tako da se seeding vedno začne iz clean stanja

        Task::create([
            //Task::create inserta simple task enega po enega
            'title' => 'Review API requirements',
            'description' => 'Read the coding challenge carefully and plan the implementation.',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => now()->addDays(3)->toDateString(),
            'external_reference' => 'seed-001',
            'metadata' => [
                'source' => 'seeder',
                'type' => 'planning',
            ],
        ]);

        Task::create([
            'title' => 'Implement CRUD endpoints',
            'description' => 'Create task listing, create, update, show, and delete endpoints.',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => now()->addDay()->toDateString(),
            'external_reference' => 'seed-002',
            'metadata' => [
                'source' => 'seeder',
                'type' => 'development',
            ],
        ]);

        Task::create([
            'title' => 'Write documentation',
            'description' => 'Prepare README with setup and usage instructions.',
            'status' => 'done',
            'priority' => 'low',
            'due_date' => now()->subDays(2)->toDateString(),
            'external_reference' => 'seed-003',
            'metadata' => [
                'source' => 'seeder',
                'type' => 'documentation',
            ],
        ]);

        Task::create([
            'title' => 'Investigate failed process',
            'description' => 'Check why one task ended in failed state.',
            'status' => 'failed',
            'priority' => 'medium',
            'due_date' => now()->subDay()->toDateString(),
            'external_reference' => 'seed-004',
            'metadata' => [
                'source' => 'seeder',
                'type' => 'debugging',
            ],
        ]);
    }
}