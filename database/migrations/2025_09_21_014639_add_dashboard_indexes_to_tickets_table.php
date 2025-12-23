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
            // Add indexes for dashboard queries
            $table->index(['user_id', 'created_at'], 'idx_tickets_user_created');
            $table->index(['assigned_to', 'created_at'], 'idx_tickets_assigned_created');
            $table->index(['status_id', 'created_at'], 'idx_tickets_status_created');
            $table->index(['department_id', 'created_at'], 'idx_tickets_department_created');
            $table->index(['type_id', 'created_at'], 'idx_tickets_type_created');
            $table->index(['created_at'], 'idx_tickets_created_at');
            $table->index(['response'], 'idx_tickets_response');
            $table->index(['assigned_to'], 'idx_tickets_assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('idx_tickets_user_created');
            $table->dropIndex('idx_tickets_assigned_created');
            $table->dropIndex('idx_tickets_status_created');
            $table->dropIndex('idx_tickets_department_created');
            $table->dropIndex('idx_tickets_type_created');
            $table->dropIndex('idx_tickets_created_at');
            $table->dropIndex('idx_tickets_response');
            $table->dropIndex('idx_tickets_assigned_to');
        });
    }
};