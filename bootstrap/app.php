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
        
        // Register custom middleware aliases
        $middleware->alias([
            'abilities' => \App\Http\Middleware\CheckTokenAbilities::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tích hợp Sentry để tự động capture unhandled exceptions
        Integration::handles($exceptions);

        // Handle custom exceptions with appropriate HTTP status codes
        $exceptions->render(function (\App\Exceptions\UnauthorizedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'status' => 401,
                ], 401);
            }
        });

        $exceptions->render(function (\App\Exceptions\ForbiddenException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'status' => 403,
                ], 403);
            }
        });

        $exceptions->render(function (\App\Exceptions\NotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'status' => 404,
                ], 404);
            }
        });

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
