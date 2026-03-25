<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
// basic request validation
//bussines rule validation za "create"
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['todo', 'in_progress', 'done', 'failed'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'due_date' => ['nullable', 'date'],
            'external_reference' => ['nullable', 'string', 'max:100', 'unique:tasks,external_reference'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $status = $this->input('status', 'todo');
                $priority = $this->input('priority', 'medium');
                $dueDate = $this->input('due_date');

                if ($status === 'done' && $dueDate && $dueDate > now()->toDateString()) {
                    $validator->errors()->add('due_date', 'A done task cannot have a future due date.');
                }

                if ($priority === 'high' && empty($dueDate)) {
                    $validator->errors()->add('due_date', 'A high priority task must have a due date.');
                }
            },
        ];
    }
}