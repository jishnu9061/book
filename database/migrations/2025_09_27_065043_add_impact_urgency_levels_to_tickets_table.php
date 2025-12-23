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
        Schema::table('tickets', function (Blueprint $table) {
            // Add impact_level and urgency_level columns if they don't exist
            if (!Schema::hasColumn('tickets', 'impact_level')) {
                $table->string('impact_level', 20)->nullable()->after('type_id');
            }
            if (!Schema::hasColumn('tickets', 'urgency_level')) {
                $table->string('urgency_level', 20)->nullable()->after('impact_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Remove the columns if they exist
            if (Schema::hasColumn('tickets', 'urgency_level')) {
                $table->dropColumn('urgency_level');
            }
            if (Schema::hasColumn('tickets', 'impact_level')) {
                $table->dropColumn('impact_level');
            }
        });
    }
};
