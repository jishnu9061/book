<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use App\Mail\SendMailFromHtml;
use App\Models\EmailTemplate;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Notifications\NewTicketNotification;
use Illuminate\Support\Facades\Notification;
use Exception;

class TicketCreatedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param TicketCreated $event
     * @return void
     */
    public function handle(TicketCreated $event): void
    {
        try {
            $data = $event->data;
            
            // Validate event data
            if (!$this->validateEventData($data)) {
                Log::warning('Invalid event data received in TicketCreatedNotification', ['data' => $data]);
                return;
            }

            $ticket = $this->getTicketWithRelations($data['ticket_id']);
            if (!$ticket) {
                Log::warning('Ticket not found in TicketCreatedNotification', ['ticket_id' => $data['ticket_id']]);
                return;
            }

            $notifications = $this->getEmailNotificationSettings();

            // Handle new customer ticket creation
            if (!empty($data['password'])) {
                $this->handleNewCustomerTicket($ticket, $data['password'], $notifications);
            }

            // Handle dashboard ticket creation
            $this->handleDashboardTicket($ticket, $notifications);

        } catch (Exception $e) {
            Log::error('Error in TicketCreatedNotification::handle', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event_data' => $event->data ?? null
            ]);
        }
    }

    /**
     * Validate event data structure
     *
     * @param array $data
     * @return bool
     */
    private function validateEventData(array $data): bool
    {
        return isset($data['ticket_id']) && is_numeric($data['ticket_id']);
    }

    /**
     * Get ticket with necessary relationships
     *
     * @param int $ticketId
     * @return Ticket|null
     */
    private function getTicketWithRelations(int $ticketId): ?Ticket
    {
        return Ticket::with(['user', 'ticketType'])->find($ticketId);
    }

    /**
     * Get email notification settings
     *
     * @return array
     */
    private function getEmailNotificationSettings(): array
    {
        try {
            return app('App\HelpDesk')->getSettingsEmailNotifications();
        } catch (Exception $e) {
            Log::error('Failed to get email notification settings', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Handle new customer ticket creation
     *
     * @param Ticket $ticket
     * @param string $password
     * @param array $notifications
     * @return void
     */
    private function handleNewCustomerTicket(Ticket $ticket, string $password, array $notifications): void
    {
        $ticketSlug = 'create_ticket_new_customer';
        $recipient = $ticket->user;

        if (!$recipient || empty($notifications[$ticketSlug])) {
            return;
        }

        $template = $this->getEmailTemplate($ticketSlug);
        if ($template) {
            $this->sendMailWithTemplate($template, $ticket, $recipient, $password);
        }
    }

    /**
     * Handle dashboard ticket creation
     *
     * @param Ticket $ticket
     * @param array $notifications
     * @return void
     */
    private function handleDashboardTicket(Ticket $ticket, array $notifications): void
    {
        $ticketSlug = 'create_ticket_dashboard';
        $recipient = $this->getRecipient($ticket);

        $this->sendInAppNotification($ticket);

        if (!$recipient || empty($notifications[$ticketSlug])) {
            return;
        }

        $template = $this->getEmailTemplate($ticketSlug);
        if ($template) {
            $this->sendMailWithTemplate($template, $ticket, $recipient, '');
        }
    }

    /**
     * Send in-app notifications to relevant users
     *
     * @param Ticket $ticket
     * @return void
     */
    private function sendInAppNotification(Ticket $ticket): void
    {
        $recipient = $this->getRecipient($ticket);

        // Notify the assigned recipient
        if ($recipient) {
            $this->sendNotificationToUser($recipient, $ticket);
        }

        // Notify all admins (excluding the recipient if they're an admin)
        $this->sendNotificationToAdmins($ticket, $recipient);
    }

    /**
     * Send notification to a specific user
     *
     * @param User $user
     * @param Ticket $ticket
     * @return void
     */
    private function sendNotificationToUser(User $user, Ticket $ticket): void
    {
        try {
            Notification::send($user, new NewTicketNotification($ticket));
        } catch (Exception $e) {
            Log::error('Failed to notify user', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification to all admins
     *
     * @param Ticket $ticket
     * @param User|null $excludeUser
     * @return void
     */
    private function sendNotificationToAdmins(Ticket $ticket, ?User $excludeUser = null): void
    {
        try {
            $admins = User::whereHas('role', function ($query) {
                $query->where('slug', 'like', '%admin%');
            })
            ->when($excludeUser, function ($query) use ($excludeUser) {
                $query->where('id', '<>', $excludeUser->id);
            })
            ->get();

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new NewTicketNotification($ticket));
            }
        } catch (Exception $e) {
            Log::error('Failed to notify admins', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get the appropriate recipient for the ticket
     *
     * @param Ticket $ticket
     * @return User|null
     */
    private function getRecipient(Ticket $ticket): ?User
    {
        try {
            // First, try to get the default recipient from settings
            $setting = Setting::where('slug', 'default_recipient')->first();
            if ($setting && $setting->value) {
                $user = User::find($setting->value);
                if ($user) {
                    return $user;
                }
            }

            // Fallback to first admin user
            $adminRole = Role::where('slug', 'like', '%admin%')->first();
            if ($adminRole) {
                return User::where('role_id', $adminRole->id)
                    ->orderBy('id')
                    ->first();
            }

            // Last resort: get any user
            return User::orderBy('role_id')->first();

        } catch (Exception $e) {
            Log::error('Failed to get recipient', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get email template by slug
     *
     * @param string $slug
     * @return EmailTemplate|null
     */
    private function getEmailTemplate(string $slug): ?EmailTemplate
    {
        try {
            return EmailTemplate::where('slug', $slug)->first();
        } catch (Exception $e) {
            Log::error('Failed to get email template', [
                'slug' => $slug,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Send email with template processing
     *
     * @param EmailTemplate $template
     * @param Ticket $ticket
     * @param User $user
     * @param string $password
     * @return void
     */
    private function sendMailWithTemplate(EmailTemplate $template, Ticket $ticket, User $user, string $password = ''): void
    {
        try {
            $processedTemplate = $this->processEmailTemplate($template->html, $ticket, $user, $password);
            $subject = '[Ticket#' . $ticket->uid . '] - ' . $ticket->subject;
            
            $messageData = [
                'html' => $processedTemplate,
                'subject' => $subject
            ];

            $mailable = new SendMailFromHtml($messageData);

            if (config('queue.enable')) {
                Mail::to($user->email)->queue($mailable);
            } else {
                Mail::to($user->email)->send($mailable);
            }

            Log::info('Email sent successfully', [
                'ticket_id' => $ticket->id,
                'recipient' => $user->email,
                'template_slug' => $template->slug
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send email', [
                'ticket_id' => $ticket->id,
                'recipient' => $user->email,
                'template_slug' => $template->slug,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process email template with variables
     *
     * @param string $template
     * @param Ticket $ticket
     * @param User $user
     * @param string $password
     * @return string
     */
    private function processEmailTemplate(string $template, Ticket $ticket, User $user, string $password = ''): string
    {
        $variables = [
            'name' => $user->first_name ?? $user->name ?? 'User',
            'email' => $user->email,
            'password' => $password,
            'url' => config('app.url') . '/dashboard/tickets/' . $ticket->uid,
            'sender_name' => 'Manager',
            'ticket_id' => $ticket->id,
            'uid' => $ticket->uid,
            'subject' => $ticket->subject,
            'type' => $ticket->ticketType ? $ticket->ticketType->name : 'General',
        ];

        // Process template variables using a more robust approach
        if (preg_match_all("/\{([^}]+)\}/", $template, $matches)) {
            foreach ($matches[1] as $index => $variableName) {
                $placeholder = $matches[0][$index];
                $value = $variables[$variableName] ?? '';
                
                // Handle sprintf-style formatting if present
                if (strpos($value, '%') !== false) {
                    $template = str_replace($placeholder, sprintf($value, $variableName), $template);
                } else {
                    $template = str_replace($placeholder, $value, $template);
                }
            }
        }

        return $template;
    }
}
