<?php

namespace App\Console\Commands;

use App\Models\TicketActivity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupDuplicateSlaActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:cleanup-sla-activities {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up duplicate SLA application activities';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Starting cleanup of duplicate SLA activities...');
        
        // Find duplicate SLA activities for the same ticket
        $duplicates = DB::table('ticket_activities')
            ->select('ticket_id', 'action', 'field', 'new_value', DB::raw('COUNT(*) as count'))
            ->where('action', 'sla_applied')
            ->where('field', 'sla_policy_id')
            ->groupBy('ticket_id', 'action', 'field', 'new_value')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate SLA activities found.');
            return 0;
        }

        $this->info("Found {$duplicates->count()} groups of duplicate SLA activities.");

        $totalToDelete = 0;
        $ticketsProcessed = 0;

        foreach ($duplicates as $duplicate) {
            $ticketId = $duplicate->ticket_id;
            $count = $duplicate->count;
            $toDelete = $count - 1; // Keep one, delete the rest
            $totalToDelete += $toDelete;
            $ticketsProcessed++;

            $this->line("Ticket #{$ticketId}: {$count} duplicate SLA activities (will delete {$toDelete})");

            if (!$dryRun) {
                // Get all duplicate activities for this ticket, ordered by created_at
                $activities = TicketActivity::where('ticket_id', $ticketId)
                    ->where('action', 'sla_applied')
                    ->where('field', 'sla_policy_id')
                    ->where('new_value', $duplicate->new_value)
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Keep the first one, delete the rest
                $activities->skip(1)->each(function ($activity) {
                    $activity->delete();
                });
            }
        }

        if ($dryRun) {
            $this->warn("DRY RUN: Would delete {$totalToDelete} duplicate SLA activities from {$ticketsProcessed} tickets.");
            $this->info('Run without --dry-run to actually perform the cleanup.');
        } else {
            $this->info("Successfully deleted {$totalToDelete} duplicate SLA activities from {$ticketsProcessed} tickets.");
        }

        return 0;
    }
}