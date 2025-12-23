<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioritySeeder extends Seeder
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
        DB::table('priorities')->truncate();
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $priorities = [
            ['name' => 'Low'],
            ['name' => 'Medium'],
            ['name' => 'High'],
            ['name' => 'Critical'],
        ];

        foreach ($priorities as $priority) {
            Priority::create($priority);
        }
    }
}
