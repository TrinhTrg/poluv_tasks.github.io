<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tích hợp Sentry để tự động capture unhandled exceptions
        Integration::handles($exceptions);

        // Báo lỗi đến Slack khi có exception nghiêm trọng
        $exceptions->report(function (\Throwable $e) {
            if (app()->environment('production') && config('logging.channels.slack.url')) {
                // Chỉ gửi lỗi nghiêm trọng đến Slack
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() < 500) {
                    return; // Bỏ qua lỗi 4xx (client errors)
                }
                
                \Illuminate\Support\Facades\Log::channel('slack')->critical(
                    'Error occurred: ' . $e->getMessage(),
                    [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'url' => request()->fullUrl(),
                        'user' => auth()->id(),
                    ]
                );
            }
        });
    })->create();
