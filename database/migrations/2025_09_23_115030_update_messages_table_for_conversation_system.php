<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMessagesTableForConversationSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('messages', 'is_internal')) {
                $table->boolean('is_internal')->default(false)->after('is_read');
            }
            if (!Schema::hasColumn('messages', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_internal');
            }
            if (!Schema::hasColumn('messages', 'message_type')) {
                $table->string('message_type')->default('text')->after('read_at');
            }
        });

        // Add indexes
        Schema::table('messages', function (Blueprint $table) {
            $indexes = [
                'messages_is_internal_index' => ['is_internal'],
                'messages_read_at_index' => ['read_at'],
                'messages_message_type_index' => ['message_type'],
            ];

            foreach ($indexes as $indexName => $columns) {
                if (Schema::hasColumn('messages', $columns[0]) && !Schema::hasIndex('messages', $indexName)) {
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
        Schema::table('messages', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('messages_is_internal_index');
            $table->dropIndex('messages_read_at_index');
            $table->dropIndex('messages_message_type_index');
            
            // Drop columns
            $table->dropColumn([
                'is_internal',
                'read_at',
                'message_type'
            ]);
        });
    }
}