<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomepageController extends Controller
{
    /**
     * Hiển thị trang chủ dashboard.
     */
    public function index()
    {
        // Guest không thể xem tasks của user khác - chỉ hiển thị landing page
        if (Auth::check()) {
            $userId = Auth::id();
            
            // Don't cache for AJAX requests (reloadTasks function)
            // Only cache for initial page load
            $isAjaxRequest = request()->ajax() && request()->header('X-Requested-With') === 'XMLHttpRequest';
            
            if (!$isAjaxRequest) {
                $cacheKey = 'homepage:tasks:user:' . $userId;
                // Cache homepage tasks for 30 seconds (frequent updates expected)
                $tasks = Cache::remember($cacheKey, 30, function () use ($userId) {
                    return Task::with('category')
                        ->where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();
                });
            } else {
                // For AJAX requests, always fetch fresh data
                $tasks = Task::with('category')
                    ->where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        } else {
            // Guest mode: không hiển thị tasks, chỉ hiển thị landing page
            $tasks = collect([]); // Empty collection
        }

        // Tính toán tasks cho hôm nay (để render sidebar ngay từ server)
        $today = now()->format('Y-m-d');
        $todayTasks = $tasks->filter(function($task) use ($today) {
            if (!$task->due_at) return false;
            $taskDate = $task->due_at->format('Y-m-d');
            return $taskDate === $today && !$task->is_completed;
        })->sortBy(function($task) {
            // Sắp xếp theo start_at hoặc due_at
            $time = $task->start_at ?? $task->due_at;
            return $time ? $time->format('H:i:s') : '23:59:59';
        });

        // If AJAX request, return only task list HTML
        if (request()->ajax() && request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('components.task.grids', compact('tasks'))->render();
        }

        // Trả về view 'index' và truyền biến $tasks sang
        return view('homepage', compact('tasks', 'todayTasks'));
    }
}