<?php

use Illuminate\Support\Facades\Broadcast;

// Broadcasting authentication route - MUST be first to override Laravel's default
Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    $channelName = $request->input('channel_name');
    $socketId = $request->input('socket_id');

    \Log::info('Broadcasting Auth Request', [
        'channel_name' => $channelName,
        'socket_id' => $socketId,
        'authenticated' => auth()->check(),
        'user_id' => auth()->id()
    ]);

    // Handle chat channels (both authenticated and unauthenticated users)
    if (str_starts_with($channelName, 'chat.')) {
        $conversationId = str_replace('chat.', '', $channelName);

        // Check if conversation exists
        $conversation = \App\Models\Conversation::find($conversationId);
        if (!$conversation) {
            \Log::warning('Broadcasting Auth: Conversation not found', ['conversation_id' => $conversationId]);
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        // For authenticated users, check proper access permissions
        if (auth()->check()) {
            $user = auth()->user();

            // Check if user is a participant in this conversation
            $isParticipant = $conversation->participants()
                ->where('user_id', $user->id)
                ->exists();

            // Check if user is admin/manager/agent
            $isAdmin = in_array($user->role->slug ?? '', ['admin', 'manager', 'agent']);

            // Determine access permissions
            // - Participants can always access conversations they're part of
            // - Admins can access internal conversations (but not customer conversations they're not part of)
            $canAccess = $isParticipant || ($isAdmin && $conversation->type === 'internal');

            if (!$canAccess) {
                \Log::warning('Broadcasting Auth: Unauthorized access attempt', [
                    'user_id' => $user->id,
                    'user_role' => $user->role->slug ?? 'unknown',
                    'conversation_id' => $conversationId,
                    'conversation_type' => $conversation->type,
                    'is_participant' => $isParticipant,
                    'is_admin' => $isAdmin,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                return response()->json(['error' => 'Access denied'], 403);
            }

            \Log::info('Broadcasting Auth: Authenticated user granted access', [
                'user_id' => auth()->id(),
                'conversation_id' => $conversationId,
                'access_reason' => $isParticipant ? 'participant' : 'admin_internal'
            ]);
            return response()->json([
                'auth' => hash_hmac('sha256', $socketId . ':' . $channelName, config('broadcasting.connections.pusher.secret'))
            ]);
        }

        // For unauthenticated users, allow access to their own conversations
        // This allows public users to join their conversations
        \Log::info('Broadcasting Auth: Unauthenticated user granted access', [
            'conversation_id' => $conversationId
        ]);
        return response()->json([
            'auth' => hash_hmac('sha256', $socketId . ':' . $channelName, config('broadcasting.connections.pusher.secret'))
        ]);
    }

    // For other channels, require authentication
    if (!auth()->check()) {
        \Log::warning('Broadcasting Auth: Unauthenticated user denied access to private channel', [
            'channel_name' => $channelName
        ]);
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Use standard Laravel broadcasting auth for authenticated users
    return Broadcast::auth($request);
});

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CronJobsController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\FinalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\ImapController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\LanguagesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\FaqsController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\RequirementsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\TicketFieldsController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PendingUsersController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\PrioritiesController;
use App\Http\Controllers\StatusesController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\TypesController;
use App\Http\Controllers\EmailTemplatesController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FrontPagesController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallController;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth

Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::get('register', [AuthenticatedSessionController::class, 'register'])
    ->name('register')
    ->middleware('guest');

Route::get('password-reset', [AuthenticatedSessionController::class, 'forgotPassword'])->name('password.reset')->middleware('guest');
Route::post('password-reset-email', [AuthenticatedSessionController::class, 'forgotPasswordMail'])->name('password.reset.email')->middleware('guest');
Route::get('password-reset/{token}', [AuthenticatedSessionController::class, 'forgotPasswordToken'])->name('password.reset.token')->middleware('guest');
Route::post('password-reset-confirm', [AuthenticatedSessionController::class, 'forgotPasswordStore'])->name('password.reset.store')->middleware('guest');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store')
    ->middleware('guest');

Route::post('register', [AuthenticatedSessionController::class, 'registerStore'])
    ->name('register.store')
    ->middleware('guest');

Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

Route::get('/ticket/open', [HomeController::class, 'ticketOpen'])
    ->name('ticket_open');

Route::post('/ticket/open', [HomeController::class, 'ticketPublicStore'])
    ->name('ticket_store');

// Dashboard
Route::prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('auth');



    /** Ticket Functions */
    Route::get('tickets', [TicketsController::class, 'index'])
        ->name('tickets')
        ->middleware('auth');

    Route::post('ticket/csv/import', [TicketsController::class, 'csvImport'])->name('ticket.csv.import')
        ->middleware('auth');

    Route::get('ticket/csv/export', [TicketsController::class, 'csvExport'])->name('ticket.csv.export')
        ->middleware('auth');

    Route::get('tickets/create', [TicketsController::class, 'create'])
        ->name('tickets.create')
        ->middleware('auth');

    Route::post('tickets', [TicketsController::class, 'store'])
        ->name('tickets.store')
        ->middleware('auth');

    Route::get('tickets/{uid}', [TicketsController::class, 'show'])
        ->name('tickets.show')
        ->middleware('auth');

    Route::get('tickets/{uid}/edit', [TicketsController::class, 'edit'])
        ->name('tickets.edit')
        ->middleware(['auth', \App\Http\Middleware\TicketUpdateAccess::class]);

    Route::post('tickets/{ticket}', [TicketsController::class, 'update'])
        ->name('tickets.update')
        ->middleware(['auth', \App\Http\Middleware\TicketUpdateAccess::class]);

    Route::delete('tickets/{ticket}', [TicketsController::class, 'destroy'])
        ->name('tickets.destroy')
        ->middleware('auth');

    Route::put('tickets/{ticket}/restore', [TicketsController::class, 'restore'])
        ->name('tickets.restore')
        ->middleware('auth');

    Route::post('ticket/comment', [TicketsController::class, 'newComment'])
        ->name('ticket.comment')
        ->middleware('auth');

    /** Contact Functions */
    Route::get('notes', [NotesController::class, 'index'])
        ->name('notes')
        ->middleware('auth');

    Route::post('notes/{id?}', [NotesController::class, 'saveNote'])
        ->name('notes.save')
        ->middleware('auth');

    Route::delete('notes/{note?}', [NotesController::class, 'delete'])
        ->name('notes.delete')
        ->middleware('auth');

    Route::get('contacts', [ContactsController::class, 'index'])
        ->name('contacts')
        ->middleware('auth');

    Route::get('contacts/create', [ContactsController::class, 'create'])
        ->name('contacts.create')
        ->middleware('auth');

    Route::post('contacts', [ContactsController::class, 'store'])
        ->name('contacts.store')
        ->middleware('auth');

    Route::get('contacts/{contact}/edit', [ContactsController::class, 'edit'])
        ->name('contacts.edit')
        ->middleware('auth');

    Route::put('contacts/{contact}', [ContactsController::class, 'update'])
        ->name('contacts.update')
        ->middleware('auth');

    Route::delete('contacts/{contact}', [ContactsController::class, 'destroy'])
        ->name('contacts.destroy')
        ->middleware('auth');

    Route::put('contacts/{contact}/restore', [ContactsController::class, 'restore'])
        ->name('contacts.restore')
        ->middleware('auth');

    /** Contact Functions */
    Route::get('settings/categories', [CategoriesController::class, 'index'])
        ->name('categories')
        ->middleware('auth');

    Route::get('settings/categories/create', [CategoriesController::class, 'create'])
        ->name('categories.create')
        ->middleware('auth');

    Route::post('settings/categories', [CategoriesController::class, 'store'])
        ->name('categories.store')
        ->middleware('auth');

    Route::get('settings/categories/{category}/edit', [CategoriesController::class, 'edit'])
        ->name('categories.edit')
        ->middleware('auth');

    Route::put('settings/categories/{category}', [CategoriesController::class, 'update'])
        ->name('categories.update')
        ->middleware('auth');

    Route::delete('settings/categories/{category}', [CategoriesController::class, 'destroy'])
        ->name('categories.destroy')
        ->middleware('auth');
    Route::put('settings/categories/{category}/restore', [CategoriesController::class, 'restore'])
        ->name('categories.restore')
        ->middleware('auth');

    Route::get('settings/update', [SettingsController::class, 'systemUpdate'])->name('settings.update')->middleware('auth');
    Route::post('settings/update/check', [SettingsController::class, 'systemUpdateCheck'])->name('settings.update.check')->middleware('auth');

    /** Admin Chat functions */
    Route::prefix('chat')->name('chat.')->middleware('auth')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/create', [ChatController::class, 'create'])->name('create');
        Route::post('/', [ChatController::class, 'store'])->name('store');
        Route::get('/{id}', [ChatController::class, 'chat'])->name('current');
        Route::get('/{chat}/edit', [ChatController::class, 'edit'])->name('edit');
        Route::put('/{chat}', [ChatController::class, 'update'])->name('update');
        Route::delete('/{chat}', [ChatController::class, 'destroy'])->name('destroy');
        Route::post('/message', [ChatController::class, 'newMessage'])->name('message');
        Route::post('/upload-attachments', [ChatController::class, 'uploadAttachments'])->name('upload-attachments');
        Route::post('/mark-read', [ChatController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-conversation-read', [ChatController::class, 'markConversationAsRead'])->name('mark-conversation-read');
        Route::post('/typing-indicator', [ChatController::class, 'updateTypingIndicator'])->name('typing-indicator');
        Route::get('/typing-indicators', [ChatController::class, 'getTypingIndicators'])->name('typing-indicators');
        Route::put('/{chat}/restore', [ChatController::class, 'restore'])->name('restore');
    });

// AI Routes
    Route::prefix('ai')->name('ai.')->middleware('auth')->group(function () {
        Route::get('/status', [App\Http\Controllers\AIController::class, 'getStatus'])->name('status');
        Route::get('/analytics', [App\Http\Controllers\AIController::class, 'getAnalytics'])->name('analytics');
        Route::post('/batch-classify', [App\Http\Controllers\AIController::class, 'batchClassify'])->name('batch-classify');

        // AI Settings
        Route::get('/settings', [App\Http\Controllers\AIController::class, 'getSettings'])->name('settings');
        Route::post('/settings', [App\Http\Controllers\AIController::class, 'saveSettings'])->name('settings.save');

        // AI Response Suggestions
        Route::post('/response-suggestions', [App\Http\Controllers\AIController::class, 'getResponseSuggestions'])->name('response-suggestions');

        // AI Sentiment Analysis
        Route::post('/sentiment-analysis', [App\Http\Controllers\AIController::class, 'analyzeSentiment'])->name('sentiment-analysis');

        // AI Knowledge Base
        Route::get('/knowledge-base/suggestions', [App\Http\Controllers\AIController::class, 'getKnowledgeBaseSuggestions'])->name('knowledge-base-suggestions');

        // Ticket-specific AI routes
        Route::prefix('tickets/{ticket}')->group(function () {
            Route::get('/suggestions', [App\Http\Controllers\AIController::class, 'getClassificationSuggestions'])->name('suggestions');
            Route::post('/classify', [App\Http\Controllers\AIController::class, 'autoClassify'])->name('classify');
            Route::post('/apply-classification', [App\Http\Controllers\AIController::class, 'applyClassification'])->name('apply-classification');
            Route::get('/classification-history', [App\Http\Controllers\AIController::class, 'getClassificationHistory'])->name('classification-history');
        });
    });



    /** Priorities functions */
    Route::get('settings/priorities', [PrioritiesController::class, 'index'])
        ->name('priorities')
        ->middleware('auth');

    Route::get('settings/priorities/create', [PrioritiesController::class, 'create'])
        ->name('priorities.create')
        ->middleware('auth');

    Route::post('settings/priorities', [PrioritiesController::class, 'store'])
        ->name('priorities.store')
        ->middleware('auth');

    Route::get('settings/priorities/{priority}/edit', [PrioritiesController::class, 'edit'])
        ->name('priorities.edit')
        ->middleware('auth');

    Route::put('settings/priorities/{priority}', [PrioritiesController::class, 'update'])
        ->name('priorities.update')
        ->middleware('auth');

    Route::put('settings/priorities/{priority}/restore', [PrioritiesController::class, 'restore'])
        ->name('priorities.restore')
        ->middleware('auth');

    Route::delete('settings/priorities/{priority}', [PrioritiesController::class, 'destroy'])
        ->name('priorities.destroy')
        ->middleware('auth');

// End - Priorities

    /** Faq Route */
    Route::get('faqs', [FaqsController::class, 'index'])
        ->name('faqs')
        ->middleware('auth');

    Route::get('faqs/create', [FaqsController::class, 'create'])
        ->name('faqs.create')
        ->middleware('auth');

    Route::post('faqs', [FaqsController::class, 'store'])
        ->name('faqs.store')
        ->middleware('auth');

    Route::get('faqs/{faq}/edit', [FaqsController::class, 'edit'])
        ->name('faqs.edit')
        ->middleware('auth');

    Route::put('faqs/{faq}', [FaqsController::class, 'update'])
        ->name('faqs.update')
        ->middleware('auth');

    Route::delete('faqs/{faq}', [FaqsController::class, 'destroy'])
        ->name('faqs.destroy')
        ->middleware('auth');

    Route::put('faqs/{faq}/restore', [FaqsController::class, 'restore'])
        ->name('faqs.restore')
        ->middleware('auth');

    /** Blog Route */
    Route::get('posts', [BlogController::class, 'index'])
        ->name('posts')
        ->middleware('auth');

    Route::get('posts/create', [BlogController::class, 'create'])
        ->name('posts.create')
        ->middleware('auth');

    Route::post('posts', [BlogController::class, 'store'])
        ->name('posts.store')
        ->middleware('auth');

    Route::get('posts/{post}/edit', [BlogController::class, 'edit'])
        ->name('posts.edit')
        ->middleware('auth');

    Route::put('posts/{post}', [BlogController::class, 'update'])
        ->name('posts.update')
        ->middleware('auth');

    Route::delete('posts/{post}', [BlogController::class, 'destroy'])
        ->name('posts.destroy')
        ->middleware('auth');
// End - Blog

    /** Knowledge base */
    Route::get('knowledge_base', [KnowledgeBaseController::class, 'index'])
        ->name('knowledge_base')
        ->middleware('auth');

    Route::get('knowledge_base/create', [KnowledgeBaseController::class, 'create'])
        ->name('knowledge_base.create')
        ->middleware('auth');

    Route::post('knowledge_base', [KnowledgeBaseController::class, 'store'])
        ->name('knowledge_base.store')
        ->middleware('auth');

    Route::get('knowledge_base/{knowledge_base}/edit', [KnowledgeBaseController::class, 'edit'])
        ->name('knowledge_base.edit')
        ->middleware('auth');

    Route::post('knowledge_base/{knowledge_base}', [KnowledgeBaseController::class, 'update'])
        ->name('knowledge_base.update')
        ->middleware('auth');

    Route::delete('knowledge_base/{knowledge_base}', [KnowledgeBaseController::class, 'destroy'])
        ->name('knowledge_base.destroy')
        ->middleware('auth');


    /** Status Routing */
    // Statuses
    Route::get('settings/statuses', [StatusesController::class, 'index'])
        ->name('statuses')
        ->middleware('auth');

    Route::get('settings/statuses/create', [StatusesController::class, 'create'])
        ->name('statuses.create')
        ->middleware('auth');

    Route::post('settings/statuses', [StatusesController::class, 'store'])
        ->name('statuses.store')
        ->middleware('auth');

    Route::get('settings/statuses/{status}/edit', [StatusesController::class, 'edit'])
        ->name('statuses.edit')
        ->middleware('auth');

    Route::put('settings/statuses/{status}', [StatusesController::class, 'update'])
        ->name('statuses.update')
        ->middleware('auth');

    Route::put('settings/statuses/{status}/restore', [StatusesController::class, 'restore'])
        ->name('statuses.restore')
        ->middleware('auth');

    Route::delete('settings/statuses/{status}', [StatusesController::class, 'destroy'])
        ->name('statuses.destroy')
        ->middleware('auth');
// End - Statuses



    // Departments
    Route::get('settings/departments', [DepartmentsController::class, 'index'])
        ->name('departments')
        ->middleware('auth');

    Route::get('settings/departments/create', [DepartmentsController::class, 'create'])
        ->name('departments.create')
        ->middleware('auth');

    Route::post('settings/departments', [DepartmentsController::class, 'store'])
        ->name('departments.store')
        ->middleware('auth');

    Route::get('settings/departments/{department}/edit', [DepartmentsController::class, 'edit'])
        ->name('departments.edit')
        ->middleware('auth');

    Route::put('settings/departments/{department}/restore', [DepartmentsController::class, 'restore'])
        ->name('departments.restore')
        ->middleware('auth');

    Route::get('settings/filter/customers', [FilterController::class, 'customers'])
        ->name('filter.customers')
        ->middleware('auth');

    Route::get('settings/filter/assignees', [FilterController::class, 'assignees'])
        ->name('filter.assignees')
        ->middleware('auth');

    Route::get('settings/filter/clients', [FilterController::class, 'clients'])
        ->name('filter.clients')
        ->middleware('auth');

    Route::get('settings/filter/users_except_customer', [FilterController::class, 'usersExceptCustomer'])
        ->name('filter.users_except_customer')
        ->middleware('auth');

    Route::put('settings/departments/{department}', [DepartmentsController::class, 'update'])
        ->name('departments.update')
        ->middleware('auth');

    Route::delete('settings/departments/{department}', [DepartmentsController::class, 'destroy'])
        ->name('departments.destroy')
        ->middleware('auth');
// End - Departments




    // Types
    Route::get('settings/types', [TypesController::class, 'index'])
        ->name('types')
        ->middleware('auth');

    Route::get('settings/types/create', [TypesController::class, 'create'])
        ->name('types.create')
        ->middleware('auth');

    Route::post('settings/types', [TypesController::class, 'store'])
        ->name('types.store')
        ->middleware('auth');

    Route::get('settings/types/{type}/edit', [TypesController::class, 'edit'])
        ->name('types.edit')
        ->middleware('auth');

    Route::put('settings/types/{type}', [TypesController::class, 'update'])
        ->name('types.update')
        ->middleware('auth');
    Route::put('settings/types/{type}/restore', [TypesController::class, 'restore'])
        ->name('types.restore')
        ->middleware('auth');

    Route::delete('settings/types/{type}', [TypesController::class, 'destroy'])
        ->name('types.destroy')
        ->middleware('auth');
// End - Types


    // Email Templates
    Route::get('settings/templates', [EmailTemplatesController::class, 'index'])
        ->name('templates')
        ->middleware('auth');

    Route::get('settings/templates/{emailTemplate}/edit', [EmailTemplatesController::class, 'edit'])
        ->name('templates.edit')
        ->middleware('auth');

    Route::put('settings/templates/{emailTemplate}', [EmailTemplatesController::class, 'update'])
        ->name('templates.update')
        ->middleware('auth');
// End - Email Template

    // Languages
    Route::get('settings/languages', [LanguagesController::class, 'index'])
        ->name('languages')
        ->middleware('auth');

    Route::get('settings/languages/create', [LanguagesController::class, 'create'])
        ->name('languages.create')
        ->middleware('auth');

    Route::post('settings/languages', [LanguagesController::class, 'store'])
        ->name('languages.store')
        ->middleware('auth');

    Route::get('settings/languages/{language}/edit', [LanguagesController::class, 'edit'])
        ->name('languages.edit')
        ->middleware('auth');

    Route::put('settings/languages/{language}', [LanguagesController::class, 'update'])
        ->name('languages.update')
        ->middleware('auth');

    Route::post('settings/languages/new_item', [LanguagesController::class, 'newItem'])
        ->name('languages.newItem')
        ->middleware('auth');

    Route::delete('settings/languages/delete_item/{value}', [LanguagesController::class, 'deleteItem'])
        ->name('languages.deleteItem')
        ->middleware('auth');

    Route::delete('settings/languages/{id}', [LanguagesController::class, 'delete'])
        ->name('languages.delete')
        ->middleware('auth');
    // End - Lanuages



    Route::get('users', [UsersController::class, 'index'])
        ->name('users')
        ->middleware('auth');

// Pending Users
    Route::get('users/pending', [PendingUsersController::class, 'index'])
        ->name('pending_users')
        ->middleware('auth');
    Route::get('users/pending/active/{id}', [PendingUsersController::class, 'active'])
        ->name('pending.active')
        ->middleware('auth');
    Route::get('users/pending/decline/{id}', [PendingUsersController::class, 'decline'])
        ->name('pending.decline')
        ->middleware('auth');

    Route::get('users/create', [UsersController::class, 'create'])
        ->name('users.create')
        ->middleware('auth');

    Route::post('users', [UsersController::class, 'store'])
        ->name('users.store')
        ->middleware('auth');

    Route::get('users/{user}/edit', [UsersController::class, 'edit'])
        ->name('users.edit')
        ->middleware('auth');

    Route::get('edit_profile', [DashboardController::class, 'editProfile'])
        ->name('users.edit.profile')
        ->middleware('auth');

    Route::put('users/{user}', [UsersController::class, 'update'])
        ->name('users.update')
        ->middleware('auth');

    Route::delete('users/{user}', [UsersController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('auth');

    Route::put('users/{user}/restore', [UsersController::class, 'restore'])
        ->name('users.restore')
        ->middleware('auth');

// Customers
    Route::get('customers/{user}/edit', [CustomersController::class, 'edit'])
        ->name('customers.edit')
        ->middleware('auth');

    Route::get('customers', [CustomersController::class, 'index'])
        ->name('customers')
        ->middleware('auth');

    Route::put('customers/{user}', [CustomersController::class, 'update'])
        ->name('customers.update')
        ->middleware('auth');

    Route::get('customers/create', [CustomersController::class, 'create'])
        ->name('customers.create')
        ->middleware('auth');

    Route::post('customers', [CustomersController::class, 'store'])
        ->name('customers.store')
        ->middleware('auth');

    Route::delete('customers/{user}', [CustomersController::class, 'destroy'])
        ->name('customers.destroy')
        ->middleware('auth');

    Route::put('customers/{user}/restore', [CustomersController::class, 'restore'])
        ->name('customers.restore')
        ->middleware('auth');


// Organizations

    Route::get('organizations', [OrganizationsController::class, 'index'])
        ->name('organizations')
        ->middleware('auth');

    Route::get('organizations/create', [OrganizationsController::class, 'create'])
        ->name('organizations.create')
        ->middleware('auth');

    Route::post('organizations', [OrganizationsController::class, 'store'])
        ->name('organizations.store')
        ->middleware('auth');

    Route::get('organizations/{organization}/edit', [OrganizationsController::class, 'edit'])
        ->name('organizations.edit')
        ->middleware('auth');

    Route::put('organizations/{organization}', [OrganizationsController::class, 'update'])
        ->name('organizations.update')
        ->middleware('auth');

    Route::delete('organizations/{organization}', [OrganizationsController::class, 'destroy'])
        ->name('organizations.destroy')
        ->middleware('auth');

    Route::put('organizations/{organization}/restore', [OrganizationsController::class, 'restore'])
        ->name('organizations.restore')
        ->middleware('auth');



    // Global Settings
    Route::get('settings/global', [SettingsController::class, 'index'])
        ->name('global')
        ->middleware('auth');
    Route::post('settings/global', [SettingsController::class, 'update'])
        ->name('global.update')
        ->middleware('auth');

    // AI Settings
    Route::get('settings/ai', function () {
        return \Inertia\Inertia::render('Settings/AI');
    })->name('settings.ai')->middleware('auth');

    // AI Analytics Page
    Route::get('ai/analytics-page', function () {
        return \Inertia\Inertia::render('AI/Analytics');
    })->name('ai.analytics.page')->middleware('auth');

    Route::get('settings/smtp', [SettingsController::class, 'smtp'])
        ->name('settings.smtp')
        ->middleware('auth');
    Route::post('settings/smtp/update', [SettingsController::class, 'updateSmtp'])
        ->name('settings.smtp.update')
        ->middleware('auth');
    Route::post('settings/smtp/test', [SettingsController::class, 'testSmtp'])
        ->name('settings.smtp.test')
        ->middleware('auth');

    Route::put('settings/pusher/update', [SettingsController::class, 'updatePusher'])
        ->name('settings.pusher.update')
        ->middleware('auth');
    Route::get('settings/pusher', [SettingsController::class, 'pusher'])
        ->name('settings.pusher')
        ->middleware('auth');
    Route::post('settings/pusher/test', [SettingsController::class, 'testPusher'])
        ->name('settings.pusher.test')
        ->middleware('auth');

    Route::get('settings/piping', [SettingsController::class, 'piping'])
        ->name('settings.piping')
        ->middleware('auth');
    Route::put('settings/piping/update', [SettingsController::class, 'updatePiping'])
        ->name('settings.piping.update')
        ->middleware('auth');


    Route::resource('ticket-fields', TicketFieldsController::class);
    Route::get('settings/custom-form', [TicketFieldsController::class, 'builder'])->name('tickets.builder');
    Route::post('ticket-fields/delete', [TicketFieldsController::class, 'delete'])->name('ticket-fields.delete');


    Route::get('clear/{slug}', [BackupController::class, 'clearCache'])
        ->name('clear.cache');
// End - Global Settings

    /** Front Page Setup */
    Route::get('front_pages/{slug}', [FrontPagesController::class, 'page'])
        ->name('front_pages.page')
        ->middleware('auth');

    Route::put('front_pages/{slug}', [FrontPagesController::class, 'update'])
        ->name('front_pages.update')
        ->middleware('auth');

    Route::post('/upload/image', [FrontPagesController::class, 'uploadImage'])
        ->name('upload.image')
        ->middleware('auth');

    /** User Roles */
    Route::get('settings/roles', [RolesController::class, 'index'])
        ->name('roles')
        ->middleware('auth');
    Route::get('settings/roles/create', [RolesController::class, 'create'])
        ->name('roles.create')
        ->middleware('auth');
    Route::post('settings/roles', [RolesController::class, 'store'])
        ->name('roles.store')
        ->middleware('auth');
    Route::get('settings/roles/{role}/edit', [RolesController::class, 'edit'])
        ->name('roles.edit')
        ->middleware('auth');
    Route::put('settings/roles/{role}', [RolesController::class, 'update'])
        ->name('roles.update')
        ->middleware('auth');
    Route::delete('settings/roles/{role}', [RolesController::class, 'destroy'])
        ->name('roles.destroy')
        ->middleware('auth');
    /** end - User Roles */

});

Route::get('language/flag/{code}', [PageController::class, 'getFlag'])
    ->name('language.flag');


Route::get('language/test/{id}', [LanguagesController::class, 'newLanguageManually'])->name('language.test');

Route::get('/settings/license', [LicenseController::class, 'showSettings'])->name('license.settings');
Route::post('/settings/license/deactivate', [LicenseController::class, 'deactivate'])->name('license.deactivate');

Route::get('/license/activate', [LicenseController::class, 'showActivationForm'])->name('license.show');
Route::post('/license/activate', [LicenseController::class, 'activate'])->name('license.activate');

// Reports
Route::get('reports', [ReportsController::class, 'index'])
    ->name('reports')
    ->middleware('auth');

Route::post('/cke/image', [ImagesController::class, 'ckeImageUpload'])
    ->name('cke.image');



Route::get('/img/{path}', [ImagesController::class, 'show'])
    ->where('path', '.*')
    ->name('image');

// Public Chat
Route::post('chat/init', [ChatController::class, 'init'])
    ->name('chat.init');

Route::get('chat/getConversation/{id}/{contact_id}', [ChatController::class, 'getConversation'])
    ->name('chat.conversation');

Route::post('chat/sendMessage', [ChatController::class, 'sendPublicMessage'])
    ->name('chat.send_message');


/** Language Selector  */
Route::post('/language/{language}', [DashboardController::class, 'setLocale'])
    ->name('language');


/** Site Front-Landing */
Route::get('/', [HomeController::class, 'index'])
    ->name('home');


Route::get('terms-of-services', [PageController::class, 'terms'])
    ->name('terms_service');

Route::get('privacy', [PageController::class, 'privacy'])
    ->name('privacy');

Route::get('contact', [PageController::class, 'contact'])
    ->name('contact');

Route::get('services', [PageController::class, 'services'])
    ->name('services');

Route::post('contact', [PageController::class, 'contactPost'])
    ->name('contact.send');

Route::get('faq', [PageController::class, 'faq'])
    ->name('faq');

Route::get('team', [PageController::class, 'team'])
    ->name('team');

Route::get('kb', [PageController::class, 'kb'])
    ->name('kb');

Route::get('kb/{kb_item}', [PageController::class, 'kbDetails'])
    ->name('kb.details');

Route::get('blog/type/{typeId}', [PageController::class, 'blogByType'])
    ->name('blog.by_type');

Route::get('kb/type/{typeId}', [PageController::class, 'kbByType'])
    ->name('kb.by_type');

Route::get('blog', [PageController::class, 'blog'])
    ->name('blog');

Route::get('blog/{post}', [PageController::class, 'blogDetails'])
    ->name('blog.details');


/** Newsletter Subscribe */
Route::post('subscribe/news', [SubscriptionController::class, 'subscribe'])->name('subscribe.news');
/** Newsletter Subscribe */

/** Installation Steps */
Route::get('/install/init', [InstallController::class, 'init'])->name('install.init');
Route::get('/install/pre_installation', [InstallController::class, 'pre_installation'])->name('install.pre_installation');
Route::get('/install/purchase_code', [InstallController::class, 'purchase_code'])->name('install.purchase_code');
Route::post('/install/purchase_code/verify', [InstallController::class, 'purchaseCodeVerify'])->name('install.purchase_code.verify');
Route::get('/install/database_setup', [InstallController::class, 'database_setup'])->name('install.database_setup');
Route::get('/install/mail_setup', [InstallController::class, 'mail_setup'])->name('install.mail_setup');
Route::post('/install/mail_setup', [InstallController::class, 'mailSetupStore'])->name('install.mail_setup.save');
Route::get('/install/admin_setup', [InstallController::class, 'admin_setup'])->name('install.admin_setup');
Route::post('/install/database_installation', [InstallController::class, 'database_installation'])->name('install.db');
Route::post('/install/system_settings', [InstallController::class, 'adminSetupSave'])->name('install.admin_setup.save');
Route::get('/install/migrate', [InstallController::class, 'migrate'])->name('install.migrate');
Route::get('/backup/mail/test', [BackupController::class, 'testMail'])->name('backup.restore');
Route::get('/backup/uid/fix', [BackupController::class, 'fixUid'])->name('backup.fix.uid');


//Route::get('/complete/welcome', [InstallController::class, 'welcome'])->name('welcome');

// IMAP Custom
Route::get('/cron/imap/direct/run', [ImapController::class, 'run'])->name('cron.imap.run');
Route::get('/cron/piping', [CronJobsController::class, 'piping'])->name('cron.piping');
Route::get('/cron/queue_work', [CronJobsController::class, 'queueWork'])->name('cron.queue_work');

Route::get('/db/seed/{className}', [DatabaseController::class, 'seedByClassName'])->name('bd.seed.class')->middleware('auth');

// Modern Installer Routes
Route::group(['prefix' => 'install', 'middleware' => ['web', 'install']], function () {
    // Main installer page
    Route::get('/', function () {
        try {
            return Inertia::render('Installer/Index');
        } catch (Exception $e) {
            \Log::error('Installer page failed to load', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback to simple HTML installer
            return view('install');
        }
    })->name('installer.index');

    // API endpoints for installer
    Route::post('/check-requirements', [App\Http\Controllers\ModernInstallerController::class, 'checkRequirements'])->name('installer.check-requirements');
    Route::post('/verify-license', [App\Http\Controllers\ModernInstallerController::class, 'verifyLicense'])->name('installer.verify-license');
    Route::post('/test-database', [App\Http\Controllers\ModernInstallerController::class, 'testDatabase'])->name('installer.test-database');
    Route::post('/save-environment', [App\Http\Controllers\ModernInstallerController::class, 'saveEnvironment'])->name('installer.save-environment');
    Route::post('/run-installation', [App\Http\Controllers\ModernInstallerController::class, 'runInstallation'])->name('installer.run-installation');
});

// Legacy installer routes (commented out for now)
// Route::group(['prefix' => 'install', 'as' => 'LaravelInstaller::', 'middleware' => ['web', 'install']], function () {
//     Route::get('/', [InstallerController::class, 'welcome'])->name('welcome');
//     Route::get('environment', [EnvironmentController::class, 'environmentMenu'])->name('environment');
//     Route::get('environment/info', [EnvironmentController::class, 'environmentInfo'])->name('environmentInfo');
//     Route::get('environment/database', [EnvironmentController::class, 'environmentDatabase'])->name('environmentDatabase');
//     Route::get('environment/wizard', [EnvironmentController::class, 'environmentWizard'])->name('environmentWizard');
//     Route::post('environment/saveWizard', [EnvironmentController::class, 'saveWizard'])->name('environmentSaveWizard');
//     Route::post('environment/saveInfo', [EnvironmentController::class, 'saveInfo'])->name('environmentSaveInfo');
//     Route::post('environment/saveDatabase', [EnvironmentController::class, 'saveDatabase'])->name('environmentSaveDatabase');
//     Route::get('environment/classic', [EnvironmentController::class, 'environmentClassic'])->name('environmentClassic');
//     Route::post('environment/saveClassic', [EnvironmentController::class, 'saveClassic'])->name('environmentSaveClassic');
//     Route::get('requirements', [RequirementsController::class, 'requirements'])->name('requirements');
//     Route::get('permissions', [PermissionsController::class, 'permissions'])->name('permissions');
//     Route::get('database', [DatabaseController::class, 'database'])->name('database');
//     Route::get('final', [FinalController::class, 'finish'])->name('final');
//     Route::get('admin_setup', [FinalController::class, 'adminSetup'])->name('admin_setup');
//     Route::post('saveAdminSetup', [FinalController::class, 'saveAdminSetup'])->name('saveAdminSetup');
// });

Route::group(['prefix' => 'update', 'as' => 'LaravelUpdater::', 'middleware' => 'web'], function () {
    Route::group(['middleware' => 'update'], function () {
        Route::get('/', [UpdateController::class, 'welcome'])->name('update.welcome');
        Route::get('overview', [UpdateController::class, 'overview'])->name('overview');
        Route::get('database', [UpdateController::class, 'database'])->name('database');
    });

    // This needs to be out of the middleware because right after the migration has been
    // run, the middleware sends a 404.
    Route::get('final', [UpdateController::class, 'finish'])->name('final');
});
// New code for installer


Route::middleware('auth')->group(function () {
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // Conversation routes with access control
    Route::post('/conversations', [App\Http\Controllers\ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/conversations/{conversation}', [App\Http\Controllers\ConversationController::class, 'show'])->name('conversations.show')->middleware(\App\Http\Middleware\ConversationAccess::class);
    Route::get('/conversations/{conversation}/view', [App\Http\Controllers\ConversationController::class, 'view'])->name('conversations.view')->middleware(\App\Http\Middleware\ConversationAccess::class);
    Route::post('/conversations/{conversation}/messages', [App\Http\Controllers\ConversationController::class, 'sendMessage'])->name('conversations.send-message')->middleware(\App\Http\Middleware\ConversationAccess::class);
    Route::get('/tickets/{ticket}/conversations', [App\Http\Controllers\ConversationController::class, 'getTicketConversations'])->name('tickets.conversations');

    // Image upload for VueQuill
    Route::post('/dashboard/upload/image', [App\Http\Controllers\ImageUploadController::class, 'upload'])->name('image.upload');
});



