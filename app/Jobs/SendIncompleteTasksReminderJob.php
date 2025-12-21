<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use App\Notifications\IncompleteTasksReminderNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendIncompleteTasksReminderJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Skip if job is part of a batch that was cancelled
            if ($this->batch() && $this->batch()->cancelled()) {
                return;
            }

            // Optimized: Get overdue tasks and tasks without due date in separate efficient queries
            // Query 1: Get all overdue tasks (no limit - we want all of them)
            $overdueTasks = Task::where('user_id', $this->user->id)
                ->where('is_completed', false)
                ->whereNotNull('due_at')
                ->where('due_at', '<', Carbon::now())
                ->orderBy('due_at', 'asc')
                ->get();

            // Query 2: Get recent tasks without due date (limit to 10)
            $tasksWithoutDueDate = Task::where('user_id', $this->user->id)
                ->where('is_completed', false)
                ->whereNull('due_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Combine both collections
            $allIncompleteTasks = $overdueTasks->merge($tasksWithoutDueDate);

            if ($allIncompleteTasks->isEmpty()) {
                return;
            }

            // Send reminder email
            $this->user->notify(new IncompleteTasksReminderNotification($allIncompleteTasks));
        } catch (Throwable $e) {
            Log::error('Failed to send incomplete tasks reminder', [
                'user_id' => $this->user->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);
            
            // Re-throw to trigger job retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('Send incomplete tasks reminder job failed after all retries', [
            'user_id' => $this->user->id,
            'exception' => $exception?->getMessage(),
        ]);
    }
}

