<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDueNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Task $task
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
        $dueTime = $this->task->due_at 
            ? $this->task->due_at->format('F j, Y \a\t g:i A') 
            : 'Not specified';

        $mail = (new MailMessage)
            ->subject('Task Due Reminder: ' . $this->task->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a reminder that your task is due soon.')
            ->line('**Task:** ' . $this->task->title)
            ->line('**Due Date:** ' . $dueTime)
            ->action('View Task', url('/'))
            ->line('Thank you for using our application!');

        if ($this->task->description) {
            $mail->line('**Description:** ' . $this->task->description);
        }

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
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'due_at' => $this->task->due_at?->toDateTimeString(),
        ];
    }
}
