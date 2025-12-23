<?php

namespace App\Providers;

use App\Services\NotificationService;
use App\Services\EmailService;
use App\Services\BroadcastService;
use App\Services\TemplateService;
use Illuminate\Support\ServiceProvider;

/**
 * Notification Service Provider
 * 
 * Registers and configures the notification system services.
 */
class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register services as singletons for better performance
        $this->app->singleton(TemplateService::class);
        $this->app->singleton(EmailService::class);
        $this->app->singleton(BroadcastService::class);
        $this->app->singleton(NotificationService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register any notification-related configurations here
        $this->publishes([
            __DIR__.'/../../config/notifications.php' => config_path('notifications.php'),
        ], 'notifications-config');
    }
}
