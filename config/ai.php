<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Features Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for AI features in the helpdesk
    | system including OpenAI integration, classification settings, and
    | performance tuning options.
    |
    */

    'enabled' => env('AI_ENABLED', false),

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 500),
        'temperature' => env('OPENAI_TEMPERATURE', 0.3),
        'timeout' => env('OPENAI_TIMEOUT', 30),
    ],

    'classification' => [
        'auto_classify_new_tickets' => env('AI_AUTO_CLASSIFY_NEW_TICKETS', true),
        'auto_classify_existing_tickets' => env('AI_AUTO_CLASSIFY_EXISTING_TICKETS', false),
        'confidence_threshold' => env('AI_CONFIDENCE_THRESHOLD', 0.7),
        'cache_duration' => env('AI_CACHE_DURATION', 3600), // 1 hour
        'batch_size' => env('AI_BATCH_SIZE', 10),
    ],

    'response_suggestions' => [
        'enabled' => env('AI_RESPONSE_SUGGESTIONS_ENABLED', true),
        'max_suggestions' => env('AI_MAX_RESPONSE_SUGGESTIONS', 3),
        'min_confidence' => env('AI_RESPONSE_MIN_CONFIDENCE', 0.6),
    ],

    'sentiment_analysis' => [
        'enabled' => env('AI_SENTIMENT_ANALYSIS_ENABLED', true),
        'auto_escalate_negative' => env('AI_AUTO_ESCALATE_NEGATIVE', true),
        'sentiment_threshold' => env('AI_SENTIMENT_THRESHOLD', -0.3),
    ],

    'analytics' => [
        'enabled' => env('AI_ANALYTICS_ENABLED', true),
        'prediction_horizon_days' => env('AI_PREDICTION_HORIZON_DAYS', 7),
        'update_frequency_hours' => env('AI_ANALYTICS_UPDATE_FREQUENCY', 24),
    ],

    'chatbot' => [
        'enabled' => env('AI_CHATBOT_ENABLED', false),
        'model' => env('AI_CHATBOT_MODEL', 'gpt-3.5-turbo'),
        'max_context_length' => env('AI_CHATBOT_MAX_CONTEXT', 4000),
        'response_timeout' => env('AI_CHATBOT_TIMEOUT', 10),
    ],

    'knowledge_base' => [
        'enabled' => env('AI_KNOWLEDGE_BASE_ENABLED', true),
        'auto_suggest_articles' => env('AI_AUTO_SUGGEST_ARTICLES', true),
        'max_suggestions' => env('AI_KB_MAX_SUGGESTIONS', 5),
        'similarity_threshold' => env('AI_KB_SIMILARITY_THRESHOLD', 0.7),
    ],

    'performance' => [
        'rate_limit_per_minute' => env('AI_RATE_LIMIT_PER_MINUTE', 60),
        'rate_limit_per_hour' => env('AI_RATE_LIMIT_PER_HOUR', 1000),
        'queue_timeout' => env('AI_QUEUE_TIMEOUT', 300), // 5 minutes
        'retry_attempts' => env('AI_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('AI_RETRY_DELAY', 5), // seconds
    ],

    'logging' => [
        'enabled' => env('AI_LOGGING_ENABLED', true),
        'log_level' => env('AI_LOG_LEVEL', 'info'),
        'log_requests' => env('AI_LOG_REQUESTS', true),
        'log_responses' => env('AI_LOG_RESPONSES', false),
    ],

    'security' => [
        'sanitize_input' => env('AI_SANITIZE_INPUT', true),
        'max_input_length' => env('AI_MAX_INPUT_LENGTH', 10000),
        'allowed_file_types' => ['txt', 'md', 'json'],
        'blocked_keywords' => [
            'password', 'secret', 'token', 'key', 'credential'
        ],
    ],
];
