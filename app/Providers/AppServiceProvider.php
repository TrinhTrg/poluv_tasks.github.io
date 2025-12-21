<?php

namespace App\Providers;

use App\Models\Category;
use App\Observers\CategoryObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Đăng ký Slack Notification Service
        $this->app->singleton('slack.notification', function ($app) {
            return new \App\Services\SlackNotificationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Category Observer for automatic cache invalidation
        Category::observe(CategoryObserver::class);
    }
}

