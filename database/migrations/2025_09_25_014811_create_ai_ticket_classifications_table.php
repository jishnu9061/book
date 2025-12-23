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
        if (Schema::hasTable('ai_ticket_classifications')) {
            return;
        }
        
        Schema::create('ai_ticket_classifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('priority_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('department_id')->nullable();
            $table->unsignedInteger('type_id')->nullable();
            $table->decimal('confidence_score', 5, 2)->default(0.00);
            $table->text('reasoning')->nullable();
            $table->boolean('ai_generated')->default(true);
            $table->json('classification_data')->nullable();
            $table->boolean('applied')->default(false);
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['ticket_id', 'created_at']);
            $table->index(['confidence_score']);
            $table->index(['ai_generated']);
            $table->index(['applied']);
        });

        // Add foreign key constraints
        Schema::table('ai_ticket_classifications', function (Blueprint $table) {
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('priority_id')->references('id')->on('priorities')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_ticket_classifications');
    }
};
