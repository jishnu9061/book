<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * New User Created Listener
 * 
 * Handles user creation notifications using the centralized notification service.
 */
class NewUserCreatedListener
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        try {
            $data = $event->data;
            
            // Validate event data
            if (!$this->validateEventData($data)) {
                Log::warning('Invalid event data received in NewUserCreatedListener', [
                    'data' => $data
                ]);
                return;
            }

            // Get user
            $user = \App\Models\User::find($data['user_id']);
            if (!$user) {
                Log::warning('User not found in NewUserCreatedListener', [
                    'user_id' => $data['user_id']
                ]);
                return;
            }

            $password = $data['password'] ?? '';

            // Send notification using centralized service
            $this->notificationService->sendUserCreatedNotification($user, $password);

        } catch (Exception $e) {
            Log::error('Error in NewUserCreatedListener::handle', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event_data' => $event->data ?? null
            ]);
        }
    }

    /**
     * Validate event data structure
     */
    protected function validateEventData(array $data): bool
    {
        return isset($data['user_id']) && is_numeric($data['user_id']);
    }
}
