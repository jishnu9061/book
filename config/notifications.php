<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification System Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the HelpDesk notification system.
    | You can customize various aspects of how notifications are handled.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Notification Channels
    |--------------------------------------------------------------------------
    |
    | Define the default channels that will be used for notifications.
    | Available channels: email, database, broadcast
    |
    */
    'default_channels' => [
        'email',
        'database',
        'broadcast',
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration specific to email notifications.
    |
    */
    'email' => [
        'enabled' => env('NOTIFICATION_EMAIL_ENABLED', true),
        'queue_enabled' => env('NOTIFICATION_EMAIL_QUEUE', true),
        'retry_attempts' => env('NOTIFICATION_EMAIL_RETRY', 3),
        'retry_delay' => env('NOTIFICATION_EMAIL_RETRY_DELAY', 60), // seconds
        'max_recipients_per_batch' => env('NOTIFICATION_EMAIL_BATCH_SIZE', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Broadcast Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration specific to real-time broadcast notifications.
    |
    */
    'broadcast' => [
        'enabled' => env('NOTIFICATION_BROADCAST_ENABLED', true),
        'channels' => [
            'user' => 'App.Models.User.{id}',
            'ticket' => 'ticket.{id}',
            'system' => 'system.announcements',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration specific to database-stored notifications.
    |
    */
    'database' => [
        'enabled' => env('NOTIFICATION_DATABASE_ENABLED', true),
        'cleanup_after_days' => env('NOTIFICATION_DATABASE_CLEANUP_DAYS', 30),
        'max_notifications_per_user' => env('NOTIFICATION_DATABASE_MAX_PER_USER', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Template Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for email template processing.
    |
    */
    'templates' => [
        'default_language' => env('NOTIFICATION_TEMPLATE_LANGUAGE', 'en'),
        'fallback_template' => env('NOTIFICATION_FALLBACK_TEMPLATE', 'custom_mail'),
        'variable_patterns' => [
            '/\{([^}]+)\}/',           // {variable}
            '/\{\{([^}]+)\}\}/',       // {{variable}}
            '/\[([^\]]+)\]/',          // [variable]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Types
    |--------------------------------------------------------------------------
    |
    | Define the available notification types and their default settings.
    |
    */
    'types' => [
        'ticket_created' => [
            'enabled' => true,
            'channels' => ['email', 'database', 'broadcast'],
            'template' => 'create_ticket_dashboard',
        ],
        'ticket_updated' => [
            'enabled' => true,
            'channels' => ['email', 'database', 'broadcast'],
            'template' => 'ticket_updated',
        ],
        'ticket_assigned' => [
            'enabled' => true,
            'channels' => ['email', 'database', 'broadcast'],
            'template' => 'assigned_ticket',
        ],
        'ticket_comment' => [
            'enabled' => true,
            'channels' => ['email', 'database', 'broadcast'],
            'template' => 'ticket_new_comment',
        ],
        'user_created' => [
            'enabled' => true,
            'channels' => ['email', 'database'],
            'template' => 'user_created',
        ],
        'system_announcement' => [
            'enabled' => true,
            'channels' => ['database', 'broadcast'],
            'template' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for notifications to prevent spam.
    |
    */
    'rate_limiting' => [
        'enabled' => env('NOTIFICATION_RATE_LIMITING', true),
        'max_per_minute' => env('NOTIFICATION_MAX_PER_MINUTE', 60),
        'max_per_hour' => env('NOTIFICATION_MAX_PER_HOUR', 1000),
        'max_per_day' => env('NOTIFICATION_MAX_PER_DAY', 10000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging for notification events.
    |
    */
    'logging' => [
        'enabled' => env('NOTIFICATION_LOGGING', true),
        'level' => env('NOTIFICATION_LOG_LEVEL', 'info'),
        'log_failures' => env('NOTIFICATION_LOG_FAILURES', true),
        'log_success' => env('NOTIFICATION_LOG_SUCCESS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security-related settings for notifications.
    |
    */
    'security' => [
        'sanitize_html' => env('NOTIFICATION_SANITIZE_HTML', true),
        'allowed_html_tags' => [
            'p', 'br', 'strong', 'em', 'u', 'a', 'ul', 'ol', 'li',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span'
        ],
        'encrypt_sensitive_data' => env('NOTIFICATION_ENCRYPT_DATA', false),
    ],
];
