<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    // lists task: status, priority, due_before, sort, direction - ujemanje z endpoint requiermenti
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['todo', 'in_progress', 'done', 'failed'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'due_before' => ['nullable', 'date'],
            'sort' => ['nullable', Rule::in(['created_at', 'due_date', 'priority'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);

        $query = Task::query();

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (! empty($validated['priority'])) {
            $query->where('priority', $validated['priority']);
        }

        if (! empty($validated['due_before'])) {
            $query->whereDate('due_date', '<=', $validated['due_before']);
        }

        $sort = $validated['sort'] ?? 'created_at';
        $direction = $validated['direction'] ?? 'desc';

        $tasks = $query->orderBy($sort, $direction)->get();

        return response()->json($tasks);
    }

    public function store(Request $request): JsonResponse
    // ustvar now task - validacija incoming Jsona, zahteva "title", external_reference mora biti unique, defaulta status v todo, defaulta priority v medium
    // vrne 201(upajmo)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['todo', 'in_progress', 'done', 'failed'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'due_date' => ['nullable', 'date'],
            'external_reference' => ['nullable', 'string', 'max:100', 'unique:tasks,external_reference'],
            'metadata' => ['nullable', 'array'],
        ]);

        $data['status'] = $data['status'] ?? 'todo';
        $data['priority'] = $data['priority'] ?? 'medium';

        $task = Task::create($data);

        return response()->json($task, 201);
    }

    public function show(Task $task): JsonResponse
    // vrne task kot Json
    // laravel avtomatkso najde task zaradi model bindinga - Task $task
    {
        return response()->json($task);
    }

    public function update(Request $request, Task $task): JsonResponse
    // updejta EN task
    //Rule::unique(...)->ignore($task->id) - obstojeci task drzi svoj external_reference brez da pade v vodo pri uniquness validation-u
    {
        $data = $request->validate([
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
        ]);

        $task->update($data);

        return response()->json($task);
    }

    public function destroy(Task $task): JsonResponse
    // izbrise EN task in vrne Json message
    {
        $task->delete();

        return response()->json([
            'message' => 'Task deleted',
        ]);
    }
}