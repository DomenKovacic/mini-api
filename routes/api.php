<?php

use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;
//CRUD route

Route::post('tasks/{task}/process', [TaskController::class, 'process']);
Route::apiResource('tasks', TaskController::class);