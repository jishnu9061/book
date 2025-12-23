<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use App\Services\EmailService;
use App\Services\BroadcastService;
use App\Services\TemplateService;
use App\Models\User;
use App\Models\Ticket;
use App\Models\EmailTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Notification Test Command
 * 
 * Provides comprehensive testing and management tools for the notification system.
 */
class NotificationTestCommand extends Command
{
    protected $signature = 'notification:test 
                            {--test-email : Test email configuration}
                            {--test-broadcast : Test broadcasting configuration}
                            {--test-template= : Test specific email template}
                            {--send-test-email= : Send test email to specific address}
                            {--validate-templates : Validate all email templates}
                            {--preview-template= : Preview specific template with sample data}
                            {--status : Show notification system status}';

    protected $description = 'Test and manage the notification system';

    protected NotificationService $notificationService;
    protected EmailService $emailService;
    protected BroadcastService $broadcastService;
    protected TemplateService $templateService;

    public function __construct(
        NotificationService $notificationService,
        EmailService $emailService,
        BroadcastService $broadcastService,
        TemplateService $templateService
    ) {
        parent::__construct();
        $this->notificationService = $notificationService;
        $this->emailService = $emailService;
        $this->broadcastService = $broadcastService;
        $this->templateService = $templateService;
    }

    public function handle(): int
    {
        $this->info('🔔 HelpDesk Notification System Test');
        $this->newLine();

        // Test email configuration
        if ($this->option('test-email')) {
            $this->testEmailConfiguration();
        }

        // Test broadcast configuration
        if ($this->option('test-broadcast')) {
            $this->testBroadcastConfiguration();
        }

        // Test specific template
        if ($templateSlug = $this->option('test-template')) {
            $this->testTemplate($templateSlug);
        }

        // Send test email
        if ($email = $this->option('send-test-email')) {
            $this->sendTestEmail($email);
        }

        // Validate templates
        if ($this->option('validate-templates')) {
            $this->validateTemplates();
        }

        // Preview template
        if ($templateSlug = $this->option('preview-template')) {
            $this->previewTemplate($templateSlug);
        }

        // Show status
        if ($this->option('status')) {
            $this->showSystemStatus();
        }

        // If no specific options, show interactive menu
        if (!$this->hasAnyOptions()) {
            $this->showInteractiveMenu();
        }

        return 0;
    }

    protected function testEmailConfiguration(): void
    {
        $this->info('📧 Testing Email Configuration...');
        
        $validation = $this->emailService->validateEmailConfiguration();
        $queueStatus = $this->emailService->getQueueStatus();

        if ($validation['valid']) {
            $this->info('✅ Email configuration is valid');
        } else {
            $this->error('❌ Email configuration has issues:');
            foreach ($validation['issues'] as $issue) {
                $this->line("   • {$issue}");
            }
        }

        $this->newLine();
        $this->info('📊 Email Configuration:');
        foreach ($validation['config'] as $key => $value) {
            $displayValue = $key === 'mail_username' && !empty($value) ? '***configured***' : $value;
            $this->line("   {$key}: {$displayValue}");
        }

        $this->newLine();
        $this->info('📋 Queue Status:');
        $this->line("   Enabled: " . ($queueStatus['enabled'] ? 'Yes' : 'No'));
        $this->line("   Connection: {$queueStatus['connection']}");
        $this->line("   Failed Jobs: {$queueStatus['failed_jobs_count']}");
        $this->line("   Pending Jobs: {$queueStatus['pending_jobs_count']}");
    }

    protected function testBroadcastConfiguration(): void
    {
        $this->info('📡 Testing Broadcast Configuration...');
        
        $status = $this->broadcastService->getBroadcastingStatus();
        $testResult = $this->broadcastService->testBroadcastingConnection();

        if ($status['enabled']) {
            $this->info('✅ Broadcasting is enabled and configured');
        } else {
            $this->error('❌ Broadcasting has issues:');
            foreach ($status['issues'] as $issue) {
                $this->line("   • {$issue}");
            }
        }

        $this->newLine();
        $this->info('📊 Broadcast Configuration:');
        foreach ($status['config'] as $key => $value) {
            $this->line("   {$key}: {$value}");
        }

        $this->newLine();
        $this->info('🧪 Connection Test:');
        if ($testResult['success']) {
            $this->info("   ✅ {$testResult['message']}");
        } else {
            $this->error("   ❌ {$testResult['message']}");
        }
    }

    protected function testTemplate(string $templateSlug): void
    {
        $this->info("📄 Testing Template: {$templateSlug}");
        
        $template = $this->templateService->getTemplate($templateSlug);
        
        if (!$template) {
            $this->error("❌ Template not found: {$templateSlug}");
            return;
        }

        $this->info("✅ Template found: {$template->name}");
        
        $validation = $this->templateService->validateTemplate($template->html);
        
        if ($validation['valid']) {
            $this->info('✅ Template syntax is valid');
        } else {
            $this->error('❌ Template has issues:');
            foreach ($validation['issues'] as $issue) {
                $this->line("   • {$issue}");
            }
        }

        $variables = $this->templateService->extractUsedVariables($template->html);
        $this->info("📝 Variables used: " . implode(', ', $variables));
    }

    protected function sendTestEmail(string $email): void
    {
        $this->info("📧 Sending test email to: {$email}");
        
        $success = $this->emailService->sendTestEmail($email);
        
        if ($success) {
            $this->info('✅ Test email sent successfully');
        } else {
            $this->error('❌ Failed to send test email');
        }
    }

    protected function validateTemplates(): void
    {
        $this->info('📄 Validating All Email Templates...');
        
        $templates = $this->templateService->getAllTemplates();
        $validCount = 0;
        $invalidCount = 0;

        foreach ($templates as $template) {
            $this->line("   {$template['name']} ({$template['slug']})");
            
            if ($template['validation']['valid']) {
                $this->info("      ✅ Valid");
                $validCount++;
            } else {
                $this->error("      ❌ Invalid:");
                foreach ($template['validation']['issues'] as $issue) {
                    $this->line("         • {$issue}");
                }
                $invalidCount++;
            }
        }

        $this->newLine();
        $this->info("📊 Validation Summary:");
        $this->line("   Valid: {$validCount}");
        $this->line("   Invalid: {$invalidCount}");
        $this->line("   Total: " . count($templates));
    }

    protected function previewTemplate(string $templateSlug): void
    {
        $this->info("👁️  Previewing Template: {$templateSlug}");
        
        $preview = $this->templateService->previewTemplate($templateSlug);
        
        if (!$preview['success']) {
            $this->error("❌ {$preview['message']}");
            return;
        }

        $this->info('✅ Template preview generated');
        $this->newLine();
        
        $this->info('📝 Variables used:');
        foreach ($preview['variables_used'] as $variable) {
            $this->line("   • {$variable}");
        }

        $this->newLine();
        $this->info('📄 Preview HTML (first 500 characters):');
        $this->line(substr($preview['template'], 0, 500) . '...');
    }

    protected function showSystemStatus(): void
    {
        $this->info('📊 Notification System Status');
        $this->newLine();

        // Email status
        $emailValidation = $this->emailService->validateEmailConfiguration();
        $emailQueue = $this->emailService->getQueueStatus();
        
        $this->info('📧 Email System:');
        $this->line("   Status: " . ($emailValidation['valid'] ? '✅ Ready' : '❌ Issues'));
        $this->line("   Queue: " . ($emailQueue['enabled'] ? '✅ Enabled' : '❌ Disabled'));
        $this->line("   Failed Jobs: {$emailQueue['failed_jobs_count']}");

        // Broadcast status
        $broadcastStatus = $this->broadcastService->getBroadcastingStatus();
        
        $this->newLine();
        $this->info('📡 Broadcast System:');
        $this->line("   Status: " . ($broadcastStatus['enabled'] ? '✅ Ready' : '❌ Issues'));

        // Template status
        $templates = $this->templateService->getAllTemplates();
        $validTemplates = collect($templates)->where('validation.valid', true)->count();
        
        $this->newLine();
        $this->info('📄 Templates:');
        $this->line("   Total: " . count($templates));
        $this->line("   Valid: {$validTemplates}");
        $this->line("   Invalid: " . (count($templates) - $validTemplates));
    }

    protected function showInteractiveMenu(): void
    {
        $this->info('🔔 Notification System Management');
        $this->newLine();

        $choice = $this->choice(
            'What would you like to do?',
            [
                'test-email' => 'Test Email Configuration',
                'test-broadcast' => 'Test Broadcast Configuration',
                'validate-templates' => 'Validate All Templates',
                'send-test-email' => 'Send Test Email',
                'status' => 'Show System Status',
                'exit' => 'Exit'
            ]
        );

        switch ($choice) {
            case 'test-email':
                $this->testEmailConfiguration();
                break;
            case 'test-broadcast':
                $this->testBroadcastConfiguration();
                break;
            case 'validate-templates':
                $this->validateTemplates();
                break;
            case 'send-test-email':
                $email = $this->ask('Enter email address');
                $this->sendTestEmail($email);
                break;
            case 'status':
                $this->showSystemStatus();
                break;
            case 'exit':
                $this->info('Goodbye!');
                break;
        }
    }

    protected function hasAnyOptions(): bool
    {
        return $this->option('test-email') ||
               $this->option('test-broadcast') ||
               $this->option('test-template') ||
               $this->option('send-test-email') ||
               $this->option('validate-templates') ||
               $this->option('preview-template') ||
               $this->option('status');
    }
}
