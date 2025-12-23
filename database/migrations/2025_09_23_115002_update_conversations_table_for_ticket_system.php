<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateConversationsTableForTicketSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip column creation since they already exist
        // The conversations table already has the required columns

        // Add foreign key constraints
        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'ticket_id') && !Schema::hasIndex('conversations', 'conversations_ticket_id_foreign')) {
                $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            }
            if (Schema::hasColumn('conversations', 'created_by') && !Schema::hasIndex('conversations', 'conversations_created_by_foreign')) {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        // Add indexes
        Schema::table('conversations', function (Blueprint $table) {
            $indexes = [
                'conversations_ticket_id_index' => ['ticket_id'],
                'conversations_type_index' => ['type'],
                'conversations_status_index' => ['status'],
                'conversations_last_message_at_index' => ['last_message_at'],
            ];

            foreach ($indexes as $indexName => $columns) {
                if (Schema::hasColumn('conversations', $columns[0]) && !Schema::hasIndex('conversations', $indexName)) {
                    $table->index($columns, $indexName);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['ticket_id']);
            $table->dropForeign(['created_by']);
            
            // Drop indexes
            $table->dropIndex('conversations_ticket_id_index');
            $table->dropIndex('conversations_type_index');
            $table->dropIndex('conversations_status_index');
            $table->dropIndex('conversations_last_message_at_index');
            
            // Drop columns
            $table->dropColumn([
                'ticket_id',
                'type',
                'subject',
                'created_by',
                'context',
                'last_message_at',
                'status'
            ]);
        });
    }
}