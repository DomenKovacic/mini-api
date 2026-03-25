<?php

namespace App\Http\Controllers\Api;

use App\Actions\ProcessTaskAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller

//separacija:
//controller = HTTP
//request = validation
//action = bussines logic

{
    public function index(Request $request): JsonResponse

    //Vrni vse naloge z neobveznimi filtri in razvrščanjem po stanju/prioriteti/datumu.
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

        return response()->json(
            $query->orderBy($sort, $direction)->get()
        );
    }

    public function store(StoreTaskRequest $request): JsonResponse
    //Shrani novo nalogo po zahtevi in ​​potrditvi poslovnih pravil.
    // namesto da se validira v controllerju, Laravel uporabi form request preden se ta funkcija sprozi
    {
        $data = $request->validated();
        $data['status'] = $data['status'] ?? 'todo';
        $data['priority'] = $data['priority'] ?? 'medium';

        $task = Task::create($data);

        return response()->json($task, 201);
    }

    public function show(Task $task): JsonResponse
    //pokazi en task
    {
        return response()->json($task);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    //updejtaj task po requestu in bussines rule validation-u
    {
        $task->update($request->validated());

        return response()->json($task->fresh());
    }

    public function destroy(Task $task): JsonResponse
    //odstrani task
    {
        $task->delete();

        return response()->json([
            'message' => 'Task deleted',
        ]);
    }

    public function process(Task $task, ProcessTaskAction $action): JsonResponse
    {
        $task = $action->execute($task);

        return response()->json([
            'message' => 'Task processed',
            'task' => $task,
        ]);
    }
}