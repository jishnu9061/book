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
            // Add sla_policy_id column if it doesn't exist
            if (!Schema::hasColumn('tickets', 'sla_policy_id')) {
                $table->unsignedBigInteger('sla_policy_id')->nullable()->after('type_id');
                $table->foreign('sla_policy_id')->references('id')->on('sla_policies')->onDelete('set null');
                $table->index('sla_policy_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'sla_policy_id')) {
                $table->dropForeign(['sla_policy_id']);
                $table->dropIndex(['sla_policy_id']);
                $table->dropColumn('sla_policy_id');
            }
        });
    }
};