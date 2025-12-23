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
        Schema::table('pending_users', function (Blueprint $table) {
            // Add country_id column if it doesn't exist
            if (!Schema::hasColumn('pending_users', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pending_users', function (Blueprint $table) {
            // Remove country_id column if it exists
            if (Schema::hasColumn('pending_users', 'country_id')) {
                $table->dropColumn('country_id');
            }
        });
    }
};
