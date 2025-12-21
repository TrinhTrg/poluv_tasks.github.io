<?php

namespace App\Console\Commands;

use App\Jobs\SendIncompleteTasksReminderJob;
use App\Models\User;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Throwable;

class SendIncompleteTasksReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:send-incomplete-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders to users about their incomplete tasks';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting incomplete tasks reminder batch job...');

        // Get all users who have incomplete tasks
        $users = User::whereHas('tasks', function ($query) {
            $query->where('is_completed', false);
        })->get();

        if ($users->isEmpty()) {
            $this->info('No users with incomplete tasks found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$users->count()} user(s) with incomplete tasks.");

        // Create jobs for each user
        $jobs = $users->map(fn($user) => new SendIncompleteTasksReminderJob($user));

        // Dispatch as a batch
        $batch = Bus::batch($jobs)
            ->name('Send Incomplete Tasks Reminders')
            ->allowFailures()
            ->then(function (Batch $batch) {
                // All jobs completed successfully
                $this->info('All reminder emails sent successfully!');
            })
            ->catch(function (Batch $batch, Throwable $e) {
                // First batch job failure detected
                $this->error('Error sending reminder emails: ' . $e->getMessage());
            })
            ->finally(function (Batch $batch) {
                // The batch has finished executing
                $this->info('Batch job completed. Processed: ' . $batch->totalJobs . ' jobs.');
            })
            ->dispatch();

        $this->info("Batch job dispatched with ID: {$batch->id}");
        $this->info("Processing {$batch->totalJobs} reminder email(s)...");

        return Command::SUCCESS;
    }
}

