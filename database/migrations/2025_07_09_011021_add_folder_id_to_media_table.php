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
        Schema::table('media', function (Blueprint $table) {
            // We add our foreign key. It's nullable so existing media isn't affected,
            // and files can exist in the "root" directory (null folder_id).
            $table->foreignId('folder_id')->nullable()->after('uuid')->constrained('media_folders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // It's important to define how to reverse the migration.
            $table->dropForeign(['folder_id']);
            $table->dropColumn('folder_id');
        });
    }
};
