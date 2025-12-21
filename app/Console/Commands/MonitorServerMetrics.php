<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Facades\Slack;

class MonitorServerMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:server-metrics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor server metrics (CPU, memory, disk, network) and send alerts if thresholds are exceeded';

    /**
     * Thresholds for alerting (configurable via .env)
     */
    private array $thresholds = [
        'cpu' => 80,        // CPU usage percentage
        'memory' => 85,     // Memory usage percentage
        'disk' => 90,       // Disk usage percentage
        'load' => 2.0,      // System load average
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Monitoring server metrics...');

        $metrics = $this->collectMetrics();
        $alerts = $this->checkThresholds($metrics);

        if (!empty($alerts)) {
            $this->sendAlerts($alerts, $metrics);
            $this->warn('Alerts sent: ' . count($alerts));
        } else {
            $this->info('All metrics are within normal ranges.');
        }

        // Log metrics for monitoring
        Log::channel('daily')->info('Server metrics collected', $metrics);

        return Command::SUCCESS;
    }

    /**
     * Collect server metrics
     */
    private function collectMetrics(): array
    {
        return [
            'cpu' => $this->getCpuUsage(),
            'memory' => $this->getMemoryUsage(),
            'disk' => $this->getDiskUsage(),
            'load' => $this->getLoadAverage(),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Get CPU usage percentage
     */
    private function getCpuUsage(): float
    {
        if (PHP_OS_FAMILY === 'Windows') {
            // Windows: Use WMI or alternative method
            $output = shell_exec('wmic cpu get loadpercentage /value 2>nul');
            if ($output && preg_match('/LoadPercentage=(\d+)/', $output, $matches)) {
                return (float) $matches[1];
            }
            return 0.0;
        }

        // Linux/Unix: Use top or /proc/stat
        $load = sys_getloadavg();
        if ($load && isset($load[0])) {
            // Approximate CPU usage from load average
            $cpuCount = $this->getCpuCount();
            return min(100, ($load[0] / $cpuCount) * 100);
        }

        return 0.0;
    }

    /**
     * Get memory usage percentage
     */
    private function getMemoryUsage(): float
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $output = shell_exec('wmic OS get TotalVisibleMemorySize,FreePhysicalMemory /value 2>nul');
            if ($output && preg_match('/TotalVisibleMemorySize=(\d+)/', $output, $totalMatches) &&
                preg_match('/FreePhysicalMemory=(\d+)/', $output, $freeMatches)) {
                $total = (int) $totalMatches[1];
                $free = (int) $freeMatches[1];
                $used = $total - $free;
                return ($used / $total) * 100;
            }
            return 0.0;
        }

        // Linux: Read from /proc/meminfo
        $meminfo = @file_get_contents('/proc/meminfo');
        if ($meminfo && preg_match('/MemTotal:\s+(\d+)\s+kB/', $meminfo, $totalMatches) &&
            preg_match('/MemAvailable:\s+(\d+)\s+kB/', $meminfo, $availMatches)) {
            $total = (int) $totalMatches[1];
            $available = (int) $availMatches[1];
            $used = $total - $available;
            return ($used / $total) * 100;
        }

        return 0.0;
    }

    /**
     * Get disk usage percentage
     */
    private function getDiskUsage(): float
    {
        $path = base_path();
        $total = disk_total_space($path);
        $free = disk_free_space($path);

        if ($total && $free) {
            $used = $total - $free;
            return ($used / $total) * 100;
        }

        return 0.0;
    }

    /**
     * Get system load average
     */
    private function getLoadAverage(): float
    {
        $load = sys_getloadavg();
        return $load ? $load[0] : 0.0;
    }

    /**
     * Get CPU count
     */
    private function getCpuCount(): int
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $output = shell_exec('wmic cpu get NumberOfCores /value 2>nul');
            if ($output && preg_match('/NumberOfCores=(\d+)/', $output, $matches)) {
                return (int) $matches[1];
            }
            return 1;
        }

        // Linux: Count from /proc/cpuinfo
        $cpuinfo = @file_get_contents('/proc/cpuinfo');
        if ($cpuinfo) {
            return substr_count($cpuinfo, 'processor');
        }

        return 1;
    }

    /**
     * Check metrics against thresholds
     */
    private function checkThresholds(array $metrics): array
    {
        $alerts = [];

        // Load thresholds from config
        $this->thresholds = [
            'cpu' => (float) env('MONITOR_CPU_THRESHOLD', 80),
            'memory' => (float) env('MONITOR_MEMORY_THRESHOLD', 85),
            'disk' => (float) env('MONITOR_DISK_THRESHOLD', 90),
            'load' => (float) env('MONITOR_LOAD_THRESHOLD', 2.0),
        ];

        if ($metrics['cpu'] > $this->thresholds['cpu']) {
            $alerts[] = [
                'type' => 'cpu',
                'value' => $metrics['cpu'],
                'threshold' => $this->thresholds['cpu'],
                'message' => "CPU usage is {$metrics['cpu']}% (threshold: {$this->thresholds['cpu']}%)",
            ];
        }

        if ($metrics['memory'] > $this->thresholds['memory']) {
            $alerts[] = [
                'type' => 'memory',
                'value' => $metrics['memory'],
                'threshold' => $this->thresholds['memory'],
                'message' => "Memory usage is {$metrics['memory']}% (threshold: {$this->thresholds['memory']}%)",
            ];
        }

        if ($metrics['disk'] > $this->thresholds['disk']) {
            $alerts[] = [
                'type' => 'disk',
                'value' => $metrics['disk'],
                'threshold' => $this->thresholds['disk'],
                'message' => "Disk usage is {$metrics['disk']}% (threshold: {$this->thresholds['disk']}%)",
            ];
        }

        if ($metrics['load'] > $this->thresholds['load']) {
            $alerts[] = [
                'type' => 'load',
                'value' => $metrics['load'],
                'threshold' => $this->thresholds['load'],
                'message' => "System load is {$metrics['load']} (threshold: {$this->thresholds['load']})",
            ];
        }

        return $alerts;
    }

    /**
     * Send alerts via Slack
     */
    private function sendAlerts(array $alerts, array $metrics): void
    {
        $alertMessages = array_map(fn($alert) => $alert['message'], $alerts);
        $message = "🚨 *Server Metrics Alert*\n\n" . implode("\n", $alertMessages);
        
        $context = [
            'metrics' => $metrics,
            'alerts' => $alerts,
            'server' => gethostname(),
            'environment' => app()->environment(),
        ];

        Slack::error($message, $context);
    }
}

