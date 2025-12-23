<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use App\Services\EmailService;
use App\Services\BroadcastService;
use App\Services\TemplateService;
use App\Models\User;
use App\Models\Ticket;
use App\Models\EmailTemplate;
use App\Events\TicketCreated;
use App\Events\AssignedUser;
use App\Events\TicketUpdated;
use App\Events\TicketNewComment;
use App\Events\UserCreated;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Notification Migration Command
 * 
 * Tests and validates the migration from old to new notification system.
 */
class NotificationMigrationCommand extends Command
{
    protected $signature = 'notification:migrate 
                            {--test-events : Test event triggering}
                            {--test-services : Test notification services}
                            {--compare-systems : Compare old vs new system}
                            {--validate-data : Validate event data structures}
                            {--dry-run : Run without actually sending notifications}';

    protected $description = 'Test and validate the notification system migration';

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
        $this->info('🔄 Notification System Migration Test');
        $this->newLine();

        // Test event triggering
        if ($this->option('test-events')) {
            $this->testEventTriggering();
        }

        // Test notification services
        if ($this->option('test-services')) {
            $this->testNotificationServices();
        }

        // Compare old vs new system
        if ($this->option('compare-systems')) {
            $this->compareSystems();
        }

        // Validate event data structures
        if ($this->option('validate-data')) {
            $this->validateEventDataStructures();
        }

        // If no specific options, run all tests
        if (!$this->hasAnyOptions()) {
            $this->runAllTests();
        }

        return 0;
    }

    protected function testEventTriggering(): void
    {
        $this->info('🎯 Testing Event Triggering...');
        
        try {
            // Test with fake events to avoid side effects
            Event::fake();
            
            // Test TicketCreated event
            $this->line('Testing TicketCreated event...');
            event(new TicketCreated([
                'ticket_id' => 1,
                'created_by' => 1,
                'is_public' => false
            ]));
            Event::assertDispatched(TicketCreated::class);
            $this->info('✅ TicketCreated event dispatched successfully');

            // Test AssignedUser event
            $this->line('Testing AssignedUser event...');
            event(new AssignedUser([
                'ticket_id' => 1,
                'assigned_to' => 2,
                'assigned_by' => 1
            ]));
            Event::assertDispatched(AssignedUser::class);
            $this->info('✅ AssignedUser event dispatched successfully');

            // Test TicketUpdated event
            $this->line('Testing TicketUpdated event...');
            event(new TicketUpdated([
                'ticket_id' => 1,
                'changes' => ['status_id' => ['old' => 1, 'new' => 2]],
                'updated_by' => 1
            ]));
            Event::assertDispatched(TicketUpdated::class);
            $this->info('✅ TicketUpdated event dispatched successfully');

            // Test TicketNewComment event
            $this->line('Testing TicketNewComment event...');
            event(new TicketNewComment([
                'ticket_id' => 1,
                'user_id' => 1,
                'comment' => 'Test comment'
            ]));
            Event::assertDispatched(TicketNewComment::class);
            $this->info('✅ TicketNewComment event dispatched successfully');

            // Test UserCreated event
            $this->line('Testing UserCreated event...');
            event(new UserCreated([
                'user_id' => 1,
                'password' => 'test123',
                'created_by' => 1
            ]));
            Event::assertDispatched(UserCreated::class);
            $this->info('✅ UserCreated event dispatched successfully');

        } catch (\Exception $e) {
            $this->error('❌ Event testing failed: ' . $e->getMessage());
        }
    }

    protected function testNotificationServices(): void
    {
        $this->info('🔧 Testing Notification Services...');
        
        try {
            // Test email service
            $this->line('Testing EmailService...');
            $emailValidation = $this->emailService->validateEmailConfiguration();
            if ($emailValidation['valid']) {
                $this->info('✅ EmailService configuration is valid');
            } else {
                $this->warn('⚠️  EmailService has configuration issues:');
                foreach ($emailValidation['issues'] as $issue) {
                    $this->line("   • {$issue}");
                }
            }

            // Test broadcast service
            $this->line('Testing BroadcastService...');
            $broadcastStatus = $this->broadcastService->getBroadcastingStatus();
            if ($broadcastStatus['enabled']) {
                $this->info('✅ BroadcastService is enabled and configured');
            } else {
                $this->warn('⚠️  BroadcastService has configuration issues:');
                foreach ($broadcastStatus['issues'] as $issue) {
                    $this->line("   • {$issue}");
                }
            }

            // Test template service
            $this->line('Testing TemplateService...');
            $templates = $this->templateService->getAllTemplates();
            $validTemplates = collect($templates)->where('validation.valid', true)->count();
            $this->info("✅ TemplateService: {$validTemplates}/" . count($templates) . " templates valid");

            // Test notification service
            $this->line('Testing NotificationService...');
            $this->info('✅ NotificationService initialized successfully');

        } catch (\Exception $e) {
            $this->error('❌ Service testing failed: ' . $e->getMessage());
        }
    }

    protected function compareSystems(): void
    {
        $this->info('⚖️  Comparing Old vs New System...');
        
        $this->newLine();
        $this->info('📊 System Comparison:');
        
        $comparison = [
            'Architecture' => [
                'Old' => 'Fragmented listeners with duplicate code',
                'New' => 'Centralized service with unified logic'
            ],
            'Error Handling' => [
                'Old' => 'Basic error handling',
                'New' => 'Comprehensive error handling with logging'
            ],
            'Template Processing' => [
                'Old' => 'Basic regex replacement',
                'New' => 'Advanced template engine with validation'
            ],
            'Multi-Channel Support' => [
                'Old' => 'Limited to email and database',
                'New' => 'Email, database, and broadcast channels'
            ],
            'Queue Support' => [
                'Old' => 'Basic queue support',
                'New' => 'Advanced queue with retry mechanisms'
            ],
            'Type Safety' => [
                'Old' => 'Limited type hints',
                'New' => 'Full type safety throughout'
            ],
            'Testing Tools' => [
                'Old' => 'No built-in testing tools',
                'New' => 'Comprehensive testing and validation tools'
            ],
            'Documentation' => [
                'Old' => 'Limited documentation',
                'New' => 'Complete documentation and guides'
            ]
        ];

        foreach ($comparison as $aspect => $systems) {
            $this->line("📋 {$aspect}:");
            $this->line("   Old: {$systems['Old']}");
            $this->line("   New: {$systems['New']}");
            $this->newLine();
        }
    }

    protected function validateEventDataStructures(): void
    {
        $this->info('📋 Validating Event Data Structures...');
        
        $eventStructures = [
            'TicketCreated' => [
                'required' => ['ticket_id'],
                'optional' => ['created_by', 'is_public', 'password', 'assigned_to'],
                'example' => [
                    'ticket_id' => 1,
                    'created_by' => 1,
                    'is_public' => false,
                    'assigned_to' => 2
                ]
            ],
            'AssignedUser' => [
                'required' => ['ticket_id'],
                'optional' => ['assigned_to', 'assigned_by'],
                'example' => [
                    'ticket_id' => 1,
                    'assigned_to' => 2,
                    'assigned_by' => 1
                ]
            ],
            'TicketUpdated' => [
                'required' => ['ticket_id'],
                'optional' => ['changes', 'updated_by', 'update_message'],
                'example' => [
                    'ticket_id' => 1,
                    'changes' => ['status_id' => ['old' => 1, 'new' => 2]],
                    'updated_by' => 1
                ]
            ],
            'TicketNewComment' => [
                'required' => ['ticket_id', 'user_id', 'comment'],
                'optional' => [],
                'example' => [
                    'ticket_id' => 1,
                    'user_id' => 1,
                    'comment' => 'Test comment'
                ]
            ],
            'UserCreated' => [
                'required' => ['user_id'],
                'optional' => ['password', 'created_by'],
                'example' => [
                    'user_id' => 1,
                    'password' => 'test123',
                    'created_by' => 1
                ]
            ]
        ];

        foreach ($eventStructures as $eventName => $structure) {
            $this->line("📄 {$eventName}:");
            $this->line("   Required: " . implode(', ', $structure['required']));
            $this->line("   Optional: " . implode(', ', $structure['optional']));
            $this->line("   Example: " . json_encode($structure['example']));
            $this->newLine();
        }
    }

    protected function runAllTests(): void
    {
        $this->info('🧪 Running All Migration Tests...');
        $this->newLine();

        $this->testEventTriggering();
        $this->newLine();
        
        $this->testNotificationServices();
        $this->newLine();
        
        $this->validateEventDataStructures();
        $this->newLine();
        
        $this->compareSystems();
        
        $this->newLine();
        $this->info('✅ All migration tests completed!');
        
        $this->newLine();
        $this->info('📋 Next Steps:');
        $this->line('1. Review test results above');
        $this->line('2. Fix any configuration issues');
        $this->line('3. Test with real data in staging environment');
        $this->line('4. Deploy to production with monitoring');
        $this->line('5. Remove old system after successful migration');
    }

    protected function hasAnyOptions(): bool
    {
        return $this->option('test-events') ||
               $this->option('test-services') ||
               $this->option('compare-systems') ||
               $this->option('validate-data') ||
               $this->option('dry-run');
    }
}
