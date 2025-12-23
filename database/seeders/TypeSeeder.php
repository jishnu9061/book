<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeSeeder extends Seeder
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
        DB::table('types')->truncate();
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $types = [
            ['name' => 'Bug Report'],
            ['name' => 'Feature Request'],
            ['name' => 'Question'],
            ['name' => 'Service Request'],
            ['name' => 'Incident'],
            ['name' => 'Maintenance'],
        ];

        foreach ($types as $type) {
            Type::create($type);
        }
    }
}
