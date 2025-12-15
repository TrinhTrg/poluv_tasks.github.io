<?php

namespace App\Listeners;

use App\Events\TaskDue;
use App\Notifications\TaskDueNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendTaskDueNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskDue $event): void
    {
        $task = $event->task;
        
        // Check if task has notification enabled
        if (!$task->has_notify) {
            return;
        }

        // Get the user associated with the task
        $user = $task->user;

        if ($user) {
            // Send notification using Notification::route (following teacher's pattern)
            Notification::route('mail', $user->email)
                ->notify(new TaskDueNotification($task));
            
            // Alternative approach (also valid):
            // $user->notify(new TaskDueNotification($task));
        }
    }
}

