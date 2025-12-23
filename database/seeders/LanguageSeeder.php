<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('languages')->truncate();
        DB::table('languages')->insert(['name' => 'English', 'code' => 'en', 'flag' => 'us']);
        DB::table('languages')->insert(['name' => 'German', 'code' => 'de', 'flag' => 'de']);
        DB::table('languages')->insert(['name' => 'Chinese', 'code' => 'cn', 'flag' => 'cn']);
        DB::table('languages')->insert(['name' => 'Bengali', 'code' => 'bd', 'flag' => 'bd']);
        DB::table('languages')->insert(['name' => 'Urdu', 'code' => 'ur', 'flag' => 'pk']);
        DB::table('languages')->insert(['name' => 'Dutch', 'code' => 'nl', 'flag' => 'nl']);
        DB::table('languages')->insert(['name' => 'Italian', 'code' => 'it', 'flag' => 'it']);
        DB::table('languages')->insert(['name' => 'Arabic', 'code' => 'sa', 'flag' => 'sa']);
        DB::table('languages')->insert(['name' => 'Turkish', 'code' => 'tr', 'flag' => 'tr']);
        DB::table('languages')->insert(['name' => 'Spanish', 'code' => 'es', 'flag' => 'es']);
        DB::table('languages')->insert(['name' => 'Swedish', 'code' => 'se', 'flag' => 'se']);
        DB::table('languages')->insert(['name' => 'Portuguese', 'code' => 'pt', 'flag' => 'pt']);
        DB::table('languages')->insert(['name' => 'Hebrew', 'code' => 'he', 'flag' => 'il']);
        DB::table('languages')->insert(['name' => 'Lithuanian', 'code' => 'lt', 'flag' => 'lt']);
        DB::table('languages')->insert(['name' => 'Polish', 'code' => 'pl', 'flag' => 'pl']);
        DB::table('languages')->insert(['name' => 'French', 'code' => 'fr', 'flag' => 'fr']);
    }
}
