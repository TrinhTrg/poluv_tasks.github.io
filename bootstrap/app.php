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
            return response()->view('errors.401', ['message' => $e->getMessage()], 401);
        });

        $exceptions->render(function (\App\Exceptions\ForbiddenException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'status' => 403,
                ], 403);
            }
            return response()->view('errors.403', ['message' => $e->getMessage()], 403);
        });

        $exceptions->render(function (\App\Exceptions\NotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'status' => 404,
                ], 404);
            }
            return response()->view('errors.404', ['message' => $e->getMessage()], 404);
        });

        // Handle 404 Not Found
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The requested resource was not found.',
                    'status' => 404,
                ], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // Handle 500 Internal Server Error
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => app()->environment('production') 
                        ? 'An error occurred. Please try again later.' 
                        : $e->getMessage(),
                    'status' => 500,
                ], 500);
            }
            
            // Only show detailed error in non-production
            if (!app()->environment('production')) {
                return null; // Let Laravel show default error page
            }
            
            return response()->view('errors.500', [], 500);
        });

        // Báo lỗi đến Slack và Sentry khi có exception nghiêm trọng
        $exceptions->report(function (\Throwable $e) {
            // Log to application logs with context
            \Illuminate\Support\Facades\Log::channel('daily')->error('Exception occurred', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'user_id' => auth()->id(),
                'user_email' => auth()->user()?->email,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Send to Slack in production or if explicitly enabled
            if ((app()->environment('production') || config('logging.slack_alerts_enabled', false)) 
                && config('logging.channels.slack.url')) {
                // Chỉ gửi lỗi nghiêm trọng đến Slack (5xx errors)
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() < 500) {
                    return; // Bỏ qua lỗi 4xx (client errors)
                }
                
                try {
                    \App\Facades\Slack::error(
                        "🚨 *Critical Error Alert*\n\n" . 
                        "Exception: `" . get_class($e) . "`\n" .
                        "Message: " . $e->getMessage() . "\n" .
                        "File: `{$e->getFile()}:{$e->getLine()}`\n" .
                        "URL: " . request()->fullUrl(),
                        [
                            'level' => 'critical',
                            'exception' => get_class($e),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'url' => request()->fullUrl(),
                            'user_id' => auth()->id(),
                            'environment' => app()->environment(),
                        ]
                    );
                } catch (\Exception $slackException) {
                    // Fail silently if Slack notification fails
                    \Illuminate\Support\Facades\Log::warning('Failed to send Slack alert: ' . $slackException->getMessage());
                }
            }
        });
    })->create();
