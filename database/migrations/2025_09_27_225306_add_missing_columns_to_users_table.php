<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add missing columns that are expected by the application
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city', 50)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address', 200)->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'country_id')) {
                $table->integer('country_id')->nullable()->index()->after('address');
            }
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->integer('role_id')->nullable()->index()->after('country_id');
            }
            if (!Schema::hasColumn('users', 'title')) {
                $table->string('title', 100)->default('Engineer')->nullable()->after('role_id');
            }
            if (!Schema::hasColumn('users', 'locale')) {
                $table->string('locale', 5)->default('en')->after('title');
            }
            if (!Schema::hasColumn('users', 'photo_path')) {
                $table->string('photo_path', 100)->nullable()->after('locale');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove the columns that were added
            $table->dropColumn([
                'phone',
                'city', 
                'address',
                'country_id',
                'role_id',
                'title',
                'locale',
                'photo_path'
            ]);
        });
    }
};
