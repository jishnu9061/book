<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\Type;
use App\Models\User;
use App\Models\TicketActivity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComprehensiveTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing tickets and related data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('ticket_activities')->truncate();
        DB::table('tickets')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();
        $departments = Department::all();
        $categories = Category::all();
        $statuses = Status::all();
        $priorities = Priority::all();
        $types = Type::all();

        // Ensure we have the required data
        if ($users->isEmpty() || $departments->isEmpty() || $statuses->isEmpty() || $priorities->isEmpty()) {
            $this->command->error('Required data (users, departments, statuses, priorities) not found. Please run other seeders first.');
            return;
        }

        // Create realistic ticket scenarios
        $ticketScenarios = [
            // Technical Support Tickets
            [
                'subject' => 'Website is loading very slowly',
                'details' => 'Our website has been experiencing extremely slow loading times for the past 2 days. Pages are taking 10-15 seconds to load, which is affecting our business operations. This started after the recent server maintenance.',
                'priority' => 'High',
                'status' => 'In Progress',
                'department' => 'Technical Support',
                'type' => 'Bug Report',
                'category' => 'Hosting Issue',
                'impact_level' => 'high',
                'urgency_level' => 'high',
                'source' => 'web',
                'tags' => ['performance', 'server', 'urgent']
            ],
            [
                'subject' => 'Email notifications not working',
                'details' => 'I am not receiving email notifications for new tickets and updates. This is causing me to miss important customer inquiries. Please check the email configuration.',
                'priority' => 'Medium',
                'status' => 'Open',
                'department' => 'Technical Support',
                'type' => 'Bug Report',
                'category' => 'Domain Issue',
                'impact_level' => 'medium',
                'urgency_level' => 'medium',
                'source' => 'email',
                'tags' => ['email', 'notifications', 'configuration']
            ],
            [
                'subject' => 'Database connection timeout errors',
                'details' => 'We are experiencing intermittent database connection timeout errors. This is causing our application to crash randomly. The errors occur mostly during peak hours.',
                'priority' => 'Critical',
                'status' => 'In Progress',
                'department' => 'Technical Support',
                'type' => 'Incident',
                'category' => 'Server is not working',
                'impact_level' => 'critical',
                'urgency_level' => 'critical',
                'source' => 'api',
                'tags' => ['database', 'timeout', 'critical', 'performance']
            ],

            // Sales Tickets
            [
                'subject' => 'Interested in upgrading to premium plan',
                'details' => 'I would like to know more about your premium hosting plan. Can you provide details about the features, pricing, and migration process?',
                'priority' => 'Medium',
                'status' => 'Open',
                'department' => 'Sales',
                'type' => 'Question',
                'category' => 'New Customer',
                'impact_level' => 'low',
                'urgency_level' => 'low',
                'source' => 'web',
                'tags' => ['upgrade', 'premium', 'pricing']
            ],
            [
                'subject' => 'Need custom domain setup assistance',
                'details' => 'I purchased a custom domain and need help setting it up with my hosting account. I am not familiar with DNS settings and would appreciate step-by-step guidance.',
                'priority' => 'Medium',
                'status' => 'Pending',
                'department' => 'Sales',
                'type' => 'Service Request',
                'category' => 'Domain Purchase',
                'impact_level' => 'medium',
                'urgency_level' => 'medium',
                'source' => 'phone',
                'tags' => ['domain', 'dns', 'setup', 'guidance']
            ],

            // Billing Tickets
            [
                'subject' => 'Invoice payment failed - need assistance',
                'details' => 'I tried to pay my monthly invoice but the payment failed. I received an error message saying "Payment declined". My credit card is valid and has sufficient funds. Please help resolve this.',
                'priority' => 'High',
                'status' => 'Open',
                'department' => 'Billing',
                'type' => 'Service Request',
                'category' => 'Domain Price',
                'impact_level' => 'high',
                'urgency_level' => 'high',
                'source' => 'email',
                'tags' => ['payment', 'invoice', 'billing', 'urgent']
            ],
            [
                'subject' => 'Request for invoice adjustment',
                'details' => 'I was charged for services I did not use last month. I would like to request an adjustment to my invoice. Please review my usage and provide a corrected invoice.',
                'priority' => 'Medium',
                'status' => 'Pending',
                'department' => 'Billing',
                'type' => 'Question',
                'category' => 'Pricing about hosting',
                'impact_level' => 'medium',
                'urgency_level' => 'medium',
                'source' => 'web',
                'tags' => ['invoice', 'adjustment', 'billing']
            ],

            // Customer Success Tickets
            [
                'subject' => 'Need help with account migration',
                'details' => 'I am migrating my business to your platform and need assistance with transferring my data and setting up the new account. This is a complex migration involving multiple domains and email accounts.',
                'priority' => 'High',
                'status' => 'In Progress',
                'department' => 'Customer Success',
                'type' => 'Service Request',
                'category' => 'Migrate hosting plan',
                'impact_level' => 'high',
                'urgency_level' => 'high',
                'source' => 'web',
                'tags' => ['migration', 'account', 'data-transfer']
            ],
            [
                'subject' => 'Training session request',
                'details' => 'I would like to schedule a training session for my team to learn how to use the new features in your platform. We have 5 team members who need training.',
                'priority' => 'Low',
                'status' => 'Open',
                'department' => 'Customer Success',
                'type' => 'Service Request',
                'category' => 'Existing Customer',
                'impact_level' => 'low',
                'urgency_level' => 'low',
                'source' => 'email',
                'tags' => ['training', 'team', 'education']
            ],

            // Development/Feature Requests
            [
                'subject' => 'Feature request: API rate limiting controls',
                'details' => 'We need more granular control over API rate limiting. Currently, we can only set global limits, but we need per-user and per-endpoint rate limiting capabilities.',
                'priority' => 'Medium',
                'status' => 'Open',
                'department' => 'Development',
                'type' => 'Feature Request',
                'category' => 'New Event',
                'impact_level' => 'medium',
                'urgency_level' => 'medium',
                'source' => 'web',
                'tags' => ['api', 'rate-limiting', 'feature-request']
            ],
            [
                'subject' => 'Bug: Mobile app crashes on iOS 17',
                'details' => 'The mobile app crashes immediately when opened on iOS 17 devices. This affects all users with the latest iOS version. The app works fine on older iOS versions.',
                'priority' => 'High',
                'status' => 'In Progress',
                'department' => 'Development',
                'type' => 'Bug Report',
                'category' => 'Upcoming Event',
                'impact_level' => 'high',
                'urgency_level' => 'high',
                'source' => 'api',
                'tags' => ['mobile', 'ios', 'crash', 'bug']
            ],

            // Management/Administrative
            [
                'subject' => 'Request for account access audit',
                'details' => 'We need to conduct a security audit of our account access. Please provide a report of all users who have accessed our account in the last 30 days.',
                'priority' => 'Medium',
                'status' => 'Open',
                'department' => 'Management',
                'type' => 'Service Request',
                'category' => 'Meeting',
                'impact_level' => 'medium',
                'urgency_level' => 'medium',
                'source' => 'email',
                'tags' => ['security', 'audit', 'access-log']
            ],
            [
                'subject' => 'Contract renewal discussion',
                'details' => 'Our current contract is expiring in 2 months. We would like to discuss renewal terms and potential upgrades. Please schedule a call with our account manager.',
                'priority' => 'Low',
                'status' => 'Pending',
                'department' => 'Management',
                'type' => 'Question',
                'category' => 'Arrange A Meeting',
                'impact_level' => 'low',
                'urgency_level' => 'low',
                'source' => 'phone',
                'tags' => ['contract', 'renewal', 'meeting']
            ],

            // Resolved/Closed Tickets
            [
                'subject' => 'SSL certificate expired - RESOLVED',
                'details' => 'Our SSL certificate expired and caused security warnings on our website. This has been resolved by renewing the certificate.',
                'priority' => 'High',
                'status' => 'Resolved',
                'department' => 'Technical Support',
                'type' => 'Incident',
                'category' => 'Domain Issue',
                'impact_level' => 'high',
                'urgency_level' => 'high',
                'source' => 'web',
                'tags' => ['ssl', 'certificate', 'security', 'resolved'],
                'resolution' => 'SSL certificate has been renewed and installed. Website is now secure and accessible.',
                'resolved_at' => Carbon::now()->subDays(2)
            ],
            [
                'subject' => 'Password reset not working - CLOSED',
                'details' => 'I was unable to reset my password using the forgot password feature. This has been resolved.',
                'priority' => 'Medium',
                'status' => 'Closed',
                'department' => 'Technical Support',
                'type' => 'Bug Report',
                'category' => 'Hosting Issue',
                'impact_level' => 'medium',
                'urgency_level' => 'medium',
                'source' => 'web',
                'tags' => ['password', 'reset', 'authentication', 'closed'],
                'resolution' => 'Password reset functionality has been fixed. User can now reset passwords successfully.',
                'resolved_at' => Carbon::now()->subDays(5),
                'closed_at' => Carbon::now()->subDays(3)
            ]
        ];

        // Create the predefined realistic scenarios first
        foreach ($ticketScenarios as $index => $scenario) {
            // Find related models
            $priority = $priorities->where('name', $scenario['priority'])->first() ?? $priorities->random();
            $status = $statuses->where('name', $scenario['status'])->first() ?? $statuses->random();
            $department = $departments->where('name', $scenario['department'])->first() ?? $departments->random();
            $type = $types->where('name', $scenario['type'])->first() ?? $types->random();
            $category = $categories->where('name', $scenario['category'])->first() ?? $categories->random();
            
            $customer = $users->where('role_id', 6)->random(); // Customer role
            
            // Only assign to specific users by email
            $allowedEmails = ['robert.slaughter@mail.com', 'john.ali@mail.com'];
            $allowedAgents = $users->whereIn('email', $allowedEmails);
            
            if ($allowedAgents->isEmpty()) {
                $this->command->warn('No users found with the specified email addresses. Skipping ticket assignment.');
                $agent = null;
            } else {
                $agent = $allowedAgents->random();
            }
            
            $creator = $users->random();

            // Create ticket
            $ticket = Ticket::create([
                'uid' => 'TKT-' . (100000 + $index + 1),
                'subject' => $scenario['subject'],
                'details' => $scenario['details'],
                'user_id' => $customer->id,
                'created_by' => $creator->id,
                'assigned_to' => $agent ? $agent->id : null,
                'priority_id' => $priority->id,
                'status_id' => $status->id,
                'department_id' => $department->id,
                'category_id' => $category->id,
                'type_id' => $type->id,
                'impact_level' => $scenario['impact_level'],
                'urgency_level' => $scenario['urgency_level'],
                'source' => $scenario['source'],
                'tags' => $scenario['tags'],
                'resolution' => $scenario['resolution'] ?? null,
                'due_date' => $this->calculateDueDate($priority, $status),
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(0, 5)),
            ]);

            // Create ticket activities
            $this->createTicketActivities($ticket, $scenario);
        }

        // Create additional meaningful tickets to reach 100 total
        // Skip the first 20 scenarios to avoid duplicates with predefined scenarios
        $remainingTickets = 100 - count($ticketScenarios);
        for ($i = 0; $i < $remainingTickets; $i++) {
            $this->createMeaningfulTicket($users, $departments, $categories, $statuses, $priorities, $types, $i + count($ticketScenarios) + 20);
        }

        $this->command->info('Created ' . Ticket::count() . ' tickets with realistic scenarios');
    }

    private function calculateDueDate($priority, $status)
    {
        if ($status->slug === 'closed' || $status->slug === 'resolved') {
            return null;
        }

        $days = match($priority->name) {
            'Critical' => 1,
            'High' => 3,
            'Medium' => 7,
            'Low' => 14,
            default => 7
        };

        return Carbon::now()->addDays($days);
    }

    private function createTicketActivities($ticket, $scenario)
    {
        $activities = [
            [
                'action' => 'created',
                'description' => 'Ticket created',
                'user_id' => $ticket->created_by,
                'created_at' => $ticket->created_at
            ]
        ];

        if ($ticket->assigned_to && $ticket->assignedTo) {
            $activities[] = [
                'action' => 'assigned',
                'description' => 'Ticket assigned to ' . $ticket->assignedTo->first_name,
                'user_id' => $ticket->created_by,
                'created_at' => $ticket->created_at->addMinutes(5)
            ];
        }

        if ($scenario['status'] === 'In Progress' && $ticket->assigned_to) {
            $activities[] = [
                'action' => 'status_changed',
                'description' => 'Status changed to In Progress',
                'user_id' => $ticket->assigned_to,
                'created_at' => $ticket->created_at->addHours(2)
            ];
        }

        if (($scenario['status'] === 'Resolved' || $scenario['status'] === 'Closed') && $ticket->assigned_to) {
            $activities[] = [
                'action' => 'resolved',
                'description' => 'Ticket resolved',
                'user_id' => $ticket->assigned_to,
                'created_at' => $scenario['resolved_at'] ?? $ticket->created_at->addDays(2)
            ];
        }

        foreach ($activities as $activity) {
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'activity_type' => $activity['action'],
                'description' => $activity['description'],
                'user_id' => $activity['user_id'],
                'created_at' => $activity['created_at']
            ]);
        }
    }

    private function createMeaningfulTicket($users, $departments, $categories, $statuses, $priorities, $types, $index = null)
    {
        $customer = $users->where('role_id', 6)->random();
        
        // Only assign to specific users by email
        $allowedEmails = ['robert.slaughter@mail.com', 'john.ali@mail.com'];
        $allowedAgents = $users->whereIn('email', $allowedEmails);
        
        if ($allowedAgents->isEmpty()) {
            $agent = null;
        } else {
            $agent = $allowedAgents->random();
        }
        
        $creator = $users->random();

        // Comprehensive list of meaningful ticket scenarios
        $meaningfulScenarios = [
            // Technical Support Issues
            [
                'subject' => 'Unable to access my account dashboard',
                'details' => 'I am getting an error message when trying to log into my account dashboard. The error says "Access denied" but I am using the correct credentials.',
                'priority' => 'Medium',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['login', 'access', 'dashboard']
            ],
            [
                'subject' => 'Website showing 404 errors',
                'details' => 'My website is showing 404 errors for several pages that were working fine yesterday. This is affecting my business.',
                'priority' => 'High',
                'type' => 'Incident',
                'department' => 'Technical Support',
                'tags' => ['404', 'website', 'urgent']
            ],
            [
                'subject' => 'Email delivery issues',
                'details' => 'My emails are not being delivered to customers. They are not receiving order confirmations and notifications.',
                'priority' => 'Critical',
                'type' => 'Incident',
                'department' => 'Technical Support',
                'tags' => ['email', 'delivery', 'critical']
            ],
            [
                'subject' => 'Database backup not working',
                'details' => 'The automated database backup process seems to be failing. I need to ensure my data is properly backed up.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['backup', 'database', 'data']
            ],
            [
                'subject' => 'Performance issues with website',
                'details' => 'My website is loading very slowly and sometimes times out. This is affecting user experience.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['performance', 'slow', 'timeout']
            ],
            [
                'subject' => 'SSL certificate expired',
                'details' => 'My website is showing security warnings because the SSL certificate has expired. Customers are concerned about security.',
                'priority' => 'Critical',
                'type' => 'Incident',
                'department' => 'Technical Support',
                'tags' => ['ssl', 'certificate', 'security']
            ],
            [
                'subject' => 'Server disk space full',
                'details' => 'I received an alert that my server disk space is 95% full. This could cause my website to go down.',
                'priority' => 'High',
                'type' => 'Incident',
                'department' => 'Technical Support',
                'tags' => ['disk', 'space', 'server']
            ],
            [
                'subject' => 'CDN not working properly',
                'details' => 'My CDN seems to be serving old cached content. Changes to my website are not appearing to users.',
                'priority' => 'Medium',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['cdn', 'cache', 'content']
            ],
            [
                'subject' => 'Database connection errors',
                'details' => 'I am getting intermittent database connection errors. This is causing my application to crash randomly.',
                'priority' => 'High',
                'type' => 'Incident',
                'department' => 'Technical Support',
                'tags' => ['database', 'connection', 'errors']
            ],
            [
                'subject' => 'File upload not working',
                'details' => 'Users cannot upload files to my website. They get an error message saying "Upload failed".',
                'priority' => 'Medium',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['upload', 'files', 'error']
            ],

            // Sales Inquiries
            [
                'subject' => 'Interested in enterprise plan',
                'details' => 'I am interested in upgrading to your enterprise plan. Can you provide details about features and pricing?',
                'priority' => 'Medium',
                'type' => 'Question',
                'department' => 'Sales',
                'tags' => ['enterprise', 'upgrade', 'pricing']
            ],
            [
                'subject' => 'Need custom domain setup assistance',
                'details' => 'I purchased a custom domain and need help setting it up with my hosting account. I am not familiar with DNS settings.',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Sales',
                'tags' => ['domain', 'dns', 'setup']
            ],
            [
                'subject' => 'Want to migrate from competitor',
                'details' => 'I am currently using a competitor\'s service and want to migrate to your platform. What is the migration process?',
                'priority' => 'High',
                'type' => 'Service Request',
                'department' => 'Sales',
                'tags' => ['migration', 'competitor', 'process']
            ],
            [
                'subject' => 'Need bulk pricing for multiple websites',
                'details' => 'I manage 20 websites and need hosting for all of them. Do you offer bulk pricing or volume discounts?',
                'priority' => 'Medium',
                'type' => 'Question',
                'department' => 'Sales',
                'tags' => ['bulk', 'pricing', 'volume']
            ],
            [
                'subject' => 'Request for demo of premium features',
                'details' => 'I would like to see a demo of your premium features before making a decision. Can you schedule a call?',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Sales',
                'tags' => ['demo', 'premium', 'features']
            ],

            // Billing Issues
            [
                'subject' => 'Invoice payment failed',
                'details' => 'I tried to pay my monthly invoice but the payment failed. My credit card is valid and has sufficient funds.',
                'priority' => 'High',
                'type' => 'Service Request',
                'department' => 'Billing',
                'tags' => ['payment', 'invoice', 'failed']
            ],
            [
                'subject' => 'Request for invoice adjustment',
                'details' => 'I was charged for services I did not use last month. I would like to request an adjustment to my invoice.',
                'priority' => 'Medium',
                'type' => 'Question',
                'department' => 'Billing',
                'tags' => ['invoice', 'adjustment', 'billing']
            ],
            [
                'subject' => 'Need to change billing address',
                'details' => 'I moved to a new address and need to update my billing information. How can I change this?',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Billing',
                'tags' => ['billing', 'address', 'update']
            ],
            [
                'subject' => 'Want to switch to annual billing',
                'details' => 'I want to switch from monthly to annual billing to get the discount. Can you help me with this?',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Billing',
                'tags' => ['annual', 'billing', 'discount']
            ],
            [
                'subject' => 'Refund request for unused service',
                'details' => 'I cancelled my service last month but was still charged. I would like to request a refund for the unused period.',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Billing',
                'tags' => ['refund', 'cancelled', 'unused']
            ],

            // Customer Success
            [
                'subject' => 'Need help with account migration',
                'details' => 'I am migrating my business to your platform and need assistance with transferring my data and setting up the new account.',
                'priority' => 'High',
                'type' => 'Service Request',
                'department' => 'Customer Success',
                'tags' => ['migration', 'account', 'data-transfer']
            ],
            [
                'subject' => 'Training session request',
                'details' => 'I would like to schedule a training session for my team to learn how to use the new features in your platform.',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Customer Success',
                'tags' => ['training', 'team', 'education']
            ],
            [
                'subject' => 'Account setup assistance needed',
                'details' => 'I am new to your platform and need help setting up my account properly. Can someone guide me through the process?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Customer Success',
                'tags' => ['setup', 'new-user', 'guidance']
            ],
            [
                'subject' => 'Feature request: Better reporting',
                'details' => 'I would like to request better reporting features in the dashboard. Currently, the reports are too basic for my needs.',
                'priority' => 'Low',
                'type' => 'Feature Request',
                'department' => 'Customer Success',
                'tags' => ['reporting', 'dashboard', 'feature-request']
            ],
            [
                'subject' => 'Need help with API documentation',
                'details' => 'I am trying to use your API but the documentation is unclear. Can you provide better examples and explanations?',
                'priority' => 'Medium',
                'type' => 'Question',
                'department' => 'Customer Success',
                'tags' => ['api', 'documentation', 'examples']
            ],

            // Development/Feature Requests
            [
                'subject' => 'Feature request: API rate limiting controls',
                'details' => 'We need more granular control over API rate limiting. Currently, we can only set global limits.',
                'priority' => 'Medium',
                'type' => 'Feature Request',
                'department' => 'Development',
                'tags' => ['api', 'rate-limiting', 'feature-request']
            ],
            [
                'subject' => 'Bug: Mobile app crashes on iOS 17',
                'details' => 'The mobile app crashes immediately when opened on iOS 17 devices. This affects all users with the latest iOS version.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Development',
                'tags' => ['mobile', 'ios', 'crash']
            ],
            [
                'subject' => 'Request for webhook functionality',
                'details' => 'I need webhook functionality to integrate with my external systems. Is this feature available or planned?',
                'priority' => 'Medium',
                'type' => 'Feature Request',
                'department' => 'Development',
                'tags' => ['webhook', 'integration', 'feature-request']
            ],
            [
                'subject' => 'Bug: Search functionality not working',
                'details' => 'The search functionality on my website is not returning any results, even for content that definitely exists.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Development',
                'tags' => ['search', 'functionality', 'bug']
            ],
            [
                'subject' => 'Request for custom fields feature',
                'details' => 'I need the ability to add custom fields to my forms. This would greatly improve my workflow.',
                'priority' => 'Low',
                'type' => 'Feature Request',
                'department' => 'Development',
                'tags' => ['custom-fields', 'forms', 'workflow']
            ],

            // Management/Administrative
            [
                'subject' => 'Request for account access audit',
                'details' => 'We need to conduct a security audit of our account access. Please provide a report of all users who have accessed our account.',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Management',
                'tags' => ['security', 'audit', 'access-log']
            ],
            [
                'subject' => 'Contract renewal discussion',
                'details' => 'Our current contract is expiring in 2 months. We would like to discuss renewal terms and potential upgrades.',
                'priority' => 'Low',
                'type' => 'Question',
                'department' => 'Management',
                'tags' => ['contract', 'renewal', 'meeting']
            ],
            [
                'subject' => 'Need compliance documentation',
                'details' => 'We need documentation for compliance purposes. Can you provide SOC 2 and GDPR compliance certificates?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Management',
                'tags' => ['compliance', 'documentation', 'certificates']
            ],
            [
                'subject' => 'Request for dedicated support',
                'details' => 'As a large enterprise customer, we would like to request dedicated support with a dedicated account manager.',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Management',
                'tags' => ['dedicated', 'support', 'enterprise']
            ],
            [
                'subject' => 'Need SLA documentation',
                'details' => 'We need detailed SLA documentation for our legal team. Can you provide the complete service level agreement?',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Management',
                'tags' => ['sla', 'documentation', 'legal']
            ],

            // Additional Technical Support Scenarios
            [
                'subject' => 'Website redirects not working',
                'details' => 'I set up redirects from my old domain to the new one, but they are not working properly. Users are getting 404 errors.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['redirects', 'domain', '404']
            ],
            [
                'subject' => 'Cron jobs not executing',
                'details' => 'My scheduled cron jobs are not running as expected. This is affecting my automated tasks and data processing.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['cron', 'scheduled', 'automation']
            ],
            [
                'subject' => 'Memory limit exceeded errors',
                'details' => 'I am getting "memory limit exceeded" errors on my website. This is causing pages to fail loading.',
                'priority' => 'High',
                'type' => 'Incident',
                'department' => 'Technical Support',
                'tags' => ['memory', 'limit', 'performance']
            ],
            [
                'subject' => 'Email server blacklisted',
                'details' => 'My email server has been blacklisted and emails are being rejected. This is affecting my business communications.',
                'priority' => 'Critical',
                'type' => 'Incident',
                'department' => 'Technical Support',
                'tags' => ['email', 'blacklist', 'server']
            ],
            [
                'subject' => 'Website showing mixed content warnings',
                'details' => 'My website is showing mixed content warnings in browsers. Some resources are being loaded over HTTP instead of HTTPS.',
                'priority' => 'Medium',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['https', 'mixed-content', 'security']
            ],

            // Additional Sales Scenarios
            [
                'subject' => 'Need pricing for dedicated server',
                'details' => 'I am interested in a dedicated server solution. Can you provide pricing and specifications for your dedicated hosting plans?',
                'priority' => 'Medium',
                'type' => 'Question',
                'department' => 'Sales',
                'tags' => ['dedicated', 'server', 'pricing']
            ],
            [
                'subject' => 'Want to upgrade from shared to VPS',
                'details' => 'My website has outgrown shared hosting. I need to upgrade to a VPS solution. What are my options?',
                'priority' => 'High',
                'type' => 'Service Request',
                'department' => 'Sales',
                'tags' => ['upgrade', 'vps', 'shared-hosting']
            ],
            [
                'subject' => 'Need help with SSL certificate purchase',
                'details' => 'I need to purchase an SSL certificate for my website. Can you guide me through the process and recommend the best option?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Sales',
                'tags' => ['ssl', 'certificate', 'purchase']
            ],
            [
                'subject' => 'Interested in managed WordPress hosting',
                'details' => 'I am looking for managed WordPress hosting with automatic updates and security features. Do you offer this service?',
                'priority' => 'Medium',
                'type' => 'Question',
                'department' => 'Sales',
                'tags' => ['wordpress', 'managed', 'hosting']
            ],
            [
                'subject' => 'Need help with email hosting setup',
                'details' => 'I want to set up professional email hosting for my business domain. Can you help me with the configuration?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Sales',
                'tags' => ['email', 'hosting', 'business']
            ],

            // Additional Billing Scenarios
            [
                'subject' => 'Need to update payment method',
                'details' => 'My credit card has expired and I need to update my payment method. How can I do this without service interruption?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Billing',
                'tags' => ['payment', 'update', 'credit-card']
            ],
            [
                'subject' => 'Want to cancel auto-renewal',
                'details' => 'I want to cancel the auto-renewal for my hosting plan. I prefer to renew manually each year.',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Billing',
                'tags' => ['auto-renewal', 'cancel', 'manual']
            ],
            [
                'subject' => 'Need invoice for tax purposes',
                'details' => 'I need a detailed invoice for tax purposes. Can you provide an invoice with all the required tax information?',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Billing',
                'tags' => ['invoice', 'tax', 'documentation']
            ],
            [
                'subject' => 'Dispute charge on credit card',
                'details' => 'I see a charge on my credit card that I do not recognize. Can you help me identify what this charge is for?',
                'priority' => 'High',
                'type' => 'Service Request',
                'department' => 'Billing',
                'tags' => ['dispute', 'charge', 'credit-card']
            ],
            [
                'subject' => 'Need to split billing across departments',
                'details' => 'I need to split the billing for our hosting services across different departments in my company. Is this possible?',
                'priority' => 'Medium',
                'type' => 'Question',
                'department' => 'Billing',
                'tags' => ['split', 'billing', 'departments']
            ],

            // Additional Customer Success Scenarios
            [
                'subject' => 'Need help with website backup',
                'details' => 'I want to set up automated backups for my website. Can you help me configure this and explain the backup process?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Customer Success',
                'tags' => ['backup', 'automated', 'website']
            ],
            [
                'subject' => 'Want to learn about security features',
                'details' => 'I would like to learn about the security features available for my hosting account. Can you provide a comprehensive overview?',
                'priority' => 'Low',
                'type' => 'Question',
                'department' => 'Customer Success',
                'tags' => ['security', 'features', 'overview']
            ],
            [
                'subject' => 'Need help with website optimization',
                'details' => 'My website is not performing well in search engines. Can you help me optimize it for better SEO and performance?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Customer Success',
                'tags' => ['optimization', 'seo', 'performance']
            ],
            [
                'subject' => 'Want to implement two-factor authentication',
                'details' => 'I want to add two-factor authentication to my account for better security. How can I set this up?',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Customer Success',
                'tags' => ['2fa', 'authentication', 'security']
            ],
            [
                'subject' => 'Need help with database optimization',
                'details' => 'My database queries are running slowly. Can you help me optimize the database performance?',
                'priority' => 'High',
                'type' => 'Service Request',
                'department' => 'Customer Success',
                'tags' => ['database', 'optimization', 'performance']
            ],

            // Additional Development Scenarios
            [
                'subject' => 'Request for staging environment',
                'details' => 'I need a staging environment to test changes before deploying to production. Is this available?',
                'priority' => 'Medium',
                'type' => 'Feature Request',
                'department' => 'Development',
                'tags' => ['staging', 'environment', 'testing']
            ],
            [
                'subject' => 'Bug: Form submissions not working',
                'details' => 'The contact form on my website is not working. Users are not receiving confirmation emails and submissions are not being saved.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Development',
                'tags' => ['form', 'submission', 'contact']
            ],
            [
                'subject' => 'Request for Git integration',
                'details' => 'I would like to integrate Git with my hosting account for easier deployment. Is this feature available?',
                'priority' => 'Medium',
                'type' => 'Feature Request',
                'department' => 'Development',
                'tags' => ['git', 'integration', 'deployment']
            ],
            [
                'subject' => 'Bug: Image uploads failing',
                'details' => 'Users cannot upload images to my website. They get an error message about file size limits, but the images are within the allowed size.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Development',
                'tags' => ['image', 'upload', 'file-size']
            ],
            [
                'subject' => 'Request for PHP version upgrade',
                'details' => 'I need to upgrade to a newer version of PHP for my application. Can you help me with this upgrade?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Development',
                'tags' => ['php', 'upgrade', 'version']
            ],

            // Additional Management Scenarios
            [
                'subject' => 'Need disaster recovery plan',
                'details' => 'We need to develop a disaster recovery plan for our hosting services. Can you provide documentation and recommendations?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Management',
                'tags' => ['disaster', 'recovery', 'plan']
            ],
            [
                'subject' => 'Request for uptime statistics',
                'details' => 'We need detailed uptime statistics for our hosting services for our SLA reporting. Can you provide this data?',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Management',
                'tags' => ['uptime', 'statistics', 'sla']
            ],
            [
                'subject' => 'Need help with vendor evaluation',
                'details' => 'We are evaluating hosting providers for our enterprise needs. Can you provide detailed information about your enterprise solutions?',
                'priority' => 'Medium',
                'type' => 'Question',
                'department' => 'Management',
                'tags' => ['vendor', 'evaluation', 'enterprise']
            ],
            [
                'subject' => 'Request for custom SLA terms',
                'details' => 'We need custom SLA terms for our enterprise contract. Can you provide information about custom SLA options?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Management',
                'tags' => ['custom', 'sla', 'enterprise']
            ],
            [
                'subject' => 'Need help with data migration planning',
                'details' => 'We are planning to migrate our data to your platform. Can you help us create a detailed migration plan?',
                'priority' => 'High',
                'type' => 'Service Request',
                'department' => 'Management',
                'tags' => ['migration', 'planning', 'data']
            ],

            // Additional Technical Support Scenarios (35 more to reach 100)
            [
                'subject' => 'Website showing blank pages',
                'details' => 'My website is showing blank pages instead of content. This is affecting all visitors to my site.',
                'priority' => 'Critical',
                'type' => 'Incident',
                'department' => 'Technical Support',
                'tags' => ['blank', 'pages', 'critical']
            ],
            [
                'subject' => 'Email attachments not working',
                'details' => 'I cannot send or receive email attachments. The emails are being delivered but without the attached files.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['email', 'attachments', 'files']
            ],
            [
                'subject' => 'Website showing PHP errors',
                'details' => 'My website is displaying PHP error messages to visitors. This looks unprofessional and needs to be fixed.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['php', 'errors', 'display']
            ],
            [
                'subject' => 'Database queries timing out',
                'details' => 'My database queries are timing out frequently. This is causing slow page loads and user complaints.',
                'priority' => 'High',
                'type' => 'Incident',
                'department' => 'Technical Support',
                'tags' => ['database', 'timeout', 'queries']
            ],
            [
                'subject' => 'Website not accessible from mobile',
                'details' => 'My website is not accessible from mobile devices. Users are getting connection errors on their phones.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['mobile', 'access', 'connection']
            ],
            [
                'subject' => 'FTP connection issues',
                'details' => 'I cannot connect to my website via FTP. The connection keeps timing out or being rejected.',
                'priority' => 'Medium',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['ftp', 'connection', 'timeout']
            ],
            [
                'subject' => 'Website showing 500 internal server errors',
                'details' => 'My website is showing 500 internal server errors randomly. This is causing users to lose access intermittently.',
                'priority' => 'Critical',
                'type' => 'Incident',
                'department' => 'Technical Support',
                'tags' => ['500', 'server', 'errors']
            ],
            [
                'subject' => 'Email bounce rate too high',
                'details' => 'My email bounce rate has increased significantly. Many emails are being rejected by recipient servers.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['email', 'bounce', 'rejection']
            ],
            [
                'subject' => 'Website loading very slowly from certain locations',
                'details' => 'My website loads very slowly from certain geographic locations. Users in Europe are experiencing delays.',
                'priority' => 'Medium',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['slow', 'geographic', 'locations']
            ],
            [
                'subject' => 'SSL certificate installation failed',
                'details' => 'I tried to install an SSL certificate but the installation failed. My website is still showing as not secure.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Technical Support',
                'tags' => ['ssl', 'installation', 'failed']
            ],

            // Additional Sales Scenarios
            [
                'subject' => 'Need help with reseller program',
                'details' => 'I am interested in your reseller program. Can you provide information about requirements and benefits?',
                'priority' => 'Medium',
                'type' => 'Question',
                'department' => 'Sales',
                'tags' => ['reseller', 'program', 'requirements']
            ],
            [
                'subject' => 'Want to compare hosting plans',
                'details' => 'I need help comparing your hosting plans to find the best option for my growing business.',
                'priority' => 'Medium',
                'type' => 'Question',
                'department' => 'Sales',
                'tags' => ['compare', 'plans', 'business']
            ],
            [
                'subject' => 'Need help with domain transfer',
                'details' => 'I want to transfer my domain to your service. Can you help me with the transfer process?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Sales',
                'tags' => ['domain', 'transfer', 'process']
            ],
            [
                'subject' => 'Interested in white-label hosting',
                'details' => 'I am looking for white-label hosting solutions for my clients. Do you offer this service?',
                'priority' => 'Medium',
                'type' => 'Question',
                'department' => 'Sales',
                'tags' => ['white-label', 'hosting', 'clients']
            ],
            [
                'subject' => 'Need help with website migration',
                'details' => 'I want to migrate my existing website to your hosting. Can you provide migration assistance?',
                'priority' => 'High',
                'type' => 'Service Request',
                'department' => 'Sales',
                'tags' => ['migration', 'website', 'assistance']
            ],

            // Additional Billing Scenarios
            [
                'subject' => 'Need to change billing cycle',
                'details' => 'I want to change my billing cycle from monthly to quarterly. How can I do this?',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Billing',
                'tags' => ['billing', 'cycle', 'quarterly']
            ],
            [
                'subject' => 'Want to add additional services',
                'details' => 'I want to add additional services to my account. Can you help me with the pricing and setup?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Billing',
                'tags' => ['additional', 'services', 'pricing']
            ],
            [
                'subject' => 'Need help with tax exemption',
                'details' => 'I am a non-profit organization and need help setting up tax exemption for my account.',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Billing',
                'tags' => ['tax', 'exemption', 'non-profit']
            ],
            [
                'subject' => 'Want to downgrade my plan',
                'details' => 'I want to downgrade my hosting plan to save costs. What are my options?',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Billing',
                'tags' => ['downgrade', 'plan', 'costs']
            ],
            [
                'subject' => 'Need help with invoice format',
                'details' => 'I need my invoices in a specific format for my accounting system. Is this possible?',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Billing',
                'tags' => ['invoice', 'format', 'accounting']
            ],

            // Additional Customer Success Scenarios
            [
                'subject' => 'Need help with website analytics',
                'details' => 'I want to set up website analytics to track visitor behavior. Can you help me configure this?',
                'priority' => 'Low',
                'type' => 'Service Request',
                'department' => 'Customer Success',
                'tags' => ['analytics', 'tracking', 'visitors']
            ],
            [
                'subject' => 'Want to learn about caching',
                'details' => 'I want to learn about caching to improve my website performance. Can you provide guidance?',
                'priority' => 'Low',
                'type' => 'Question',
                'department' => 'Customer Success',
                'tags' => ['caching', 'performance', 'guidance']
            ],
            [
                'subject' => 'Need help with content management',
                'details' => 'I need help setting up a content management system for my website. What options do you recommend?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Customer Success',
                'tags' => ['cms', 'content', 'management']
            ],
            [
                'subject' => 'Want to implement user authentication',
                'details' => 'I want to add user authentication to my website. Can you help me set this up?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Customer Success',
                'tags' => ['authentication', 'users', 'login']
            ],
            [
                'subject' => 'Need help with website monitoring',
                'details' => 'I want to set up monitoring for my website to track uptime and performance. What tools do you recommend?',
                'priority' => 'Low',
                'type' => 'Question',
                'department' => 'Customer Success',
                'tags' => ['monitoring', 'uptime', 'performance']
            ],

            // Additional Development Scenarios
            [
                'subject' => 'Request for database optimization',
                'details' => 'I need help optimizing my database for better performance. Can you provide recommendations?',
                'priority' => 'High',
                'type' => 'Service Request',
                'department' => 'Development',
                'tags' => ['database', 'optimization', 'performance']
            ],
            [
                'subject' => 'Bug: Website not working in Internet Explorer',
                'details' => 'My website is not working properly in Internet Explorer. It works fine in other browsers.',
                'priority' => 'Medium',
                'type' => 'Bug Report',
                'department' => 'Development',
                'tags' => ['ie', 'browser', 'compatibility']
            ],
            [
                'subject' => 'Request for API documentation',
                'details' => 'I need comprehensive API documentation for integrating with your services. Can you provide this?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Development',
                'tags' => ['api', 'documentation', 'integration']
            ],
            [
                'subject' => 'Bug: Website not responsive on tablets',
                'details' => 'My website is not responsive on tablet devices. The layout is broken and content is not displaying properly.',
                'priority' => 'High',
                'type' => 'Bug Report',
                'department' => 'Development',
                'tags' => ['responsive', 'tablet', 'layout']
            ],
            [
                'subject' => 'Request for code review',
                'details' => 'I would like to have my website code reviewed for security and performance issues. Is this service available?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Development',
                'tags' => ['code', 'review', 'security']
            ],

            // Additional Management Scenarios
            [
                'subject' => 'Need help with compliance audit',
                'details' => 'We need to conduct a compliance audit for our hosting services. Can you provide the necessary documentation?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Management',
                'tags' => ['compliance', 'audit', 'documentation']
            ],
            [
                'subject' => 'Request for service level review',
                'details' => 'We want to review our current service level agreement and discuss potential improvements.',
                'priority' => 'Low',
                'type' => 'Question',
                'department' => 'Management',
                'tags' => ['sla', 'review', 'improvements']
            ],
            [
                'subject' => 'Need help with vendor management',
                'details' => 'We need assistance with vendor management for our hosting services. Can you provide guidance?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Management',
                'tags' => ['vendor', 'management', 'guidance']
            ],
            [
                'subject' => 'Request for cost optimization',
                'details' => 'We want to optimize our hosting costs while maintaining performance. Can you provide recommendations?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Management',
                'tags' => ['cost', 'optimization', 'performance']
            ],
            [
                'subject' => 'Need help with risk assessment',
                'details' => 'We need to conduct a risk assessment for our hosting services. Can you provide the necessary information?',
                'priority' => 'Medium',
                'type' => 'Service Request',
                'department' => 'Management',
                'tags' => ['risk', 'assessment', 'information']
            ]
        ];

        // Use a meaningful scenario with unique subjects
        // Cycle through scenarios to avoid duplicates as much as possible
        $scenario = $meaningfulScenarios[$index % count($meaningfulScenarios)];
        $subject = $scenario['subject'];
        $details = $scenario['details'];
        $tags = $scenario['tags'];
        $priority = $priorities->where('name', $scenario['priority'])->first() ?? $priorities->random();
        $department = $departments->where('name', $scenario['department'])->first() ?? $departments->random();
        $type = $types->where('name', $scenario['type'])->first() ?? $types->random();

        $status = $statuses->random();
        $category = $categories->random();

        $ticket = Ticket::create([
            'uid' => 'TKT-' . (200000 + ($index ?? rand(1, 999999))),
            'subject' => $subject,
            'details' => $details,
            'user_id' => $customer->id,
            'created_by' => $creator->id,
            'assigned_to' => $agent ? $agent->id : null,
            'priority_id' => $priority->id,
            'status_id' => $status->id,
            'department_id' => $department->id,
            'category_id' => $category->id,
            'type_id' => $type->id,
            'impact_level' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'urgency_level' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'source' => fake()->randomElement(['web', 'email', 'phone', 'chat', 'api']),
            'tags' => $tags,
            'due_date' => $this->calculateDueDate($priority, $status),
            'created_at' => fake()->dateTimeBetween('-90 days', 'now'),
            'updated_at' => fake()->dateTimeBetween('-10 days', 'now'),
        ]);

        // Create basic activities for random tickets
        $this->createBasicTicketActivities($ticket);
    }

    private function createBasicTicketActivities($ticket)
    {
        $activities = [
            [
                'activity_type' => 'created',
                'description' => 'Ticket created',
                'user_id' => $ticket->created_by,
                'created_at' => $ticket->created_at
            ]
        ];

        if ($ticket->assigned_to && $ticket->assignedTo) {
            $activities[] = [
                'activity_type' => 'assigned',
                'description' => 'Ticket assigned to ' . $ticket->assignedTo->first_name,
                'user_id' => $ticket->created_by,
                'created_at' => $ticket->created_at->addMinutes(rand(5, 60))
            ];
        }

        // Add status change activity for some tickets
        if (rand(1, 3) === 1 && $ticket->assigned_to) {
            $activities[] = [
                'activity_type' => 'status_changed',
                'description' => 'Status changed to ' . $ticket->status->name,
                'user_id' => $ticket->assigned_to,
                'created_at' => $ticket->created_at->addHours(rand(1, 24))
            ];
        }

        foreach ($activities as $activity) {
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'activity_type' => $activity['activity_type'],
                'description' => $activity['description'],
                'user_id' => $activity['user_id'],
                'created_at' => $activity['created_at']
            ]);
        }
    }
}
