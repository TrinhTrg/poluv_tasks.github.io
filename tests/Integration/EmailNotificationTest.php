<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Notifications\TaskDueNotification;
use App\Notifications\PasswordResetNotification;
use App\Notifications\IncompleteTasksReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;

/**
 * Integration Tests for Email Notifications
 * Tests email notification sending and content
 */
class EmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function task_due_notification_can_be_sent()
    {
        Notification::fake();

        $user = $this->createUser();
        $task = $this->createTask([
            'user_id' => $user->id,
            'title' => 'Due Task',
            'due_at' => now()->addHour(),
        ]);

        $user->notify(new TaskDueNotification($task));

        Notification::assertSentTo(
            $user,
            TaskDueNotification::class,
            function ($notification) use ($task) {
                return $notification->task->id === $task->id;
            }
        );
    }

    /** @test */
    public function task_due_notification_has_correct_content()
    {
        Mail::fake();

        $user = $this->createUser();
        $task = $this->createTask([
            'user_id' => $user->id,
            'title' => 'Test Task',
            'description' => 'Task description',
            'due_at' => now()->addHour(),
        ]);

        $notification = new TaskDueNotification($task);
        $mailMessage = $notification->toMail($user);

        $this->assertStringContainsString('Test Task', $mailMessage->subject);
        $this->assertStringContainsString('Test Task', $mailMessage->introLines[1]);
    }

    /** @test */
    public function password_reset_notification_can_be_sent()
    {
        Notification::fake();

        $user = $this->createUser();
        $token = '123456';

        $user->notify(new PasswordResetNotification($token));

        Notification::assertSentTo(
            $user,
            PasswordResetNotification::class
        );
    }

    /** @test */
    public function password_reset_notification_contains_token()
    {
        Mail::fake();

        $user = $this->createUser();
        $token = '123456';

        $notification = new PasswordResetNotification($token);
        $mailMessage = $notification->toMail($user);

        $this->assertStringContainsString($token, $mailMessage->introLines[1]);
    }

    /** @test */
    public function incomplete_tasks_reminder_notification_can_be_sent()
    {
        Notification::fake();

        $user = $this->createUser();
        $tasks = collect([
            $this->createTask(['user_id' => $user->id, 'is_completed' => false]),
            $this->createTask(['user_id' => $user->id, 'is_completed' => false]),
        ]);

        $user->notify(new IncompleteTasksReminderNotification($tasks));

        Notification::assertSentTo(
            $user,
            IncompleteTasksReminderNotification::class
        );
    }

    /** @test */
    public function email_notifications_are_queued_when_configured()
    {
        // Simple check: TaskDueNotification uses Queueable trait
        $this->assertTrue(
            in_array(\Illuminate\Bus\Queueable::class, class_uses(\App\Notifications\TaskDueNotification::class))
        );
    }

    /** @test */
    public function email_notification_handles_missing_due_date()
    {
        Mail::fake();

        $user = $this->createUser();
        $task = $this->createTask([
            'user_id' => $user->id,
            'title' => 'Task without due date',
            'due_at' => null,
        ]);

        $notification = new TaskDueNotification($task);
        $mailMessage = $notification->toMail($user);

        $this->assertStringContainsString('Not specified', $mailMessage->introLines[2]);
    }
}

