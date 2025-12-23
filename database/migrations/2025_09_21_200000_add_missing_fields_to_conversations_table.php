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
            // Add missing fields that are used in the code but not in the table
            if (!Schema::hasColumn('conversations', 'status')) {
                $table->enum('status', ['active', 'inactive', 'resolved', 'closed'])->default('active')->after('title');
            }
            if (!Schema::hasColumn('conversations', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('status');
            }
            if (!Schema::hasColumn('conversations', 'department')) {
                $table->string('department', 50)->default('general')->after('priority');
            }
            if (!Schema::hasColumn('conversations', 'source')) {
                $table->string('source', 50)->default('website')->after('department');
            }
            if (!Schema::hasColumn('conversations', 'metadata')) {
                $table->json('metadata')->nullable()->after('source');
            }
            if (!Schema::hasColumn('conversations', 'last_activity')) {
                $table->timestamp('last_activity')->nullable()->after('metadata');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'priority', 
                'department',
                'source',
                'metadata',
                'last_activity'
            ]);
        });
    }
};
