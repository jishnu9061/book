<?php

namespace App\Listeners;

use App\Events\NewChatMessage;
use App\Services\ConversationNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Exception;

class SendChatMessageNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected ConversationNotificationService $conversationNotificationService;

    /**
     * Create the event listener.
     */
    public function __construct(ConversationNotificationService $conversationNotificationService)
    {
        $this->conversationNotificationService = $conversationNotificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(NewChatMessage $event): void
    {
        try {
            Log::info('SendChatMessageNotification: Processing new message notification', [
                'message_id' => $event->chatMessage->id,
                'conversation_id' => $event->chatMessage->conversation_id,
                'user_id' => $event->chatMessage->user_id
            ]);

            // Send notifications for the new message
            $this->conversationNotificationService->sendNewMessageNotification($event->chatMessage);

            Log::info('SendChatMessageNotification: Successfully processed message notification', [
                'message_id' => $event->chatMessage->id,
                'conversation_id' => $event->chatMessage->conversation_id
            ]);

        } catch (Exception $e) {
            Log::error('SendChatMessageNotification: Failed to process message notification', [
                'message_id' => $event->chatMessage->id,
                'conversation_id' => $event->chatMessage->conversation_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(NewChatMessage $event, $exception): void
    {
        Log::error('SendChatMessageNotification: Job failed', [
            'message_id' => $event->chatMessage->id,
            'conversation_id' => $event->chatMessage->conversation_id,
            'error' => $exception->getMessage()
        ]);
    }
}
