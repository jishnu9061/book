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
            // Add all missing enhanced fields to tickets table
            if (!Schema::hasColumn('tickets', 'due_date')) {
                $table->timestamp('due_date')->nullable()->after('urgency_level');
            }
            if (!Schema::hasColumn('tickets', 'estimated_hours')) {
                $table->decimal('estimated_hours', 8, 2)->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('tickets', 'actual_hours')) {
                $table->decimal('actual_hours', 8, 2)->nullable()->after('estimated_hours');
            }
            if (!Schema::hasColumn('tickets', 'sla_breach_at')) {
                $table->timestamp('sla_breach_at')->nullable()->after('actual_hours');
            }
            if (!Schema::hasColumn('tickets', 'resolution')) {
                $table->text('resolution')->nullable()->after('sla_breach_at');
            }
            if (!Schema::hasColumn('tickets', 'tags')) {
                $table->json('tags')->nullable()->after('resolution');
            }
            if (!Schema::hasColumn('tickets', 'source')) {
                $table->string('source', 50)->nullable()->after('tags');
            }
            if (!Schema::hasColumn('tickets', 'parent_ticket_id')) {
                $table->unsignedBigInteger('parent_ticket_id')->nullable()->after('source');
            }
            if (!Schema::hasColumn('tickets', 'template_id')) {
                $table->unsignedBigInteger('template_id')->nullable()->after('parent_ticket_id');
            }
            if (!Schema::hasColumn('tickets', 'last_customer_response')) {
                $table->timestamp('last_customer_response')->nullable()->after('template_id');
            }
            if (!Schema::hasColumn('tickets', 'last_agent_response')) {
                $table->timestamp('last_agent_response')->nullable()->after('last_customer_response');
            }
            if (!Schema::hasColumn('tickets', 'custom_fields')) {
                $table->json('custom_fields')->nullable()->after('last_agent_response');
            }
            if (!Schema::hasColumn('tickets', 'external_id')) {
                $table->string('external_id', 100)->nullable()->after('custom_fields');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Remove the columns in reverse order
            if (Schema::hasColumn('tickets', 'external_id')) {
                $table->dropColumn('external_id');
            }
            if (Schema::hasColumn('tickets', 'custom_fields')) {
                $table->dropColumn('custom_fields');
            }
            if (Schema::hasColumn('tickets', 'last_agent_response')) {
                $table->dropColumn('last_agent_response');
            }
            if (Schema::hasColumn('tickets', 'last_customer_response')) {
                $table->dropColumn('last_customer_response');
            }
            if (Schema::hasColumn('tickets', 'template_id')) {
                $table->dropColumn('template_id');
            }
            if (Schema::hasColumn('tickets', 'parent_ticket_id')) {
                $table->dropColumn('parent_ticket_id');
            }
            if (Schema::hasColumn('tickets', 'source')) {
                $table->dropColumn('source');
            }
            if (Schema::hasColumn('tickets', 'tags')) {
                $table->dropColumn('tags');
            }
            if (Schema::hasColumn('tickets', 'resolution')) {
                $table->dropColumn('resolution');
            }
            if (Schema::hasColumn('tickets', 'sla_breach_at')) {
                $table->dropColumn('sla_breach_at');
            }
            if (Schema::hasColumn('tickets', 'actual_hours')) {
                $table->dropColumn('actual_hours');
            }
            if (Schema::hasColumn('tickets', 'estimated_hours')) {
                $table->dropColumn('estimated_hours');
            }
            if (Schema::hasColumn('tickets', 'due_date')) {
                $table->dropColumn('due_date');
            }
        });
    }
};
