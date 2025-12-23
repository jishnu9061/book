<?php

namespace App\Services;

use App\Models\User;
use App\Models\Ticket;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Services\EmailService;
use App\Services\BroadcastService;
use App\Services\TemplateService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Collection;
use Exception;

/**
 * Centralized Notification Service
 * 
 * This service handles all notification operations including email,
 * in-app notifications, and real-time broadcasting.
 */
class NotificationService
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
     * Send notification for ticket creation
     */
    public function sendTicketCreatedNotification(Ticket $ticket, array $data = []): void
    {
        try {
            $notificationData = $this->prepareTicketNotificationData($ticket, $data);
            
            // Handle new customer ticket
            if (!empty($data['password'])) {
                $this->handleNewCustomerTicket($ticket, $data['password'], $notificationData);
            }

            // Handle dashboard ticket
            $this->handleDashboardTicket($ticket, $notificationData);

        } catch (Exception $e) {
            Log::error('Failed to send ticket created notification', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send notification for ticket updates
     */
    public function sendTicketUpdatedNotification(Ticket $ticket, array $changes = []): void
    {
        try {
            $notificationData = $this->prepareTicketNotificationData($ticket, $changes);
            
            if (!$this->isNotificationEnabled('ticket_updated')) {
                return;
            }

            $recipients = $this->getTicketRecipients($ticket);
            $template = $this->templateService->getTemplate('ticket_updated');

            foreach ($recipients as $recipient) {
                $this->sendNotificationToUser($recipient, $ticket, $template, $notificationData);
            }

        } catch (Exception $e) {
            Log::error('Failed to send ticket updated notification', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification for ticket assignment
     */
    public function sendTicketAssignedNotification(Ticket $ticket, User $assignedUser): void
    {
        try {
            if (!$this->isNotificationEnabled('assigned_ticket')) {
                return;
            }

            $notificationData = $this->prepareTicketNotificationData($ticket);
            $template = $this->templateService->getTemplate('assigned_ticket');

            $this->sendNotificationToUser($assignedUser, $ticket, $template, $notificationData);

        } catch (Exception $e) {
            Log::error('Failed to send ticket assigned notification', [
                'ticket_id' => $ticket->id,
                'assigned_user_id' => $assignedUser->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification for new comments
     */
    public function sendTicketCommentNotification(Ticket $ticket, string $comment, User $commenter): void
    {
        try {
            if (!$this->isNotificationEnabled('ticket_new_comment')) {
                return;
            }

            $notificationData = $this->prepareTicketNotificationData($ticket, ['comment' => $comment]);
            $recipients = $this->getTicketRecipients($ticket, [$commenter->id]);
            $template = $this->templateService->getTemplate('ticket_new_comment');

            foreach ($recipients as $recipient) {
                $this->sendNotificationToUser($recipient, $ticket, $template, $notificationData);
            }

        } catch (Exception $e) {
            Log::error('Failed to send ticket comment notification', [
                'ticket_id' => $ticket->id,
                'commenter_id' => $commenter->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification for user creation
     */
    public function sendUserCreatedNotification(User $user, string $password = ''): void
    {
        try {
            if (!$this->isNotificationEnabled('user_created')) {
                return;
            }

            $notificationData = [
                'name' => $user->first_name ?? $user->name ?? 'User',
                'email' => $user->email,
                'password' => $password,
                'url' => config('app.url') . '/login',
                'sender_name' => 'System Administrator',
            ];

            $template = $this->templateService->getTemplate('user_created');
            $this->sendNotificationToUser($user, null, $template, $notificationData);

        } catch (Exception $e) {
            Log::error('Failed to send user created notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send custom notification
     */
    public function sendCustomNotification(
        Collection $recipients,
        string $templateSlug,
        array $data = [],
        array $channels = ['email', 'database', 'broadcast']
    ): void {
        try {
            $template = $this->templateService->getTemplate($templateSlug);
            
            foreach ($recipients as $recipient) {
                $this->sendNotificationToUser($recipient, null, $template, $data, $channels);
            }

        } catch (Exception $e) {
            Log::error('Failed to send custom notification', [
                'template_slug' => $templateSlug,
                'recipient_count' => $recipients->count(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle new customer ticket creation
     */
    protected function handleNewCustomerTicket(Ticket $ticket, string $password, array $notificationData): void
    {
        if (!$this->isNotificationEnabled('create_ticket_new_customer') || !$ticket->user) {
            return;
        }

        $notificationData['password'] = $password;
        $template = $this->templateService->getTemplate('create_ticket_new_customer');
        
        $this->sendNotificationToUser($ticket->user, $ticket, $template, $notificationData);
    }

    /**
     * Handle dashboard ticket creation
     */
    protected function handleDashboardTicket(Ticket $ticket, array $notificationData): void
    {
        if (!$this->isNotificationEnabled('create_ticket_dashboard')) {
            return;
        }

        $recipients = $this->getTicketRecipients($ticket);
        $template = $this->templateService->getTemplate('create_ticket_dashboard');

        foreach ($recipients as $recipient) {
            $this->sendNotificationToUser($recipient, $ticket, $template, $notificationData);
        }
    }

    /**
     * Send notification to a specific user through all enabled channels
     */
    protected function sendNotificationToUser(
        User $user,
        ?Ticket $ticket,
        ?EmailTemplate $template,
        array $data = [],
        array $channels = ['email', 'database', 'broadcast']
    ): void {
        try {
            // Send in-app notification
            if (in_array('database', $channels) && $ticket) {
                $this->sendInAppNotification($user, $ticket);
            }

            // Send email notification
            if (in_array('email', $channels) && $template) {
                $this->emailService->sendTemplateEmail($user, $template, $data);
            }

            // Send broadcast notification
            if (in_array('broadcast', $channels) && $ticket) {
                $this->broadcastService->sendTicketNotification($user, $ticket);
            }

        } catch (Exception $e) {
            Log::error('Failed to send notification to user', [
                'user_id' => $user->id,
                'ticket_id' => $ticket?->id,
                'template_slug' => $template?->slug,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send in-app notification
     */
    protected function sendInAppNotification(User $user, Ticket $ticket): void
    {
        try {
            $notification = new \App\Notifications\TicketNotification($ticket);
            Notification::send($user, $notification);
        } catch (Exception $e) {
            Log::error('Failed to send in-app notification', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get recipients for a ticket
     */
    protected function getTicketRecipients(Ticket $ticket, array $excludeUserIds = []): Collection
    {
        $recipients = collect();

        // Add assigned user
        if ($ticket->assignedTo && !in_array($ticket->assignedTo->id, $excludeUserIds)) {
            $recipients->push($ticket->assignedTo);
        }

        // Add ticket creator
        if ($ticket->user && !in_array($ticket->user->id, $excludeUserIds)) {
            $recipients->push($ticket->user);
        }

        // Add default recipient if no one is assigned
        if ($recipients->isEmpty()) {
            $defaultRecipient = $this->getDefaultRecipient();
            if ($defaultRecipient) {
                $recipients->push($defaultRecipient);
            }
        }

        return $recipients->unique('id');
    }

    /**
     * Get default recipient for notifications
     */
    protected function getDefaultRecipient(): ?User
    {
        try {
            // Try to get default recipient from settings
            $setting = Setting::where('slug', 'default_recipient')->first();
            if ($setting && $setting->value) {
                $user = User::find($setting->value);
                if ($user) {
                    return $user;
                }
            }

            // Fallback to first admin user
            $adminRole = \App\Models\Role::where('slug', 'like', '%admin%')->first();
            if ($adminRole) {
                return User::where('role_id', $adminRole->id)
                    ->orderBy('id')
                    ->first();
            }

            // Last resort: get any user
            return User::orderBy('role_id')->first();

        } catch (Exception $e) {
            Log::error('Failed to get default recipient', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Prepare notification data for tickets
     */
    protected function prepareTicketNotificationData(Ticket $ticket, array $additionalData = []): array
    {
        return array_merge([
            'name' => $ticket->user?->first_name ?? $ticket->user?->name ?? 'User',
            'email' => $ticket->user?->email ?? '',
            'url' => config('app.url') . '/dashboard/tickets/' . $ticket->uid,
            'sender_name' => 'HelpDesk System',
            'ticket_id' => $ticket->id,
            'uid' => $ticket->uid,
            'subject' => $ticket->subject,
            'type' => $ticket->ticketType?->name ?? 'General',
            'status' => $ticket->status?->name ?? 'Open',
            'priority' => $ticket->priority?->name ?? 'Normal',
            'department' => $ticket->department?->name ?? 'General',
        ], $additionalData);
    }

    /**
     * Check if a notification type is enabled
     */
    protected function isNotificationEnabled(string $notificationType): bool
    {
        try {
            $settings = $this->getNotificationSettings();
            return $settings[$notificationType] ?? false;
        } catch (Exception $e) {
            Log::error('Failed to check notification settings', [
                'notification_type' => $notificationType,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get notification settings
     */
    protected function getNotificationSettings(): array
    {
        try {
            $setting = Setting::where('slug', 'email_notifications')->first();
            if (!$setting || !$setting->value) {
                return [];
            }

            $settings = json_decode($setting->value, true);
            $notifications = [];
            
            foreach ($settings as $setting) {
                $notifications[$setting['slug']] = $setting['value'];
            }

            return $notifications;
        } catch (Exception $e) {
            Log::error('Failed to get notification settings', ['error' => $e->getMessage()]);
            return [];
        }
    }
}
