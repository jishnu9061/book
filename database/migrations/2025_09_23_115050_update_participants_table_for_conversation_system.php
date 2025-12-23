<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateParticipantsTableForConversationSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('participants', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('participants', 'role')) {
                $table->string('role')->default('participant')->after('contact_id');
            }
            if (!Schema::hasColumn('participants', 'joined_at')) {
                $table->timestamp('joined_at')->nullable()->after('role');
            }
        });

        // Add indexes
        Schema::table('participants', function (Blueprint $table) {
            $indexes = [
                'participants_role_index' => ['role'],
                'participants_joined_at_index' => ['joined_at'],
            ];

            foreach ($indexes as $indexName => $columns) {
                if (Schema::hasColumn('participants', $columns[0]) && !Schema::hasIndex('participants', $indexName)) {
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
        Schema::table('participants', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('participants_role_index');
            $table->dropIndex('participants_joined_at_index');
            
            // Drop columns
            $table->dropColumn([
                'role',
                'joined_at'
            ]);
        });
    }
}