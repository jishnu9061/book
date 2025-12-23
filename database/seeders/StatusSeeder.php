<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('status')->truncate();
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $statuses = [
            ['name' => 'Open', 'slug' => 'open'],
            ['name' => 'Pending', 'slug' => 'pending'],
            ['name' => 'In Progress', 'slug' => 'in_progress'],
            ['name' => 'Resolved', 'slug' => 'resolved'],
            ['name' => 'Closed', 'slug' => 'closed'],
            ['name' => 'Cancelled', 'slug' => 'cancelled'],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }
    }
}
