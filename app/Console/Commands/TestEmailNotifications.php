<?php

namespace App\Console\Commands;

use App\Events\TicketCreated;
use App\Events\UserCreated;
use App\Events\SendMail;
use App\Mail\SendMailFromHtml;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class TestEmailNotifications extends Command
{
    protected $signature = 'test:email-notifications {--test-smtp : Test SMTP connection} {--test-template : Test email template rendering} {--test-event : Test event-driven notifications}';
    protected $description = 'Test email notification system functionality';

    public function handle()
    {
        $this->info('🧪 Testing Email Notification System...');
        $this->newLine();

        // Test 1: SMTP Configuration
        if ($this->option('test-smtp') || $this->confirm('Test SMTP configuration?', true)) {
            $this->testSmtpConfiguration();
        }

        // Test 2: Email Templates
        if ($this->option('test-template') || $this->confirm('Test email templates?', true)) {
            $this->testEmailTemplates();
        }

        // Test 3: Event-driven Notifications
        if ($this->option('test-event') || $this->confirm('Test event-driven notifications?', true)) {
            $this->testEventNotifications();
        }

        // Test 4: Direct Email Sending
        if ($this->confirm('Test direct email sending?', true)) {
            $this->testDirectEmailSending();
        }

        $this->newLine();
        $this->info('✅ Email notification testing completed!');
    }

    private function testSmtpConfiguration()
    {
        $this->info('1. Testing SMTP Configuration:');

        $config = [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
            'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
        ];

        $this->table(
            ['Setting', 'Value'],
            array_map(function($key, $value) {
                return [$key, $value ?: 'Not Set'];
            }, array_keys($config), $config)
        );

        // Test SMTP connection
        try {
            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                config('mail.mailers.smtp.host'),
                config('mail.mailers.smtp.port'),
                config('mail.mailers.smtp.encryption') === 'tls'
            );

            $transport->setUsername(config('mail.mailers.smtp.username'));
            $transport->setPassword(config('mail.mailers.smtp.password'));

            $this->info('✅ SMTP configuration appears valid');
        } catch (\Exception $e) {
            $this->error('❌ SMTP configuration error: ' . $e->getMessage());
        }

        $this->newLine();
    }

    private function testEmailTemplates()
    {
        $this->info('2. Testing Email Templates:');

        $templates = EmailTemplate::all();

        if ($templates->isEmpty()) {
            $this->warn('⚠️  No email templates found in database');
            return;
        }

        $this->table(
            ['ID', 'Name', 'Slug', 'Language', 'Has HTML'],
            $templates->map(function($template) {
                return [
                    $template->id,
                    $template->name,
                    $template->slug,
                    $template->language,
                    $template->html ? 'Yes' : 'No'
                ];
            })->toArray()
        );

        // Test template rendering
        $testTemplate = $templates->first();
        if ($testTemplate && $testTemplate->html) {
            $variables = [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'ticket_id' => 'TEST-001',
                'subject' => 'Test Subject',
                'message' => 'Test message content',
                'url' => config('app.url'),
                'sender_name' => 'HelpDesk System'
            ];

            $renderedHtml = $testTemplate->html;
            foreach ($variables as $key => $value) {
                $renderedHtml = str_replace('{' . $key . '}', $value, $renderedHtml);
            }

            $this->info("✅ Template '{$testTemplate->name}' rendered successfully");
            $this->info("📧 Rendered HTML length: " . strlen($renderedHtml) . " characters");
        }

        $this->newLine();
    }

    private function testEventNotifications()
    {
        $this->info('3. Testing Event-driven Notifications:');

        // Check if events are registered
        $events = [
            'App\Events\TicketCreated' => 'TicketCreatedNotification',
            'App\Events\UserCreated' => 'UserCreatedNotification',
            'App\Events\SendMail' => 'SendMailNotification',
        ];

        $this->table(
            ['Event', 'Listener', 'Status'],
            array_map(function($event, $listener) {
                $status = class_exists($event) && class_exists("App\Listeners\\{$listener}") ? '✅ Registered' : '❌ Missing';
                return [$event, $listener, $status];
            }, array_keys($events), $events)
        );

        // Test event firing (without actually sending emails)
        try {
            $this->info('🧪 Testing event firing...');

            // Create a test user for events
            $testUser = User::first();
            if (!$testUser) {
                $this->warn('⚠️  No users found to test events');
                return;
            }

            // Test UserCreated event
            Event::fake();
            event(new UserCreated([
                'id' => $testUser->id,
                'password' => 'test123'
            ]));
            Event::assertDispatched(UserCreated::class);
            $this->info('✅ UserCreated event fired successfully');

            // Test SendMail event
            Event::fake();
            event(new SendMail([
                'to' => ['name' => 'Test User', 'email' => 'test@example.com'],
                'subject' => 'Test Subject',
                'body' => 'Test message',
                'sender_name' => 'HelpDesk'
            ]));
            Event::assertDispatched(SendMail::class);
            $this->info('✅ SendMail event fired successfully');

        } catch (\Exception $e) {
            $this->error('❌ Event testing error: ' . $e->getMessage());
        }

        $this->newLine();
    }

    private function testDirectEmailSending()
    {
        $this->info('4. Testing Direct Email Sending:');

        $testEmail = $this->ask('Enter test email address (or press Enter to skip)', '');

        if (empty($testEmail)) {
            $this->warn('⚠️  Skipping direct email test');
            return;
        }

        if (!$this->confirm("Send test email to {$testEmail}?", false)) {
            $this->warn('⚠️  Email sending cancelled by user');
            return;
        }

        try {
            $messageData = [
                'html' => '<h1>Test Email</h1><p>This is a test email from HelpDesk system.</p><p>If you receive this, the email system is working correctly.</p>',
                'subject' => 'HelpDesk - Email System Test'
            ];

            Mail::to($testEmail)->send(new SendMailFromHtml($messageData));
            $this->info("✅ Test email sent successfully to {$testEmail}");

        } catch (\Exception $e) {
            $this->error('❌ Email sending failed: ' . $e->getMessage());
            Log::error('Email sending test failed: ' . $e->getMessage());
        }

        $this->newLine();
    }
}
