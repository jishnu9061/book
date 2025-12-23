<?php

namespace App\Services;

use App\Models\User;
use App\Models\EmailTemplate;
use App\Mail\SendMailFromHtml;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Exception;

/**
 * Email Service
 * 
 * Handles all email-related operations including template processing,
 * queue management, and delivery tracking.
 */
class EmailService
{
    protected TemplateService $templateService;

    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Send email using template
     */
    public function sendTemplateEmail(User $user, EmailTemplate $template, array $data = []): void
    {
        try {
            $processedTemplate = $this->templateService->processTemplate($template->html, $data);
            $subject = $this->generateEmailSubject($template, $data);
            
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
                'recipient' => $user->email,
                'template_slug' => $template->slug,
                'queued' => config('queue.enable')
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send email', [
                'recipient' => $user->email,
                'template_slug' => $template->slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Attempt to retry if queued
            if (config('queue.enable')) {
                $this->retryFailedEmail($user, $template, $data);
            }
        }
    }

    /**
     * Send bulk emails
     */
    public function sendBulkEmails(iterable $users, EmailTemplate $template, array $data = []): void
    {
        $successCount = 0;
        $failureCount = 0;

        foreach ($users as $user) {
            try {
                $this->sendTemplateEmail($user, $template, $data);
                $successCount++;
            } catch (Exception $e) {
                $failureCount++;
                Log::error('Bulk email failed for user', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Bulk email operation completed', [
            'template_slug' => $template->slug,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'total_count' => $successCount + $failureCount
        ]);
    }

    /**
     * Send test email
     */
    public function sendTestEmail(string $email, string $templateSlug = 'custom_mail'): bool
    {
        try {
            $template = EmailTemplate::where('slug', $templateSlug)->first();
            if (!$template) {
                throw new Exception("Template not found: {$templateSlug}");
            }

            $testData = [
                'name' => 'Test User',
                'email' => $email,
                'url' => config('app.url'),
                'sender_name' => 'HelpDesk System',
                'ticket_id' => 'TEST-001',
                'uid' => 'TEST-001',
                'subject' => 'Test Email',
                'type' => 'Test',
                'status' => 'Open',
                'priority' => 'Normal',
                'department' => 'General',
            ];

            $processedTemplate = $this->templateService->processTemplate($template->html, $testData);
            $subject = '[TEST] ' . $this->generateEmailSubject($template, $testData);
            
            $messageData = [
                'html' => $processedTemplate,
                'subject' => $subject
            ];

            Mail::to($email)->send(new SendMailFromHtml($messageData));

            Log::info('Test email sent successfully', [
                'recipient' => $email,
                'template_slug' => $templateSlug
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Failed to send test email', [
                'recipient' => $email,
                'template_slug' => $templateSlug,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Validate email configuration
     */
    public function validateEmailConfiguration(): array
    {
        $config = [
            'mail_mailer' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
        ];

        $issues = [];
        
        if (empty($config['mail_host'])) {
            $issues[] = 'Mail host is not configured';
        }
        
        if (empty($config['mail_username'])) {
            $issues[] = 'Mail username is not configured';
        }
        
        if (empty($config['mail_from_address'])) {
            $issues[] = 'Mail from address is not configured';
        }

        return [
            'valid' => empty($issues),
            'config' => $config,
            'issues' => $issues
        ];
    }

    /**
     * Get email queue status
     */
    public function getQueueStatus(): array
    {
        try {
            $queueEnabled = config('queue.enable');
            $queueConnection = config('queue.default');
            
            $status = [
                'enabled' => $queueEnabled,
                'connection' => $queueConnection,
                'failed_jobs_count' => 0,
                'pending_jobs_count' => 0
            ];

            if ($queueEnabled) {
                // Get failed jobs count
                $status['failed_jobs_count'] = \DB::table('failed_jobs')->count();
                
                // Get pending jobs count (this depends on your queue driver)
                if ($queueConnection === 'database') {
                    $status['pending_jobs_count'] = \DB::table('jobs')->count();
                }
            }

            return $status;

        } catch (Exception $e) {
            Log::error('Failed to get queue status', ['error' => $e->getMessage()]);
            return [
                'enabled' => false,
                'connection' => 'unknown',
                'failed_jobs_count' => 0,
                'pending_jobs_count' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate email subject
     */
    protected function generateEmailSubject(EmailTemplate $template, array $data): string
    {
        $defaultSubject = 'HelpDesk Notification';
        
        // Try to extract subject from template or use default
        if (isset($data['uid']) && isset($data['subject'])) {
            return '[Ticket#' . $data['uid'] . '] - ' . $data['subject'];
        }
        
        if (isset($data['subject'])) {
            return $data['subject'];
        }
        
        return $defaultSubject;
    }

    /**
     * Retry failed email
     */
    protected function retryFailedEmail(User $user, EmailTemplate $template, array $data): void
    {
        try {
            // Implement retry logic here
            // This could involve adding to a retry queue or sending immediately
            Log::info('Retrying failed email', [
                'recipient' => $user->email,
                'template_slug' => $template->slug
            ]);
            
            // For now, just log the retry attempt
            // In a production system, you might want to implement exponential backoff
            
        } catch (Exception $e) {
            Log::error('Failed to retry email', [
                'recipient' => $user->email,
                'template_slug' => $template->slug,
                'error' => $e->getMessage()
            ]);
        }
    }
}
