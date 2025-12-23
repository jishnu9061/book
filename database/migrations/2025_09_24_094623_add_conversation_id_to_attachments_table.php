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
        Schema::table('attachments', function (Blueprint $table) {
            if (!Schema::hasColumn('attachments', 'conversation_id')) {
                $table->bigInteger('conversation_id')->unsigned()->nullable()->after('ticket_id');
                $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
                $table->index('conversation_id', 'attachments_conversation_id_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            if (Schema::hasColumn('attachments', 'conversation_id')) {
                $table->dropForeign(['conversation_id']);
                $table->dropIndex('attachments_conversation_id_index');
                $table->dropColumn('conversation_id');
            }
        });
    }
};