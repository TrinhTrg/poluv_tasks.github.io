<?php

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;
use App\Facades\Slack;

class SlowQueryDetected
{
    /**
     * Slow query threshold in milliseconds (configurable via .env)
     */
    private int $slowQueryThreshold;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->slowQueryThreshold = (int) env('SLOW_QUERY_THRESHOLD', 1000); // Default: 1 second
    }

    /**
     * Handle the event.
     */
    public function handle(QueryExecuted $event): void
    {
        $executionTime = $event->time; // Time in milliseconds

        if ($executionTime > $this->slowQueryThreshold) {
            $this->logSlowQuery($event, $executionTime);
            $this->sendAlert($event, $executionTime);
        }
    }

    /**
     * Log slow query to application logs
     */
    private function logSlowQuery(QueryExecuted $event, float $executionTime): void
    {
        Log::channel('daily')->warning('Slow query detected', [
            'query' => $event->sql,
            'bindings' => $event->bindings,
            'time' => $executionTime . 'ms',
            'connection' => $event->connectionName,
        ]);
    }

    /**
     * Send alert to Slack
     */
    private function sendAlert(QueryExecuted $event, float $executionTime): void
    {
        // Only send alerts in production or if explicitly enabled
        if (!app()->environment('production') && !config('logging.slow_query_alerts', false)) {
            return;
        }

        $message = "🐌 *Slow Query Detected*\n\n";
        $message .= "Execution time: *{$executionTime}ms* (threshold: {$this->slowQueryThreshold}ms)\n";
        $message .= "Connection: `{$event->connectionName}`\n\n";
        $message .= "```sql\n" . $this->formatQuery($event->sql, $event->bindings) . "\n```";

        $context = [
            'level' => 'warning',
            'execution_time_ms' => $executionTime,
            'threshold_ms' => $this->slowQueryThreshold,
            'connection' => $event->connectionName,
            'query' => $event->sql,
            'bindings' => $event->bindings,
        ];

        Slack::warning($message, $context);
    }

    /**
     * Format SQL query with bindings
     */
    private function formatQuery(string $sql, array $bindings): string
    {
        // Replace placeholders with actual values for readability
        $formatted = $sql;
        
        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'{$binding}'";
            $formatted = preg_replace('/\?/', $value, $formatted, 1);
        }

        // Limit query length for Slack message
        if (strlen($formatted) > 1000) {
            $formatted = substr($formatted, 0, 1000) . '... [truncated]';
        }

        return $formatted;
    }
}

