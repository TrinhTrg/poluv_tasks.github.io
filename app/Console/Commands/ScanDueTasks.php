<?php

namespace App\Console\Commands;

use App\Events\TaskDue;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ScanDueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'todo:scan-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan for tasks that are due within 15 minutes and send notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Scanning for due tasks...');

        // Calculate the time range: now to 15 minutes from now
        $now = Carbon::now();
        $in15Minutes = $now->copy()->addMinutes(15);

        // Query tasks where:
        // 1. due_at is between now and 15 minutes from now
        // 2. is_notified is false (hasn't been notified yet)
        // 3. is_completed is false (task is not completed)
        // 4. has_notify is true (user wants notifications)
        $dueTasks = Task::where('is_notified', false)
            ->where('is_completed', false)
            ->where('has_notify', true)
            ->whereNotNull('due_at')
            ->whereBetween('due_at', [$now, $in15Minutes])
            ->with('user') // Eager load user relationship
            ->get();

        if ($dueTasks->isEmpty()) {
            $this->info('No tasks found that are due within 15 minutes.');
            return Command::SUCCESS;
        }

        $this->info("Found {$dueTasks->count()} task(s) due within 15 minutes.");

        $notifiedCount = 0;

        foreach ($dueTasks as $task) {
            try {
                // Dispatch the TaskDue event
                event(new TaskDue($task));

                // Update is_notified to true
                $task->update(['is_notified' => true]);

                $notifiedCount++;
                $this->line("✓ Notified for task: {$task->title} (Due: {$task->due_at->format('Y-m-d H:i')})");
            } catch (\Exception $e) {
                $this->error("✗ Failed to notify for task: {$task->title} - {$e->getMessage()}");
            }
        }

        $this->info("Successfully processed {$notifiedCount} task(s).");

        return Command::SUCCESS;
    }
}

