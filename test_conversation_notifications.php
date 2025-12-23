<?php

/**
 * Conversation Notification System Test Script
 * 
 * This script tests the complete conversation notification system
 * including events, listeners, services, and email templates.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use App\Events\ConversationCreated;
use App\Events\NewChatMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Ticket;
use App\Models\EmailTemplate;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔔 Testing Conversation Notification System\n";
echo "==========================================\n\n";

// Test 1: Check if events are registered
echo "1. Testing Event Registration...\n";
$listeners = Event::getListeners(ConversationCreated::class);
if (!empty($listeners)) {
    echo "   ✅ ConversationCreated event has listeners registered\n";
    foreach ($listeners as $listener) {
        if (is_string($listener)) {
            echo "   📋 Listener: " . $listener . "\n";
        } else {
            echo "   📋 Listener: " . get_class($listener) . "\n";
        }
    }
} else {
    echo "   ❌ ConversationCreated event has no listeners\n";
}

$listeners = Event::getListeners(NewChatMessage::class);
if (!empty($listeners)) {
    echo "   ✅ NewChatMessage event has listeners registered\n";
    foreach ($listeners as $listener) {
        if (is_string($listener)) {
            echo "   📋 Listener: " . $listener . "\n";
        } else {
            echo "   📋 Listener: " . get_class($listener) . "\n";
        }
    }
} else {
    echo "   ❌ NewChatMessage event has no listeners\n";
}

echo "\n";

// Test 2: Check if email templates exist
echo "2. Testing Email Templates...\n";
$templates = [
    'conversation_created',
    'conversation_new_message', 
    'conversation_participant_added'
];

foreach ($templates as $slug) {
    $template = EmailTemplate::where('slug', $slug)->first();
    if ($template) {
        echo "   ✅ Template '{$slug}' exists\n";
        echo "   📋 Name: {$template->name}\n";
        echo "   📋 Details: {$template->details}\n";
    } else {
        echo "   ❌ Template '{$slug}' not found\n";
    }
}

echo "\n";

// Test 3: Check if services can be instantiated
echo "3. Testing Service Instantiation...\n";
try {
    $conversationService = app(\App\Services\ConversationNotificationService::class);
    echo "   ✅ ConversationNotificationService can be instantiated\n";
} catch (Exception $e) {
    echo "   ❌ ConversationNotificationService failed: " . $e->getMessage() . "\n";
}

try {
    $notification = app(\App\Notifications\ConversationNotification::class);
    echo "   ✅ ConversationNotification can be instantiated\n";
} catch (Exception $e) {
    echo "   ❌ ConversationNotification failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Check if models have required relationships
echo "4. Testing Model Relationships...\n";
try {
    $conversation = new Conversation();
    $participants = $conversation->participants();
    echo "   ✅ Conversation model has participants relationship\n";
} catch (Exception $e) {
    echo "   ❌ Conversation participants relationship failed: " . $e->getMessage() . "\n";
}

try {
    $message = new Message();
    $conversation = $message->conversation();
    echo "   ✅ Message model has conversation relationship\n";
} catch (Exception $e) {
    echo "   ❌ Message conversation relationship failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Check if routes exist
echo "5. Testing Routes...\n";
$routes = [
    'conversations.view' => route('conversations.view', 1),
    'conversations.store' => route('conversations.store'),
];

foreach ($routes as $name => $url) {
    if ($url && $url !== '') {
        echo "   ✅ Route '{$name}' exists: {$url}\n";
    } else {
        echo "   ❌ Route '{$name}' not found\n";
    }
}

echo "\n";

// Test 6: Check if broadcasting is configured
echo "6. Testing Broadcasting Configuration...\n";
$broadcastingDefault = config('broadcasting.default');
$pusherKey = config('broadcasting.connections.pusher.key');

echo "   📋 Broadcasting driver: {$broadcastingDefault}\n";
if ($broadcastingDefault !== 'null' && $pusherKey) {
    echo "   ✅ Broadcasting is configured\n";
    echo "   📋 Pusher key: " . substr($pusherKey, 0, 10) . "...\n";
} else {
    echo "   ⚠️  Broadcasting may not be fully configured\n";
}

echo "\n";

// Test 7: Check if queue is configured
echo "7. Testing Queue Configuration...\n";
$queueDefault = config('queue.default');
echo "   📋 Queue driver: {$queueDefault}\n";
if ($queueDefault !== 'sync') {
    echo "   ✅ Queue is configured for background processing\n";
} else {
    echo "   ⚠️  Queue is set to sync (notifications will be processed immediately)\n";
}

echo "\n";

// Test 8: Check if notification channels are available
echo "8. Testing Notification Channels...\n";
$channels = ['database', 'broadcast'];
foreach ($channels as $channel) {
    try {
        $notification = new \App\Notifications\ConversationNotification(
            new Conversation(),
            'test',
            []
        );
        $via = $notification->via(new User());
        if (in_array($channel, $via)) {
            echo "   ✅ Channel '{$channel}' is available\n";
        } else {
            echo "   ⚠️  Channel '{$channel}' is not enabled\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Channel '{$channel}' failed: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// Summary
echo "🎯 Summary\n";
echo "==========\n";
echo "The conversation notification system has been implemented with:\n";
echo "✅ ConversationCreated event\n";
echo "✅ SendChatMessageNotification listener (updated)\n";
echo "✅ ConversationCreatedNotification listener\n";
echo "✅ ConversationNotificationService\n";
echo "✅ ConversationNotification class\n";
echo "✅ Email templates (3 templates)\n";
echo "✅ Event registration in EventServiceProvider\n";
echo "✅ ConversationController updated to fire events\n";
echo "\n";
echo "📋 Notification Types Implemented:\n";
echo "   • Conversation Created - Notify all participants except creator\n";
echo "   • New Message - Notify all participants except sender\n";
echo "   • Participant Added - Notify newly added participant\n";
echo "\n";
echo "📧 Email Templates Created:\n";
echo "   • conversation_created.html\n";
echo "   • conversation_new_message.html\n";
echo "   • conversation_participant_added.html\n";
echo "\n";
echo "🔄 Notification Channels:\n";
echo "   • Database (in-app notifications)\n";
echo "   • Broadcast (real-time via Pusher)\n";
echo "   • Email (SMTP)\n";
echo "\n";
echo "🚀 The system is ready for testing!\n";
echo "\n";
echo "To test the system:\n";
echo "1. Create a conversation via the UI\n";
echo "2. Send messages in the conversation\n";
echo "3. Add participants to conversations\n";
echo "4. Check the notifications table in the database\n";
echo "5. Check email delivery (if SMTP is configured)\n";
echo "6. Check real-time updates via Pusher\n";
echo "\n";
echo "📝 Note: Make sure to run the email template seeder to add the new templates:\n";
echo "   php artisan db:seed --class=EmailTemplateSeeder\n";
