<?php

namespace App\Services;

use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    /**
     * Get tasks with filters and pagination
     */
    public function getTasks(array $filters = [], ?int $userId = null): Paginator|Collection
    {
        $userId = $userId ?? $this->getUserId();
        
        $query = Task::query()
            ->with('category')
            ->when($userId !== null, function ($q) use ($userId) {
                return $q->where('user_id', $userId);
            }, function ($q) {
                // Guest mode: tasks with null user_id or user_id = 1
                return $q->where(function ($query) {
                    $query->whereNull('user_id')
                        ->orWhere('user_id', 1);
                });
            })
            ->when($filters['search'] ?? null, function ($q, $search) {
                return $q->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['category_id'] ?? null, function ($q, $categoryId) {
                return $q->where('category_id', $categoryId);
            })
            ->when($filters['status'] ?? null, function ($q, $status) {
                if ($status === 'completed') {
                    return $q->where('is_completed', true);
                }
                if ($status === 'pending') {
                    return $q->where('is_completed', false);
                }
                return $q;
            })
            ->latest('id');

        $perPage = $filters['per_page'] ?? 10;
        
        if ($perPage >= 1000) {
            return $query->get();
        }

        return $query->simplePaginate($perPage);
    }

    /**
     * Get a single task by ID
     *
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function getTask(int $taskId, ?int $userId = null): Task
    {
        $userId = $userId ?? $this->getUserId();
        
        $task = Task::with('category')->find($taskId);
        
        if (!$task) {
            throw new NotFoundException('Task not found.');
        }

        $this->checkOwnership($task, $userId);

        return $task;
    }

    /**
     * Create a new task
     *
     * @throws UnauthorizedException
     */
    public function createTask(array $data, ?int $userId = null): Task
    {
        $userId = $userId ?? $this->getUserId();
        
        if ($userId === null) {
            throw new UnauthorizedException('Please sign in to create tasks.');
        }

        // Resolve category_id from category name
        $categoryId = $this->resolveCategoryId($data['category'] ?? null, $userId);

        // Combine date and time
        $startAt = $this->combineDateTime($data['start_date'] ?? null, $data['start_time'] ?? null);
        $dueAt = $this->combineDateTime($data['due_date'] ?? null, $data['due_time'] ?? null, '23:59');

        // Convert priority string to integer
        $priority = $this->convertPriority($data['priority'] ?? 'medium');

        $task = Task::create([
            'user_id' => $userId,
            'category_id' => $categoryId,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_at' => $startAt,
            'due_at' => $dueAt,
            'color' => $data['color'] ?? null,
            'priority' => $priority,
            'has_notify' => $data['notify'] ?? false,
            'is_completed' => false,
        ]);

        return $task->load('category');
    }

    /**
     * Update an existing task
     *
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function updateTask(int $taskId, array $data, ?int $userId = null): Task
    {
        $userId = $userId ?? $this->getUserId();
        
        if ($userId === null) {
            throw new UnauthorizedException('Please sign in to update tasks.');
        }

        $task = Task::find($taskId);
        
        if (!$task) {
            throw new NotFoundException('Task not found.');
        }

        $this->checkOwnership($task, $userId);

        // Resolve category_id from category name if provided
        if (isset($data['category'])) {
            $task->category_id = $this->resolveCategoryId($data['category'], $userId);
        }

        // Update datetime fields
        if (isset($data['start_date'])) {
            $task->start_at = $this->combineDateTime($data['start_date'], $data['start_time'] ?? null);
        }

        if (isset($data['due_date'])) {
            $task->due_at = $this->combineDateTime($data['due_date'], $data['due_time'] ?? null, '23:59');
        }

        // Update other fields
        if (isset($data['title'])) {
            $task->title = $data['title'];
        }
        if (isset($data['description'])) {
            $task->description = $data['description'];
        }
        if (isset($data['color'])) {
            $task->color = $data['color'];
        }
        if (isset($data['priority'])) {
            $task->priority = $this->convertPriority($data['priority']);
        }
        if (isset($data['notify'])) {
            $task->has_notify = $data['notify'];
        }

        $task->save();

        return $task->load('category');
    }

    /**
     * Delete a task
     *
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function deleteTask(int $taskId, ?int $userId = null): bool
    {
        $userId = $userId ?? $this->getUserId();
        
        if ($userId === null) {
            throw new UnauthorizedException('Please sign in to delete tasks.');
        }

        $task = Task::find($taskId);
        
        if (!$task) {
            throw new NotFoundException('Task not found.');
        }

        $this->checkOwnership($task, $userId);

        return $task->delete();
    }

    /**
     * Toggle task completion status
     *
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function toggleTask(int $taskId, ?int $userId = null): Task
    {
        $userId = $userId ?? $this->getUserId();
        
        if ($userId === null) {
            throw new UnauthorizedException('Please sign in to complete tasks.');
        }

        $task = Task::find($taskId);
        
        if (!$task) {
            throw new NotFoundException('Task not found.');
        }

        $this->checkOwnership($task, $userId);

        $task->is_completed = !$task->is_completed;
        $task->save();

        return $task->load('category');
    }

    /**
     * Check if user owns the task
     *
     * @throws ForbiddenException
     */
    protected function checkOwnership(Task $task, ?int $userId): void
    {
        if ($userId !== null && $task->user_id !== $userId) {
            throw new ForbiddenException('You do not have permission to access this task.');
        }
        
        if ($userId === null && $task->user_id !== null && $task->user_id !== 1) {
            throw new ForbiddenException('You do not have permission to access this task.');
        }
    }

    /**
     * Resolve category ID from category name
     */
    protected function resolveCategoryId(?string $categoryName, ?int $userId): ?int
    {
        if (!$categoryName) {
            return null;
        }

        $query = Category::where('name', $categoryName);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        } else {
            $query->where(function ($q) {
                $q->whereNull('user_id')->orWhere('user_id', 1);
            });
        }

        $category = $query->first();

        return $category?->id;
    }

    /**
     * Combine date and time into datetime string
     */
    protected function combineDateTime(?string $date, ?string $time, string $defaultTime = '00:00'): ?string
    {
        if (!$date) {
            return null;
        }

        $time = $time ?? $defaultTime;
        return $date . ' ' . $time . ':00';
    }

    /**
     * Convert priority string to integer
     */
    protected function convertPriority(string $priority): int
    {
        $priorityMap = [
            'low' => 1,
            'medium' => 2,
            'high' => 3,
        ];

        return $priorityMap[$priority] ?? 2;
    }

    /**
     * Get current user ID or null
     */
    protected function getUserId(): ?int
    {
        return Auth::check() ? Auth::id() : null;
    }
}

