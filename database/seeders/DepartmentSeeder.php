<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
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
        DB::table('departments')->truncate();
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $departments = [
            ['id' => 1, 'name' => 'Sales'],
            ['id' => 2, 'name' => 'Management'],
            ['id' => 3, 'name' => 'Technical Support'],
            ['id' => 4, 'name' => 'Billing'],
            ['id' => 5, 'name' => 'Customer Success'],
            ['id' => 6, 'name' => 'Development'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
