<?php

use App\Http\Controllers\Api\MediaPickerController;
use App\Http\Controllers\EmailPipingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Legacy routes (keep for backward compatibility)
Route::post('/email-handler', [EmailPipingController::class, 'receiveEmailWebhook']);
Route::get('/media-picker', [MediaPickerController::class, 'index'])->name('api.media.list');
Route::post('/media-picker/upload', [MediaPickerController::class, 'upload'])->name('api.media.upload');

// Test message endpoint for debugging
Route::post('/test-message', function (Request $request) {
    $message = $request->input('message');
    $channel = $request->input('channel');

    \Log::info('Debug Tool: Test message received', [
        'message' => $message,
        'channel' => $channel
    ]);

    // Extract conversation ID from channel name
    $conversationId = str_replace('chat.', '', $channel);

    // Create a test message with proper relationships
    $testMessage = new \App\Models\Message([
        'conversation_id' => $conversationId,
        'message' => $message,
        'user_id' => null,
        'contact_id' => 999, // Test contact ID
        'created_at' => now(),
        'updated_at' => now()
    ]);

    // Set up relationships for the test message
    $testMessage->user = null;
    $testMessage->contact = (object) [
        'id' => 999,
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com'
    ];

    // Broadcast the test message
    broadcast(new \App\Events\NewChatMessage($testMessage));

    return response()->json([
        'success' => true,
        'message' => 'Test message sent successfully',
        'data' => [
            'message' => $message,
            'channel' => $channel,
            'conversation_id' => $conversationId
        ]
    ]);
});

// Legacy conversation routes (keep for backward compatibility)
Route::middleware('auth')->group(function () {
    Route::post('/conversations', [App\Http\Controllers\ConversationController::class, 'store'])->name('api.conversations.store');
    Route::get('/conversations/{conversation}', [App\Http\Controllers\ConversationController::class, 'show'])->name('api.conversations.show')->middleware(\App\Http\Middleware\ConversationAccess::class);
    Route::post('/conversations/{conversation}/messages', [App\Http\Controllers\ConversationController::class, 'sendMessage'])->name('api.conversations.send-message')->middleware(\App\Http\Middleware\ConversationAccess::class);
});

// API v1 Routes
Route::prefix('v1')->group(function () {
    
    // Authentication routes (public)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [App\Http\Controllers\Api\V1\AuthController::class, 'login'])->middleware('throttle:api-auth');
        Route::post('/register', [App\Http\Controllers\Api\V1\AuthController::class, 'register'])->middleware('throttle:api-auth');
        Route::post('/password/reset', [App\Http\Controllers\Api\V1\AuthController::class, 'passwordReset'])->middleware('throttle:api-auth');
        Route::post('/password/reset/{token}', [App\Http\Controllers\Api\V1\AuthController::class, 'passwordResetWithToken'])->middleware('throttle:api-auth');
    });

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth routes (protected)
        Route::prefix('auth')->group(function () {
            Route::get('/me', [App\Http\Controllers\Api\V1\AuthController::class, 'me']);
            Route::post('/logout', [App\Http\Controllers\Api\V1\AuthController::class, 'logout']);
        });

        // Tickets API
        Route::apiResource('tickets', App\Http\Controllers\Api\V1\TicketController::class)->names([
            'index' => 'api.v1.tickets.index',
            'store' => 'api.v1.tickets.store',
            'show' => 'api.v1.tickets.show',
            'update' => 'api.v1.tickets.update',
            'destroy' => 'api.v1.tickets.destroy',
        ]);
        Route::post('tickets/{ticket}/restore', [App\Http\Controllers\Api\V1\TicketController::class, 'restore'])->name('api.v1.tickets.restore');
        Route::post('tickets/{ticket}/comments', [App\Http\Controllers\Api\V1\TicketController::class, 'addComment'])->name('api.v1.tickets.comments.store');
        Route::get('tickets/{ticket}/comments', [App\Http\Controllers\Api\V1\TicketController::class, 'getComments'])->name('api.v1.tickets.comments.index');
        Route::post('tickets/import', [App\Http\Controllers\Api\V1\TicketController::class, 'import'])->name('api.v1.tickets.import');
        Route::get('tickets/export', [App\Http\Controllers\Api\V1\TicketController::class, 'export'])->name('api.v1.tickets.export');
        Route::get('tickets/{ticket}/conversations', [App\Http\Controllers\Api\V1\TicketController::class, 'getConversations'])->name('api.v1.tickets.conversations.index');

        // Contacts API
        Route::apiResource('contacts', App\Http\Controllers\Api\V1\ContactController::class)->names([
            'index' => 'api.v1.contacts.index',
            'store' => 'api.v1.contacts.store',
            'show' => 'api.v1.contacts.show',
            'update' => 'api.v1.contacts.update',
            'destroy' => 'api.v1.contacts.destroy',
        ]);
        Route::post('contacts/{contact}/restore', [App\Http\Controllers\Api\V1\ContactController::class, 'restore'])->name('api.v1.contacts.restore');

        // Customers API
        Route::apiResource('customers', App\Http\Controllers\Api\V1\CustomerController::class)->names([
            'index' => 'api.v1.customers.index',
            'store' => 'api.v1.customers.store',
            'show' => 'api.v1.customers.show',
            'update' => 'api.v1.customers.update',
            'destroy' => 'api.v1.customers.destroy',
        ]);
        Route::post('customers/{customer}/restore', [App\Http\Controllers\Api\V1\CustomerController::class, 'restore'])->name('api.v1.customers.restore');

        // Users API
        Route::apiResource('users', App\Http\Controllers\Api\V1\UserController::class)->names([
            'index' => 'api.v1.users.index',
            'store' => 'api.v1.users.store',
            'show' => 'api.v1.users.show',
            'update' => 'api.v1.users.update',
            'destroy' => 'api.v1.users.destroy',
        ]);
        Route::post('users/{user}/restore', [App\Http\Controllers\Api\V1\UserController::class, 'restore'])->name('api.v1.users.restore');
        Route::get('users/pending', [App\Http\Controllers\Api\V1\UserController::class, 'pending'])->name('api.v1.users.pending');
        Route::post('users/pending/{id}/approve', [App\Http\Controllers\Api\V1\UserController::class, 'approve'])->name('api.v1.users.pending.approve');
        Route::post('users/pending/{id}/decline', [App\Http\Controllers\Api\V1\UserController::class, 'decline'])->name('api.v1.users.pending.decline');

        // Organizations API
        Route::apiResource('organizations', App\Http\Controllers\Api\V1\OrganizationController::class)->names([
            'index' => 'api.v1.organizations.index',
            'store' => 'api.v1.organizations.store',
            'show' => 'api.v1.organizations.show',
            'update' => 'api.v1.organizations.update',
            'destroy' => 'api.v1.organizations.destroy',
        ]);
        Route::post('organizations/{organization}/restore', [App\Http\Controllers\Api\V1\OrganizationController::class, 'restore'])->name('api.v1.organizations.restore');

        // Conversations API
        Route::apiResource('conversations', App\Http\Controllers\Api\V1\ConversationController::class)->names([
            'index' => 'api.v1.conversations.index',
            'store' => 'api.v1.conversations.store',
            'show' => 'api.v1.conversations.show',
            'update' => 'api.v1.conversations.update',
            'destroy' => 'api.v1.conversations.destroy',
        ]);
        Route::post('conversations/{conversation}/restore', [App\Http\Controllers\Api\V1\ConversationController::class, 'restore'])->name('api.v1.conversations.restore');
        Route::post('conversations/{conversation}/messages', [App\Http\Controllers\Api\V1\ConversationController::class, 'sendMessage'])->name('api.v1.conversations.messages.store');
        Route::get('conversations/{conversation}/messages', [App\Http\Controllers\Api\V1\ConversationController::class, 'getMessages'])->name('api.v1.conversations.messages.index');
        Route::post('conversations/{conversation}/mark-read', [App\Http\Controllers\Api\V1\ConversationController::class, 'markRead'])->name('api.v1.conversations.mark-read');

        // Settings API - Categories
        Route::prefix('settings')->name('api.v1.settings.')->group(function () {
            Route::apiResource('categories', App\Http\Controllers\Api\V1\CategoryController::class)->names([
                'index' => 'categories.index',
                'store' => 'categories.store',
                'show' => 'categories.show',
                'update' => 'categories.update',
                'destroy' => 'categories.destroy',
            ]);
            Route::post('categories/{category}/restore', [App\Http\Controllers\Api\V1\CategoryController::class, 'restore'])->name('categories.restore');

            // Priorities
            Route::apiResource('priorities', App\Http\Controllers\Api\V1\PriorityController::class)->names([
                'index' => 'priorities.index',
                'store' => 'priorities.store',
                'show' => 'priorities.show',
                'update' => 'priorities.update',
                'destroy' => 'priorities.destroy',
            ]);
            Route::post('priorities/{priority}/restore', [App\Http\Controllers\Api\V1\PriorityController::class, 'restore'])->name('priorities.restore');

            // Statuses
            Route::apiResource('statuses', App\Http\Controllers\Api\V1\StatusController::class)->names([
                'index' => 'statuses.index',
                'store' => 'statuses.store',
                'show' => 'statuses.show',
                'update' => 'statuses.update',
                'destroy' => 'statuses.destroy',
            ]);
            Route::post('statuses/{status}/restore', [App\Http\Controllers\Api\V1\StatusController::class, 'restore'])->name('statuses.restore');

            // Departments
            Route::apiResource('departments', App\Http\Controllers\Api\V1\DepartmentController::class)->names([
                'index' => 'departments.index',
                'store' => 'departments.store',
                'show' => 'departments.show',
                'update' => 'departments.update',
                'destroy' => 'departments.destroy',
            ]);
            Route::post('departments/{department}/restore', [App\Http\Controllers\Api\V1\DepartmentController::class, 'restore'])->name('departments.restore');

            // Types
            Route::apiResource('types', App\Http\Controllers\Api\V1\TypeController::class)->names([
                'index' => 'types.index',
                'store' => 'types.store',
                'show' => 'types.show',
                'update' => 'types.update',
                'destroy' => 'types.destroy',
            ]);
            Route::post('types/{type}/restore', [App\Http\Controllers\Api\V1\TypeController::class, 'restore'])->name('types.restore');

            // Roles
            Route::apiResource('roles', App\Http\Controllers\Api\V1\RoleController::class)->names([
                'index' => 'roles.index',
                'store' => 'roles.store',
                'show' => 'roles.show',
                'update' => 'roles.update',
                'destroy' => 'roles.destroy',
            ]);
        });

        // Knowledge Base API
        Route::apiResource('knowledge-base', App\Http\Controllers\Api\V1\KnowledgeBaseController::class)->names([
            'index' => 'api.v1.knowledge-base.index',
            'store' => 'api.v1.knowledge-base.store',
            'show' => 'api.v1.knowledge-base.show',
            'update' => 'api.v1.knowledge-base.update',
            'destroy' => 'api.v1.knowledge-base.destroy',
        ]);

        // FAQs API
        Route::apiResource('faqs', App\Http\Controllers\Api\V1\FaqController::class)->names([
            'index' => 'api.v1.faqs.index',
            'store' => 'api.v1.faqs.store',
            'show' => 'api.v1.faqs.show',
            'update' => 'api.v1.faqs.update',
            'destroy' => 'api.v1.faqs.destroy',
        ]);
        Route::post('faqs/{faq}/restore', [App\Http\Controllers\Api\V1\FaqController::class, 'restore'])->name('api.v1.faqs.restore');

        // Posts API
        Route::apiResource('posts', App\Http\Controllers\Api\V1\PostController::class)->names([
            'index' => 'api.v1.posts.index',
            'store' => 'api.v1.posts.store',
            'show' => 'api.v1.posts.show',
            'update' => 'api.v1.posts.update',
            'destroy' => 'api.v1.posts.destroy',
        ]);

        // Notes API
        Route::apiResource('notes', App\Http\Controllers\Api\V1\NoteController::class)->names([
            'index' => 'api.v1.notes.index',
            'store' => 'api.v1.notes.store',
            'show' => 'api.v1.notes.show',
            'update' => 'api.v1.notes.update',
            'destroy' => 'api.v1.notes.destroy',
        ]);

        // Dashboard API
        Route::prefix('dashboard')->name('api.v1.dashboard.')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\V1\DashboardController::class, 'index'])->name('index');
            Route::get('/metrics', [App\Http\Controllers\Api\V1\DashboardController::class, 'metrics'])->name('metrics');
            Route::get('/analytics', [App\Http\Controllers\Api\V1\DashboardController::class, 'analytics'])->name('analytics');
            Route::get('/performance', [App\Http\Controllers\Api\V1\DashboardController::class, 'performance'])->name('performance');
            Route::get('/charts', [App\Http\Controllers\Api\V1\DashboardController::class, 'charts'])->name('charts');
        });

        // Reports API
        Route::prefix('reports')->name('api.v1.reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\V1\ReportController::class, 'index'])->name('index');
            Route::post('/generate', [App\Http\Controllers\Api\V1\ReportController::class, 'generate'])->name('generate');
            Route::get('/{id}', [App\Http\Controllers\Api\V1\ReportController::class, 'show'])->name('show');
        });

        // AI API
        Route::prefix('ai')->name('api.v1.ai.')->group(function () {
            Route::get('/status', [App\Http\Controllers\Api\V1\AIController::class, 'status'])->name('status');
            Route::get('/analytics', [App\Http\Controllers\Api\V1\AIController::class, 'analytics'])->name('analytics');
            Route::post('/batch-classify', [App\Http\Controllers\Api\V1\AIController::class, 'batchClassify'])->name('batch-classify');
            Route::get('/settings', [App\Http\Controllers\Api\V1\AIController::class, 'getSettings'])->name('settings.get');
            Route::post('/settings', [App\Http\Controllers\Api\V1\AIController::class, 'updateSettings'])->name('settings.update');
            Route::post('/response-suggestions', [App\Http\Controllers\Api\V1\AIController::class, 'responseSuggestions'])->name('response-suggestions');
            Route::post('/sentiment-analysis', [App\Http\Controllers\Api\V1\AIController::class, 'sentimentAnalysis'])->name('sentiment-analysis');
            Route::get('/knowledge-base/suggestions', [App\Http\Controllers\Api\V1\AIController::class, 'knowledgeBaseSuggestions'])->name('knowledge-base.suggestions');
            Route::get('/tickets/{ticket}/suggestions', [App\Http\Controllers\Api\V1\AIController::class, 'getTicketSuggestions'])->name('tickets.suggestions');
            Route::post('/tickets/{ticket}/classify', [App\Http\Controllers\Api\V1\AIController::class, 'classifyTicket'])->name('tickets.classify');
            Route::post('/tickets/{ticket}/apply-classification', [App\Http\Controllers\Api\V1\AIController::class, 'applyClassification'])->name('tickets.apply-classification');
            Route::get('/tickets/{ticket}/classification-history', [App\Http\Controllers\Api\V1\AIController::class, 'classificationHistory'])->name('tickets.classification-history');
        });

        // Media/Attachments API
        Route::apiResource('media', App\Http\Controllers\Api\V1\MediaController::class)->names([
            'index' => 'api.v1.media.index',
            'store' => 'api.v1.media.store',
            'show' => 'api.v1.media.show',
            'update' => 'api.v1.media.update',
            'destroy' => 'api.v1.media.destroy',
        ]);

        // Notifications API
        Route::prefix('notifications')->name('api.v1.notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\V1\NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [App\Http\Controllers\Api\V1\NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/read-all', [App\Http\Controllers\Api\V1\NotificationController::class, 'markAllAsRead'])->name('read-all');
        });
    });
});
