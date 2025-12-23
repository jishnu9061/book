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
        Schema::create('ticket_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ticket_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('activity_type'); // created, updated, assigned, status_changed, etc.
            $table->string('field_name')->nullable(); // which field was changed
            $table->text('old_value')->nullable(); // previous value
            $table->text('new_value')->nullable(); // new value
            $table->text('description')->nullable(); // human readable description
            $table->json('metadata')->nullable(); // additional data
            $table->timestamps();
            
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['ticket_id', 'created_at']);
            $table->index(['activity_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_activities');
    }
};
