<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\NewTicketNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('notifications')->count() > 0) {
            $this->command->warn('The `notifications` table is not empty. Truncating to avoid duplicates...');
            DB::table('notifications')->truncate();
        } else {
            $this->command->info('The `notifications` table is already empty. Proceeding with seeding...');
        }

        // --- Step 1: Find all potential admin users using your provided logic ---
        // We fetch ALL users with an admin-like role first for efficiency.
        // NOTE: This assumes a 'role' relationship exists on your User model.
        $allAdmins = User::whereHas('role', fn ($q) => $q->where('slug', 'like', '%admin%'))->get(); // <-- Your logic here

        if ($allAdmins->isEmpty()) {
            $this->command->error('No admin users found based on role slug. Aborting.');
            return;
        }
        $this->command->info(sprintf('Found %d total admin user(s) to potentially notify.', $allAdmins->count()));


        // --- Step 2: Get all existing tickets ---
        // We use `with('user')` to eager-load the creator, which is more efficient.
        $tickets = Ticket::with('user')->get();

        if ($tickets->isEmpty()) {
            $this->command->info('No existing tickets found. Nothing to seed.');
            return;
        }


        // --- Step 3: Loop through tickets and create notifications ---
        $this->command->info(sprintf('Preparing to create notifications for %d tickets...', $tickets->count()));

        $bar = $this->command->getOutput()->createProgressBar($tickets->count());
        $bar->start();

        foreach ($tickets as $ticket) {
            // For each ticket, we determine the list of admins to notify.
            // We start with the full list of admins...
            $adminsToNotify = $allAdmins;

            // ...and then filter out the original creator of this specific ticket, if they exist.
            // This prevents notifying a user about a ticket they created.
            if ($ticket->user) {
                $adminsToNotify = $allAdmins->where('id', '!=', $ticket->user->id);
            }

            // Only proceed if there are admins left to notify for this ticket
            if ($adminsToNotify->isNotEmpty()) {
                $notificationInstance = new NewTicketNotification($ticket);
                Notification::sendNow($adminsToNotify, $notificationInstance);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->info("\n✅ Successfully created notifications for all existing tickets.");
    }
}
