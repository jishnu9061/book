<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $idx) {
            if ($idx->Key_name === $index) {
                return true;
            }
        }
        return false;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add performance indexes for chat tables
        Schema::table('conversations', function (Blueprint $table) {
            // Index for filtering by status and priority (only if not exists)
            if (!$this->indexExists('conversations', 'conversations_status_priority_index')) {
                $table->index(['status', 'priority']);
            }
            
            // Index for contact-based queries (only if not exists)
            if (!$this->indexExists('conversations', 'conversations_contact_id_status_index')) {
                $table->index(['contact_id', 'status']);
            }
            
            // Index for last activity sorting (only if not exists)
            if (!$this->indexExists('conversations', 'conversations_last_activity_index')) {
                $table->index('last_activity');
            }
            
            // Index for department filtering (only if not exists)
            if (!$this->indexExists('conversations', 'conversations_department_index')) {
                $table->index('department');
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            // Composite index for conversation messages with ordering (only if not exists)
            if (!$this->indexExists('messages', 'messages_conversation_id_created_at_index')) {
                $table->index(['conversation_id', 'created_at']);
            }
            
            // Index for unread messages queries (only if not exists)
            if (!$this->indexExists('messages', 'messages_is_read_user_id_index')) {
                $table->index(['is_read', 'user_id']);
            }
            
            // Index for contact messages (only if not exists)
            if (!$this->indexExists('messages', 'messages_contact_id_created_at_index')) {
                $table->index(['contact_id', 'created_at']);
            }
            
            // Index for message type filtering (only if not exists)
            if (!$this->indexExists('messages', 'messages_message_type_index')) {
                $table->index('message_type');
            }
        });

        Schema::table('participants', function (Blueprint $table) {
            // Index for user-based participant queries
            $table->index(['user_id', 'conversation_id']);
            
            // Index for contact-based participant queries
            $table->index(['contact_id', 'conversation_id']);
        });

        // Add indexes for typing indicators if table exists
        if (Schema::hasTable('chat_typing_indicators')) {
            Schema::table('chat_typing_indicators', function (Blueprint $table) {
                $table->index(['conversation_id', 'is_typing']);
                $table->index(['user_id', 'is_typing']);
                $table->index(['contact_id', 'is_typing']);
                $table->index('updated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex(['status', 'priority']);
            $table->dropIndex(['contact_id', 'status']);
            $table->dropIndex('last_activity');
            $table->dropIndex('department');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['conversation_id', 'created_at']);
            $table->dropIndex(['is_read', 'user_id']);
            $table->dropIndex(['contact_id', 'created_at']);
            $table->dropIndex('message_type');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'conversation_id']);
            $table->dropIndex(['contact_id', 'conversation_id']);
        });

        if (Schema::hasTable('chat_typing_indicators')) {
            Schema::table('chat_typing_indicators', function (Blueprint $table) {
                $table->dropIndex(['conversation_id', 'is_typing']);
                $table->dropIndex(['user_id', 'is_typing']);
                $table->dropIndex(['contact_id', 'is_typing']);
                $table->dropIndex('updated_at');
            });
        }
    }
};
