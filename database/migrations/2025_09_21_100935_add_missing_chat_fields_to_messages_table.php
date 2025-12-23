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
        // Add missing fields to messages table
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'message_type')) {
                $table->enum('message_type', ['text', 'image', 'file', 'system'])->default('text')->after('message');
            }
            if (!Schema::hasColumn('messages', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_read');
            }
        });

        // Create message_attachments table if it doesn't exist
        if (!Schema::hasTable('message_attachments')) {
            Schema::create('message_attachments', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('message_id');
                $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
                $table->string('filename');
                $table->string('file_path');
                $table->bigInteger('file_size');
                $table->string('mime_type');
                $table->timestamps();
            });
        }

        // Create chat_sessions table if it doesn't exist
        if (!Schema::hasTable('chat_sessions')) {
            Schema::create('chat_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('conversation_id');
                $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
                $table->string('session_id')->unique();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('last_activity')->nullable();
                $table->timestamps();
            });
        }

        // Create chat_typing_indicators table if it doesn't exist
        if (!Schema::hasTable('chat_typing_indicators')) {
            Schema::create('chat_typing_indicators', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('conversation_id');
                $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unsignedInteger('contact_id')->nullable();
                $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
                $table->boolean('is_typing')->default(false);
                $table->timestamps();
            });
        }

        // Add indexes for better performance
        Schema::table('messages', function (Blueprint $table) {
            if (!$this->indexExists('messages', 'messages_conversation_id_created_at_index')) {
                $table->index(['conversation_id', 'created_at']);
            }
            if (!$this->indexExists('messages', 'messages_is_read_user_id_index')) {
                $table->index(['is_read', 'user_id']);
            }
        });

        Schema::table('conversations', function (Blueprint $table) {
            if (!$this->indexExists('conversations', 'conversations_contact_id_index')) {
                $table->index(['contact_id']);
            }
            if (!$this->indexExists('conversations', 'conversations_created_at_index')) {
                $table->index('created_at');
            }
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists($table, $indexName)
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_typing_indicators');
        Schema::dropIfExists('chat_sessions');
        Schema::dropIfExists('message_attachments');

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['conversation_id', 'created_at']);
            $table->dropIndex(['is_read', 'user_id']);
            $table->dropColumn(['message_type', 'read_at']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex(['status', 'priority']);
            $table->dropIndex(['contact_id', 'status']);
            $table->dropIndex('last_activity');
        });
    }
};