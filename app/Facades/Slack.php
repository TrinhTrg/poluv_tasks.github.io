<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool send(string $message, ?array $context = [])
 * @method static bool error(string $message, ?\Throwable $exception = null)
 * @method static bool warning(string $message, array $context = [])
 * @method static bool success(string $message, array $context = [])
 * @method static bool info(string $message, array $context = [])
 * @method static bool taskDueSoon(string $taskTitle, string $dueDate, ?string $assignedTo = null)
 * @method static bool newUserRegistered(string $userName, string $email)
 * 
 * @see \App\Services\SlackNotificationService
 */
class Slack extends Facade  // Facade để sử dụng SlackNotificationService trong các class khác
{
    protected static function getFacadeAccessor()
    {
        return 'slack.notification';
    }
}

