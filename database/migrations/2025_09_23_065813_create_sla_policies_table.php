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
        Schema::create('sla_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            
            // SLA Timeframes
            $table->integer('first_response_time')->nullable(); // in minutes
            $table->integer('resolution_time')->nullable(); // in minutes
            
            // Conditions for applying SLA
            $table->json('priority_conditions')->nullable(); // which priorities this applies to
            $table->json('department_conditions')->nullable(); // which departments
            $table->json('category_conditions')->nullable(); // which categories
            $table->json('type_conditions')->nullable(); // which ticket types
            
            // Business hours
            $table->json('business_hours')->nullable(); // working hours configuration
            $table->json('holidays')->nullable(); // holiday calendar
            
            // Escalation rules
            $table->integer('escalation_time')->nullable(); // when to escalate (in minutes)
            $table->json('escalation_actions')->nullable(); // what to do when escalated
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sla_policies');
    }
};