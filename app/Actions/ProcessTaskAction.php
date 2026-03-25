<?php

namespace App\Actions;

use App\Models\Task;

class ProcessTaskAction
// hendla "process task" obnasanje
// vzame en task ter spremeni njegov status
{
    public function execute(Task $task): Task
    {
        if ($task->status === 'todo') {
            $task->status = 'in_progress';
        } elseif ($task->status === 'in_progress') {
            $task->status = fake()->boolean(80) ? 'done' : 'failed';
        }

        $task->save();

        return $task->fresh();
    }
}