<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomepageController extends Controller
{
    /**
     * Hiển thị trang chủ dashboard.
     */
    public function index()
    {
        // Guest không thể xem tasks của user khác - chỉ hiển thị landing page
        if (Auth::check()) {
            $tasks = Task::with('category')
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();
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

        // Trả về view 'index' và truyền biến $tasks sang
        return view('homepage', compact('tasks', 'todayTasks'));
    }
}