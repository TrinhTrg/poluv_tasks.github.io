<?php

namespace App\Jobs;

use App\Services\SlackNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendSlackNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $message,
        public array $context = [],
        public string $level = 'info'
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SlackNotificationService $slackService): void
    {
        try {
            switch ($this->level) {
                case 'error':
                case 'critical':
                    $slackService->error($this->message, $this->context);
                    break;
                case 'warning':
                    $slackService->warning($this->message, $this->context);
                    break;
                case 'success':
                    $slackService->success($this->message, $this->context);
                    break;
                default:
                    $slackService->info($this->message, $this->context);
                    break;
            }
        } catch (Throwable $e) {
            // Log error but don't fail the job silently
            Log::error('Failed to send Slack notification job: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'message' => $this->message,
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
        Log::error('Slack notification job failed after all retries', [
            'exception' => $exception?->getMessage(),
            'message' => $this->message,
            'level' => $this->level,
        ]);
    }
}

