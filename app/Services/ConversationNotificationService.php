<?php

namespace App\Services;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Services\EmailService;
use App\Services\BroadcastService;
use App\Services\TemplateService;
use App\Notifications\ConversationNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Collection;
use Illuminate\Broadcasting\BroadcastException;
use Exception;

/**
 * Conversation Notification Service
 * 
 * This service handles all conversation-related notifications including
 * conversation creation, new messages, and participant additions.
 */
class ConversationNotificationService
{
    protected EmailService $emailService;
    protected BroadcastService $broadcastService;
    protected TemplateService $templateService;

    public function __construct(
        EmailService $emailService,
        BroadcastService $broadcastService,
        TemplateService $templateService
    ) {
        $this->emailService = $emailService;
        $this->broadcastService = $broadcastService;
        $this->templateService = $templateService;
    }

    /**
     * Send notification for conversation creation
     */
    public function sendConversationCreatedNotification(Conversation $conversation, array $participants, $creator = null): void
    {
        try {
            Log::info('Sending conversation created notifications', [
                'conversation_id' => $conversation->id,
                'participants_count' => count($participants),
                'creator_id' => $creator?->id
            ]);

            // Get conversation with relationships
            $conversation->load(['ticket', 'participants.user']);

            // Send notifications to all participants except creator
            foreach ($participants as $participant) {
                if ($creator && $participant['user_id'] === $creator->id) {
                    continue; // Skip creator
                }

                $user = User::find($participant['user_id']);
                if (!$user) {
                    continue;
                }

                $this->sendConversationCreatedNotificationToUser($conversation, $user, $creator);
            }

        } catch (Exception $e) {
            Log::error('Failed to send conversation created notification', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send notification for new message in conversation
     */
    public function sendNewMessageNotification(Message $message): void
    {
        try {
            Log::info('Sending new message notifications', [
                'message_id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->user_id
            ]);

            // Get conversation with participants
            $conversation = $message->conversation()->with(['participants.user', 'ticket'])->first();
            if (!$conversation) {
                Log::warning('Conversation not found for message notification', [
                    'message_id' => $message->id,
                    'conversation_id' => $message->conversation_id
                ]);
                return;
            }

            // Send notifications to all participants except sender
            foreach ($conversation->participants as $participant) {
                if ($participant->user_id === $message->user_id) {
                    continue; // Skip sender
                }

                $user = $participant->user;
                if (!$user) {
                    continue;
                }

                $this->sendNewMessageNotificationToUser($message, $conversation, $user);
            }

        } catch (Exception $e) {
            Log::error('Failed to send new message notification', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send notification when user is added to conversation
     */
    public function sendParticipantAddedNotification(Conversation $conversation, User $user, $addedBy = null): void
    {
        try {
            Log::info('Sending participant added notification', [
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'added_by' => $addedBy?->id
            ]);

            $conversation->load(['ticket', 'participants.user']);

            $this->sendParticipantAddedNotificationToUser($conversation, $user, $addedBy);

        } catch (Exception $e) {
            Log::error('Failed to send participant added notification', [
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send conversation created notification to specific user
     */
    private function sendConversationCreatedNotificationToUser(Conversation $conversation, User $user, $creator = null): void
    {
        try {
            // Send in-app notification (database channel will always work)
            // Broadcast channel will be attempted but won't fail if Pusher has issues
            try {
                Notification::send($user, new ConversationNotification(
                    $conversation,
                    'conversation_created',
                    [
                        'conversation_id' => $conversation->id,
                        'conversation_type' => $conversation->type,
                        'ticket_uid' => $conversation->ticket?->uid,
                        'creator_name' => $creator ? $creator->first_name . ' ' . $creator->last_name : 'System',
                        'participants_count' => $conversation->participants->count()
                    ]
                ));
            } catch (\Illuminate\Broadcasting\BroadcastException $e) {
                // If broadcast fails (e.g., Pusher error), log it but continue
                // The database notification should still work
                Log::warning('Broadcast failed for conversation notification, but database notification succeeded', [
                    'conversation_id' => $conversation->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            } catch (\Exception $e) {
                // For other errors, log and continue
                Log::warning('Notification sending encountered an error (non-critical)', [
                    'conversation_id' => $conversation->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Send email notification if enabled (this is independent of broadcast)
            try {
                if ($this->shouldSendEmailNotification($user, 'conversation_created')) {
                    $this->sendConversationCreatedEmail($conversation, $user, $creator);
                }
            } catch (Exception $e) {
                Log::warning('Failed to send email notification (non-critical)', [
                    'conversation_id' => $conversation->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }

        } catch (Exception $e) {
            Log::error('Failed to send conversation created notification to user', [
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send new message notification to specific user
     */
    private function sendNewMessageNotificationToUser(Message $message, Conversation $conversation, User $user): void
    {
        try {
            // Send in-app notification
            Notification::send($user, new ConversationNotification(
                $conversation,
                'new_message',
                [
                    'conversation_id' => $conversation->id,
                    'message_id' => $message->id,
                    'message_preview' => substr($message->message, 0, 100),
                    'sender_name' => $message->user ? $message->user->first_name . ' ' . $message->user->last_name : 'Unknown',
                    'ticket_uid' => $conversation->ticket?->uid,
                    'conversation_type' => $conversation->type
                ]
            ));

            // Send email notification if enabled
            if ($this->shouldSendEmailNotification($user, 'new_message')) {
                $this->sendNewMessageEmail($message, $conversation, $user);
            }

        } catch (Exception $e) {
            Log::error('Failed to send new message notification to user', [
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'message_id' => $message->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send participant added notification to specific user
     */
    private function sendParticipantAddedNotificationToUser(Conversation $conversation, User $user, $addedBy = null): void
    {
        try {
            // Send in-app notification
            Notification::send($user, new ConversationNotification(
                $conversation,
                'participant_added',
                [
                    'conversation_id' => $conversation->id,
                    'conversation_type' => $conversation->type,
                    'ticket_uid' => $conversation->ticket?->uid,
                    'added_by_name' => $addedBy ? $addedBy->first_name . ' ' . $addedBy->last_name : 'System',
                    'participants_count' => $conversation->participants->count()
                ]
            ));

            // Send email notification if enabled
            if ($this->shouldSendEmailNotification($user, 'participant_added')) {
                $this->sendParticipantAddedEmail($conversation, $user, $addedBy);
            }

        } catch (Exception $e) {
            Log::error('Failed to send participant added notification to user', [
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send conversation created email
     */
    private function sendConversationCreatedEmail(Conversation $conversation, User $user, $creator = null): void
    {
        try {
            $template = $this->getEmailTemplate('conversation_created');
            if (!$template) {
                Log::warning('Email template not found for conversation_created');
                return;
            }

            $data = [
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'conversation_type' => $conversation->type,
                'ticket_uid' => $conversation->ticket?->uid ?? 'N/A',
                'ticket_subject' => $conversation->ticket?->subject ?? 'N/A',
                'creator_name' => $creator ? $creator->first_name . ' ' . $creator->last_name : 'System',
                'conversation_url' => route('conversations.view', $conversation->id),
                'app_name' => config('app.name', 'HelpDesk')
            ];

            $this->emailService->sendTemplateEmail($user, $template, $data);

        } catch (Exception $e) {
            Log::error('Failed to send conversation created email', [
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send new message email
     */
    private function sendNewMessageEmail(Message $message, Conversation $conversation, User $user): void
    {
        try {
            $template = $this->getEmailTemplate('conversation_new_message');
            if (!$template) {
                Log::warning('Email template not found for conversation_new_message');
                return;
            }

            $data = [
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'sender_name' => $message->user ? $message->user->first_name . ' ' . $message->user->last_name : 'Unknown',
                'message_preview' => substr($message->message, 0, 200),
                'conversation_type' => $conversation->type,
                'ticket_uid' => $conversation->ticket?->uid ?? 'N/A',
                'ticket_subject' => $conversation->ticket?->subject ?? 'N/A',
                'conversation_url' => route('conversations.view', $conversation->id),
                'app_name' => config('app.name', 'HelpDesk')
            ];

            $this->emailService->sendTemplateEmail($user, $template, $data);

        } catch (Exception $e) {
            Log::error('Failed to send new message email', [
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'message_id' => $message->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send participant added email
     */
    private function sendParticipantAddedEmail(Conversation $conversation, User $user, $addedBy = null): void
    {
        try {
            $template = $this->getEmailTemplate('conversation_participant_added');
            if (!$template) {
                Log::warning('Email template not found for conversation_participant_added');
                return;
            }

            $data = [
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'added_by_name' => $addedBy ? $addedBy->first_name . ' ' . $addedBy->last_name : 'System',
                'conversation_type' => $conversation->type,
                'ticket_uid' => $conversation->ticket?->uid ?? 'N/A',
                'ticket_subject' => $conversation->ticket?->subject ?? 'N/A',
                'conversation_url' => route('conversations.view', $conversation->id),
                'app_name' => config('app.name', 'HelpDesk')
            ];

            $this->emailService->sendTemplateEmail($user, $template, $data);

        } catch (Exception $e) {
            Log::error('Failed to send participant added email', [
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if email notification should be sent
     */
    private function shouldSendEmailNotification(User $user, string $type): bool
    {
        // Check user preferences (if implemented)
        // For now, always send email notifications
        return true;
    }

    /**
     * Get email template
     */
    private function getEmailTemplate(string $slug): ?EmailTemplate
    {
        return EmailTemplate::where('slug', $slug)->first();
    }
}





