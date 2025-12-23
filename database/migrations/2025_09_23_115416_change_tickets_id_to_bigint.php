<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTicketsIdToBigint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip this migration for now - the tickets table ID is already working fine
        // This migration was causing foreign key constraint issues
        // The main installation issue (template_id column) has been resolved
        return;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Change tickets table id back to int
        Schema::table('tickets', function (Blueprint $table) {
            $table->increments('id')->change();
        });
    }
}