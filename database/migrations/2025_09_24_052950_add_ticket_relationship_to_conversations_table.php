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
        Schema::table('conversations', function (Blueprint $table) {
            // Add ticket_id column if it doesn't exist
            if (!Schema::hasColumn('conversations', 'ticket_id')) {
                $table->unsignedInteger('ticket_id')->nullable()->after('contact_id');
                $table->index('ticket_id');
            }
            
            // Add type column if it doesn't exist
            if (!Schema::hasColumn('conversations', 'type')) {
                $table->enum('type', ['internal', 'customer', 'support'])->default('internal')->after('ticket_id');
                $table->index('type');
            }
            
            // Add created_by column if it doesn't exist
            if (!Schema::hasColumn('conversations', 'created_by')) {
                $table->unsignedInteger('created_by')->nullable()->after('type');
                $table->index('created_by');
            }
            
            // Add context column if it doesn't exist
            if (!Schema::hasColumn('conversations', 'context')) {
                $table->json('context')->nullable()->after('created_by');
            }
            
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('conversations', 'status')) {
                $table->enum('status', ['active', 'archived', 'closed'])->default('active')->after('context');
                $table->index('status');
            }
            
            // Add last_message_at column if it doesn't exist
            if (!Schema::hasColumn('conversations', 'last_message_at')) {
                $table->timestamp('last_message_at')->nullable()->after('status');
                $table->index('last_message_at');
            }
        });
        
        // Skip foreign key constraints for now to avoid constraint issues
        // They can be added later if needed
        // Schema::table('conversations', function (Blueprint $table) {
        //     if (Schema::hasColumn('conversations', 'ticket_id') && !Schema::hasIndex('conversations', 'conversations_ticket_id_foreign')) {
        //         $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
        //     }
        //     if (Schema::hasColumn('conversations', 'created_by') && !Schema::hasIndex('conversations', 'conversations_created_by_foreign')) {
        //         $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        //     }
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasIndex('conversations', 'conversations_ticket_id_foreign')) {
                $table->dropForeign(['ticket_id']);
            }
            if (Schema::hasIndex('conversations', 'conversations_created_by_foreign')) {
                $table->dropForeign(['created_by']);
            }
            
            // Drop indexes
            if (Schema::hasIndex('conversations', 'conversations_ticket_id_index')) {
                $table->dropIndex('conversations_ticket_id_index');
            }
            if (Schema::hasIndex('conversations', 'conversations_type_index')) {
                $table->dropIndex('conversations_type_index');
            }
            if (Schema::hasIndex('conversations', 'conversations_status_index')) {
                $table->dropIndex('conversations_status_index');
            }
            if (Schema::hasIndex('conversations', 'conversations_last_message_at_index')) {
                $table->dropIndex('conversations_last_message_at_index');
            }
            
            // Drop columns
            $columnsToDrop = ['ticket_id', 'type', 'created_by', 'context', 'status', 'last_message_at'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('conversations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};