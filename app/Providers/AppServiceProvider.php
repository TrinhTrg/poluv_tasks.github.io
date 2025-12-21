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
        
        // Configure Laravel Pulse authorization
        // Only authenticated users can access Pulse dashboard
        // You can customize this gate based on your needs (e.g., admin only)
        \Illuminate\Support\Facades\Gate::define('viewPulse', function ($user = null) {
            // Allow all authenticated users to view Pulse
            // Change this to check for admin role if needed:
            // return $user && $user->isAdmin();
            return $user !== null;
        });
    }
}

