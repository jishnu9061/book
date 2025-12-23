<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TicketCommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing comments
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('comments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tickets = Ticket::all();
        $users = User::all();

        if ($tickets->isEmpty() || $users->isEmpty()) {
            $this->command->error('No tickets or users found. Please run ticket and user seeders first.');
            return;
        }

        $commentTemplates = [
            // Technical Support Comments
            [
                'details' => 'I have investigated the issue and found that the server resources are being heavily utilized. I have optimized the database queries and increased the server capacity. The website should now load faster.',
                'context' => 'technical_response'
            ],
            [
                'details' => 'I have checked the email configuration and found that the SMTP settings were incorrect. I have updated the configuration and sent a test email. Please check if you receive notifications now.',
                'context' => 'technical_response'
            ],
            [
                'details' => 'I have identified the root cause of the database timeout errors. The issue is related to a missing index on the users table. I have added the necessary index and the errors should be resolved.',
                'context' => 'technical_response'
            ],

            // Customer Comments
            [
                'details' => 'Thank you for your quick response. The website is now loading much faster. I appreciate your help in resolving this issue.',
                'context' => 'customer_feedback'
            ],
            [
                'details' => 'I am still not receiving email notifications. Can you please check again? This is affecting my business operations.',
                'context' => 'customer_followup'
            ],
            [
                'details' => 'The database errors have stopped occurring. Thank you for fixing this issue so quickly.',
                'context' => 'customer_feedback'
            ],

            // Sales Comments
            [
                'details' => 'Thank you for your interest in our premium plan. I have sent you a detailed proposal with all the features, pricing, and migration process. Please let me know if you have any questions.',
                'context' => 'sales_response'
            ],
            [
                'details' => 'I have prepared a step-by-step guide for setting up your custom domain. I will also schedule a call with you to walk through the process. Please let me know your availability.',
                'context' => 'sales_response'
            ],

            // Billing Comments
            [
                'details' => 'I have investigated the payment issue and found that your bank has flagged the transaction as suspicious. I have contacted your bank and the payment should now go through. Please try again.',
                'context' => 'billing_response'
            ],
            [
                'details' => 'I have reviewed your usage and found that you were indeed charged incorrectly. I have issued a credit to your account and sent you a corrected invoice.',
                'context' => 'billing_response'
            ],

            // Customer Success Comments
            [
                'details' => 'I have started working on your account migration. I have created a detailed migration plan and will begin transferring your data. This process will take approximately 2-3 business days.',
                'context' => 'customer_success_response'
            ],
            [
                'details' => 'I have scheduled a training session for your team on Friday at 2 PM. I will send you the meeting details and agenda shortly.',
                'context' => 'customer_success_response'
            ],

            // Development Comments
            [
                'details' => 'Thank you for the feature request. This is a great idea and we will add it to our development roadmap. I will keep you updated on the progress.',
                'context' => 'development_response'
            ],
            [
                'details' => 'I have identified the issue with the iOS 17 compatibility. The problem is related to a deprecated API. I have fixed the issue and the app should now work correctly on iOS 17.',
                'context' => 'development_response'
            ],

            // Management Comments
            [
                'details' => 'I have generated the access audit report for your account. The report shows all user access for the last 30 days. I will send it to you via secure email.',
                'context' => 'management_response'
            ],
            [
                'details' => 'I have scheduled a call with your account manager for next Tuesday at 10 AM to discuss your contract renewal. Please let me know if this time works for you.',
                'context' => 'management_response'
            ],

            // Internal Comments
            [
                'details' => 'Internal Note: This customer has been experiencing multiple issues. Please prioritize their tickets and provide extra attention.',
                'context' => 'internal_note'
            ],
            [
                'details' => 'Internal Note: Customer is a VIP client. Escalate any issues immediately.',
                'context' => 'internal_note'
            ],
            [
                'details' => 'Internal Note: This is a recurring issue. We need to implement a permanent solution.',
                'context' => 'internal_note'
            ]
        ];

        foreach ($tickets as $ticket) {
            // Add 1-4 comments per ticket (realistic conversation length)
            $commentCount = rand(1, 4);
            
            for ($i = 0; $i < $commentCount; $i++) {
                $template = $commentTemplates[array_rand($commentTemplates)];
                $user = $users->random();
                
                // For internal notes, use admin/manager users
                if ($template['context'] === 'internal_note') {
                    $user = $users->whereIn('role_id', [1, 2])->random();
                }
                
                // For customer comments, use the ticket owner
                if (in_array($template['context'], ['customer_feedback', 'customer_followup'])) {
                    $user = $users->find($ticket->user_id);
                }

                Comment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'details' => $template['details'],
                    'created_at' => $ticket->created_at->addHours(rand(1, 72)), // Extended time range
                    'updated_at' => $ticket->created_at->addHours(rand(1, 72))
                ]);
            }
        }

        $this->command->info('Created ' . Comment::count() . ' comments for tickets');
    }
}
