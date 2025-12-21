<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackNotificationService
{
    protected $webhookUrl;
    protected $username;
    protected $emoji;

    public function __construct()
    {
        $this->webhookUrl = config('logging.channels.slack.url');
        $this->username = config('logging.channels.slack.username', 'PoLuv Tasks');
        $this->emoji = config('logging.channels.slack.emoji', ':warning:');
    }

    /**
     * Gửi thông báo đơn giản đến Slack
     * 
     * @param bool $async If true, dispatch to queue instead of sending immediately
     */
    public function send(string $message, ?array $context = [], bool $async = true): bool
    {
        if (empty($this->webhookUrl)) {
            Log::warning('Slack webhook URL not configured');
            return false;
        }

        // For critical errors, send immediately (synchronously)
        if (!$async || ($context['level'] ?? '') === 'critical') {
            return $this->sendSync($message, $context);
        }

        // For other notifications, dispatch to queue
        try {
            \App\Jobs\SendSlackNotificationJob::dispatch(
                $message,
                $context,
                $context['level'] ?? 'info'
            );
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to dispatch Slack notification job: ' . $e->getMessage());
            // Fallback to synchronous send
            return $this->sendSync($message, $context);
        }
    }

    /**
     * Send notification synchronously (immediately)
     */
    protected function sendSync(string $message, ?array $context = []): bool
    {
        try {
            $payload = [
                'username' => $this->username,
                'icon_emoji' => $this->emoji,
                'text' => $message,
            ];

            if (!empty($context)) {
                $payload['attachments'] = [
                    [
                        'color' => $this->getColorByLevel($context['level'] ?? 'info'),
                        'fields' => $this->formatContext($context),
                        'footer' => config('app.name'),
                        'ts' => time(),
                    ]
                ];
            }

            $response = Http::post($this->webhookUrl, $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to send Slack notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi thông báo lỗi đến Slack
     */
    public function error(string $message, array|\Throwable $contextOrException = null): bool
    {
        $context = [
            'level' => 'error',
            'message' => $message,
        ];

        if ($contextOrException instanceof \Throwable) {
            $exception = $contextOrException;
            $context['exception'] = get_class($exception);
            $context['file'] = $exception->getFile();
            $context['line'] = $exception->getLine();
            $context['trace'] = substr($exception->getTraceAsString(), 0, 500);
        } elseif (is_array($contextOrException)) {
            $context = array_merge($context, $contextOrException);
        }

        return $this->send($message, $context);
    }

    /**
     * Gửi thông báo cảnh báo đến Slack
     */
    public function warning(string $message, array $context = []): bool
    {
        $context['level'] = 'warning';
        return $this->send('⚠️ *Warning*', array_merge(['message' => $message], $context));
    }

    /**
     * Gửi thông báo thành công đến Slack
     */
    public function success(string $message, array $context = []): bool
    {
        $context['level'] = 'success';
        return $this->send('✅ *Success*', array_merge(['message' => $message], $context));
    }

    /**
     * Gửi thông báo info đến Slack
     */
    public function info(string $message, array $context = []): bool
    {
        $context['level'] = 'info';
        return $this->send('ℹ️ *Info*', array_merge(['message' => $message], $context));
    }

    /**
     * Format context thành Slack attachment fields
     */
    protected function formatContext(array $context): array
    {
        $fields = [];

        foreach ($context as $key => $value) {
            if ($key === 'level') continue;

            $fields[] = [
                'title' => ucfirst(str_replace('_', ' ', $key)),
                'value' => is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : (string)$value,
                'short' => strlen((string)$value) < 50,
            ];
        }

        return $fields;
    }

    /**
     * Lấy màu dựa trên level
     */
    protected function getColorByLevel(string $level): string
    {
        return match($level) {
            'error', 'critical', 'emergency' => '#FF0000',
            'warning' => '#FFA500',
            'success' => '#36a64f',
            'info' => '#2196F3',
            default => '#808080',
        };
    }

    /**
     * Gửi thông báo task quan trọng sắp đến hạn
     */
    public function taskDueSoon(string $taskTitle, string $dueDate, ?string $assignedTo = null): bool
    {
        $context = [
            'level' => 'warning',
            'task' => $taskTitle,
            'due_date' => $dueDate,
        ];

        if ($assignedTo) {
            $context['assigned_to'] = $assignedTo;
        }

        return $this->send('⏰ *Task Due Soon*', $context);
    }

    /**
     * Gửi thông báo khi có người dùng mới đăng ký
     */
    public function newUserRegistered(string $userName, string $email): bool
    {
        $context = [
            'level' => 'success',
            'user' => $userName,
            'email' => $email,
            'time' => now()->format('Y-m-d H:i:s'),
        ];

        return $this->send('👋 *New User Registered*', $context);
    }
}

