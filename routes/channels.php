<?php

// This file is temporarily disabled to prevent Laravel from auto-registering
// the default broadcasting auth route. Channel authorization is now handled
// in routes/web.php with our custom broadcasting auth route.

// use Illuminate\Support\Facades\Broadcast;
// use App\Broadcasting\NewChatMessage;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Private user channel for notifications
// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

// Unified chat channel - allow both authenticated and unauthenticated users
// Broadcast::channel('chat.{conversation_id}', function ($user, $conversationId) {
//     \Log::info('Channel authorization attempt', [
//         'channel' => 'chat.' . $conversationId,
//         'user_id' => $user ? $user->id : 'unauthenticated',
//         'conversation_id' => $conversationId
//     ]);
    
//     // For authenticated users (admins), allow access to any conversation
//     if ($user) {
//         $conversation = \App\Models\Conversation::find($conversationId);
//         if (!$conversation) {
//             \Log::warning('Channel authorization failed: conversation not found', [
//                 'conversation_id' => $conversationId,
//                 'user_id' => $user->id
//             ]);
//             return false;
//         }
        
//         \Log::info('Channel authorization granted for authenticated user', [
//             'conversation_id' => $conversationId,
//             'user_id' => $user->id
//         ]);
        
//         // Allow any authenticated user to access any conversation
//         // This allows admins to access all conversations
//         return true;
//     }
    
//     \Log::info('Channel authorization granted for unauthenticated user', [
//         'conversation_id' => $conversationId
//     ]);
    
//     // For unauthenticated users (customers), allow access
//     // This allows public users to join their own conversations
//     return true;
// });

// Private messages channel for internal chat
// Broadcast::channel('messages', function ($user) {
//     // Allow any authenticated user
//     return (int) $user->id > 0;
// });
