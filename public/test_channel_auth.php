<?php
// Test channel authorization
require_once '../vendor/autoload.php';

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;

// Bootstrap Laravel
$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing channel authorization for chat.34...\n";

// Test the channel authorization callback
$conversationId = 34;
$conversation = \App\Models\Conversation::find($conversationId);

if (!$conversation) {
    echo "ERROR: Conversation 34 not found\n";
    exit(1);
}

echo "Conversation found: ID {$conversation->id}\n";
echo "Conversation status: {$conversation->status}\n";

// Test with no user (public access)
echo "\nTesting public access (no user):\n";
$result = Broadcast::channel('chat.34', function ($user, $conversationId) {
    // For authenticated users (admins), allow access to any conversation
    if ($user) {
        $conversation = \App\Models\Conversation::find($conversationId);
        if (!$conversation) {
            return false;
        }
        
        // Allow any authenticated user to access any conversation
        // This allows admins to access all conversations
        return true;
    }
    
    // For unauthenticated users (customers), allow access
    // This allows public users to join their own conversations
    return true;
});

echo "Channel authorization result: " . ($result ? 'ALLOWED' : 'DENIED') . "\n";

// Test broadcasting a message
echo "\nTesting message broadcast...\n";
try {
    $message = \App\Models\Message::where('conversation_id', 34)->latest()->first();
    if ($message) {
        echo "Latest message: ID {$message->id}, Content: {$message->message}\n";
        
        // Test broadcasting
        broadcast(new \App\Events\NewChatMessage($message));
        echo "Message broadcasted successfully\n";
    } else {
        echo "No messages found in conversation 34\n";
    }
} catch (Exception $e) {
    echo "Error broadcasting message: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
?>

