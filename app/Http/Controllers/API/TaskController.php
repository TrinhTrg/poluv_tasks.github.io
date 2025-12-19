<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Database\Query\Builder;

class TaskController extends Controller
{
    /**
     * Get current user ID or null for guest
     */
    protected function getUserId()
    {
        return Auth::check() ? Auth::id() : null;
    }
    public function index()
    {
        $userId = $this->getUserId();
        
        $query = Task::query()
            // 1. Lấy Task của User hiện tại hoặc guest (null)
            ->when($userId !== null, function($q) use ($userId) {
                return $q->where('user_id', $userId);
            }, function($q) {
                // Guest mode: lấy tasks không có user_id hoặc user_id = 1 (fallback)
                return $q->where(function($query) {
                    $query->whereNull('user_id')
                          ->orWhere('user_id', 1);
                });
            }) 
            
            // 2. TÌM KIẾM TASK (Search bar chính)
            ->when(request('search'), function(Builder $query, $search) {
                // Tìm trong Title HOẶC Description
                return $query->where(function($q) use ($search) {
                    $q->where('title', 'like', '%'.$search.'%')
                      ->orWhere('description', 'like', '%'.$search.'%');
                });
            })

            // 3. LỌC THEO CATEGORY (Cái Dropdown "All Categories")
            // Nếu gửi lên ?category_id=5 thì chỉ hiện task của danh mục số 5
            ->when(request('category_id'), function($query, $catId) {
                return $query->where('category_id', $catId);
            })

            // 4. LỌC THEO STATUS (Cái Dropdown "All Status")
            // ?status=completed hoặc ?status=pending
            ->when(request('status'), function($query, $status) {
                if ($status === 'completed') return $query->where('is_completed', true);
                if ($status === 'pending') return $query->where('is_completed', false);
            })

            // 5. Sắp xếp
            ->latest('id');

        // Kèm theo thông tin Category (để hiển thị màu sắc, tên danh mục trên thẻ Task)
        return $query->with('category')->simplePaginate(10);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Chặn guest tạo task
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized. Please sign in to create tasks.'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable|string',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'priority' => 'nullable|string|in:low,medium,high',
            'notify' => 'nullable|boolean',
        ]);

        // Tìm category_id từ category name
        $categoryId = null;
        $userId = $this->getUserId();
        if ($validated['category'] ?? null) {
            $category = \App\Models\Category::where('name', $validated['category'])
                ->when($userId !== null, function($q) use ($userId) {
                    return $q->where('user_id', $userId);
                }, function($q) {
                    return $q->where(function($query) {
                        $query->whereNull('user_id')->orWhere('user_id', 1);
                    });
                })
                ->first();
            if ($category) {
                $categoryId = $category->id;
            }
        }

        // Combine date + time thành datetime
        $startAt = null;
        if ($validated['start_date'] ?? null) {
            $startDate = $validated['start_date'];
            $startTime = $validated['start_time'] ?? '00:00';
            $startAt = $startDate . ' ' . $startTime . ':00';
        }

        $dueAt = null;
        if ($validated['due_date'] ?? null) {
            $dueDate = $validated['due_date'];
            $dueTime = $validated['due_time'] ?? '23:59';
            $dueAt = $dueDate . ' ' . $dueTime . ':00';
        }

        // Convert priority string to integer
        $priorityMap = ['low' => 1, 'medium' => 2, 'high' => 3];
        $priority = $priorityMap[$validated['priority'] ?? 'medium'] ?? 2;

        $userId = $this->getUserId();
        
        // Nếu user đã đăng nhập nhưng getUserId() trả về null, thử lấy trực tiếp
        if ($userId === null && Auth::check()) {
            $userId = Auth::id();
        }
        
        // Nếu vẫn null và user đã đăng nhập, throw error
        if ($userId === null && Auth::check()) {
            throw new \Exception('Unable to determine user ID. Please try logging in again.');
        }
        
        $task = Task::create([
            'user_id' => $userId, // null cho guest, Auth::id() cho authenticated
            'category_id' => $categoryId,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_at' => $startAt,
            'due_at' => $dueAt,
            'color' => $validated['color'] ?? null,
            'priority' => $priority,
            'has_notify' => $validated['notify'] ?? false,
            'is_completed' => false,
        ]);

        return response()->json($task->load('category'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $userId = $this->getUserId();
        // Check ownership: authenticated user can only see their own tasks
        // Guest can see tasks with null user_id or user_id = 1
        if ($userId !== null && $task->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($userId === null && $task->user_id !== null && $task->user_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($task->load('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Chặn guest update task
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized. Please sign in to update tasks.'], 401);
        }

        $userId = $this->getUserId();
        // Check ownership
        if ($userId !== null && $task->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($userId === null && $task->user_id !== null && $task->user_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'start_date' => 'nullable|date',
            'start_time' => 'nullable|string',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'priority' => 'nullable|string|in:low,medium,high',
            'notify' => 'nullable|boolean',
        ]);

        // Tìm category_id từ category name
        if (isset($validated['category'])) {
            $category = \App\Models\Category::where('name', $validated['category'])
                ->when($userId !== null, function($q) use ($userId) {
                    return $q->where('user_id', $userId);
                }, function($q) {
                    return $q->where(function($query) {
                        $query->whereNull('user_id')->orWhere('user_id', 1);
                    });
                })
                ->first();
            $task->category_id = $category ? $category->id : null;
        }

        // Combine date + time thành datetime
        if (isset($validated['start_date'])) {
            $startDate = $validated['start_date'];
            $startTime = $validated['start_time'] ?? '00:00';
            $task->start_at = $startDate . ' ' . $startTime . ':00';
        }

        if (isset($validated['due_date'])) {
            $dueDate = $validated['due_date'];
            $dueTime = $validated['due_time'] ?? '23:59';
            $task->due_at = $dueDate . ' ' . $dueTime . ':00';
        }

        // Update các field khác
        if (isset($validated['title'])) $task->title = $validated['title'];
        if (isset($validated['description'])) $task->description = $validated['description'];
        if (isset($validated['color'])) $task->color = $validated['color'];
        if (isset($validated['priority'])) {
            $priorityMap = ['low' => 1, 'medium' => 2, 'high' => 3];
            $task->priority = $priorityMap[$validated['priority']] ?? 2;
        }
        if (isset($validated['notify'])) $task->has_notify = $validated['notify'];

        $task->save();

        return response()->json($task->load('category'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Chặn guest delete task
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized. Please sign in to delete tasks.'], 401);
        }

        $userId = $this->getUserId();
        // Check ownership
        if ($userId !== null && $task->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($userId === null && $task->user_id !== null && $task->user_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    /**
     * Toggle task completion status.
     */
    public function toggle(Task $task)
    {
        // Chặn guest toggle task
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized. Please sign in to complete tasks.'], 401);
        }

        $userId = $this->getUserId();
        // Check ownership
        if ($userId !== null && $task->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($userId === null && $task->user_id !== null && $task->user_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->is_completed = !$task->is_completed;
        $task->save();

        return response()->json($task->load('category'));
    }
}
