<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Task $task */
        $task = $this->route('task');

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in(['todo', 'in_progress', 'done', 'failed'])],
            'priority' => ['sometimes', Rule::in(['low', 'medium', 'high'])],
            'due_date' => ['nullable', 'date'],
            'external_reference' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('tasks', 'external_reference')->ignore($task->id),
            ],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function after(): array
    // podobno kot create ampak sprocesira update-e
    {
        return [
            function ($validator) {
                /** @var Task $task */
                $task = $this->route('task');

                $newStatus = $this->input('status', $task->status);
                $newPriority = $this->input('priority', $task->priority);
                $newDueDate = $this->has('due_date')
                    ? $this->input('due_date')
                    : $task->due_date?->toDateString();

                if ($newStatus === 'done' && $newDueDate && $newDueDate > now()->toDateString()) {
                    $validator->errors()->add('due_date', 'A done task cannot have a future due date.');
                }

                if ($task->status === 'done' && $newStatus === 'todo') {
                    $validator->errors()->add('status', 'A done task cannot be moved back to todo.');
                }

                if ($newPriority === 'high' && empty($newDueDate)) {
                    $validator->errors()->add('due_date', 'A high priority task must have a due date.');
                }
            },
        ];
    }
}