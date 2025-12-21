<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class IncompleteTasksReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Collection $tasks
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $overdueCount = $this->tasks->filter(fn($task) => $task->due_at && $task->due_at->isPast())->count();
        $totalCount = $this->tasks->count();

        $mail = (new MailMessage)
            ->subject('Reminder: You have incomplete tasks - PoLuv Tasks')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a friendly reminder that you have **' . $totalCount . '** incomplete task(s).');

        if ($overdueCount > 0) {
            $mail->line('⚠️ **' . $overdueCount . '** of them are overdue.');
        }

        $mail->line('Here are your incomplete tasks:');

        // List overdue tasks first
        $overdueTasks = $this->tasks->filter(fn($task) => $task->due_at && $task->due_at->isPast());
        if ($overdueTasks->isNotEmpty()) {
            $mail->line('**Overdue Tasks:**');
            foreach ($overdueTasks->take(5) as $task) {
                $dueDate = $task->due_at->format('M j, Y g:i A');
                $mail->line("• {$task->title} (Due: {$dueDate})");
            }
            if ($overdueTasks->count() > 5) {
                $mail->line('... and ' . ($overdueTasks->count() - 5) . ' more overdue task(s)');
            }
        }

        // List other incomplete tasks
        $otherTasks = $this->tasks->filter(fn($task) => !$task->due_at || !$task->due_at->isPast());
        if ($otherTasks->isNotEmpty()) {
            $mail->line('**Other Incomplete Tasks:**');
            foreach ($otherTasks->take(5) as $task) {
                if ($task->due_at) {
                    $dueDate = $task->due_at->format('M j, Y g:i A');
                    $mail->line("• {$task->title} (Due: {$dueDate})");
                } else {
                    $mail->line("• {$task->title} (No due date)");
                }
            }
            if ($otherTasks->count() > 5) {
                $mail->line('... and ' . ($otherTasks->count() - 5) . ' more task(s)');
            }
        }

        $mail->action('View All Tasks', url('/home'))
            ->line('Keep up the great work!')
            ->salutation('Regards, The PoLuv Tasks Team');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tasks_count' => $this->tasks->count(),
            'tasks' => $this->tasks->map(fn($task) => [
                'id' => $task->id,
                'title' => $task->title,
                'due_at' => $task->due_at?->toDateTimeString(),
            ])->toArray(),
        ];
    }
}

