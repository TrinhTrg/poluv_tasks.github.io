<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        $this->clearTaskCache($task);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        $this->clearTaskCache($task);
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        $this->clearTaskCache($task);
    }

    /**
     * Clear all related caches when a task is modified.
     */
    protected function clearTaskCache(Task $task): void
    {
        $userId = $task->user_id ?? 'guest';
        
        // Tăng số phiên bản cache của user. 
        // Tất cả các key cache có gắn version này sẽ tự động không khớp nữa -> Lấy dữ liệu mới.
        Cache::increment('user:' . $userId . ':tasks_version');
        
        // Vẫn xóa cái key homepage cho chắc chắn
        Cache::forget('homepage:tasks:user:' . $userId);
        
        Log::debug('TaskObserver: Task version incremented for user ' . $userId);
    }
}
