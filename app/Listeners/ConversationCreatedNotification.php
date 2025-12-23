<?php

namespace App\Listeners;

use App\Events\ConversationCreated;
use App\Services\ConversationNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Exception;

class ConversationCreatedNotification implements ShouldQueue
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
    public function handle(ConversationCreated $event): void
    {
        try {
            Log::info('ConversationCreatedNotification: Processing conversation created notification', [
                'conversation_id' => $event->conversation->id,
                'conversation_type' => $event->conversation->type,
                'participants_count' => count($event->participants),
                'creator_id' => $event->creator?->id
            ]);

            // Send notifications for the new conversation
            $this->conversationNotificationService->sendConversationCreatedNotification(
                $event->conversation,
                $event->participants,
                $event->creator
            );

            Log::info('ConversationCreatedNotification: Successfully processed conversation created notification', [
                'conversation_id' => $event->conversation->id,
                'participants_count' => count($event->participants)
            ]);

        } catch (Exception $e) {
            Log::error('ConversationCreatedNotification: Failed to process conversation created notification', [
                'conversation_id' => $event->conversation->id,
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
    public function failed(ConversationCreated $event, $exception): void
    {
        Log::error('ConversationCreatedNotification: Job failed', [
            'conversation_id' => $event->conversation->id,
            'error' => $exception->getMessage()
        ]);
    }
}





