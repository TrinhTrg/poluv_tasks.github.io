<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'category_id' => $request->input('category_id'),
            'status' => $request->input('status'),
            'per_page' => $request->input('per_page', 10),
        ];

        $tasks = $this->taskService->getTasks($filters);

        return response()->json($tasks);
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $task = $this->taskService->createTask($request->validated());

            return response()->json($task, 201);
        } catch (\App\Exceptions\UnauthorizedException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): JsonResponse
    {
        try {
            $task = $this->taskService->getTask($task->id);

            return response()->json($task);
        } catch (\App\Exceptions\ForbiddenException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        } catch (\App\Exceptions\NotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        try {
            $task = $this->taskService->updateTask($task->id, $request->validated());

            return response()->json($task);
        } catch (\App\Exceptions\UnauthorizedException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        } catch (\App\Exceptions\ForbiddenException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        } catch (\App\Exceptions\NotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task): JsonResponse
    {
        try {
            $this->taskService->deleteTask($task->id);

            return response()->json([
                'message' => 'Task deleted successfully.',
            ]);
        } catch (\App\Exceptions\UnauthorizedException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        } catch (\App\Exceptions\ForbiddenException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        } catch (\App\Exceptions\NotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Toggle task completion status.
     */
    public function toggle(Task $task): JsonResponse
    {
        try {
            $task = $this->taskService->toggleTask($task->id);

            return response()->json($task);
        } catch (\App\Exceptions\UnauthorizedException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        } catch (\App\Exceptions\ForbiddenException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        } catch (\App\Exceptions\NotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
