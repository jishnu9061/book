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
        Schema::create('auto_assignment_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // rule priority (higher = more important)
            
            // Conditions
            $table->json('conditions')->nullable(); // conditions for when to apply this rule
            
            // Assignment logic
            $table->enum('assignment_type', ['user', 'department', 'round_robin', 'workload_based'])->default('user');
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->unsignedInteger('assigned_department_id')->nullable();
            $table->json('assignment_config')->nullable(); // additional configuration
            
            // Workload balancing
            $table->integer('max_tickets_per_user')->nullable();
            $table->boolean('consider_workload')->default(true);
            $table->boolean('consider_skills')->default(false);
            
            $table->timestamps();
            
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_assignment_rules');
    }
};