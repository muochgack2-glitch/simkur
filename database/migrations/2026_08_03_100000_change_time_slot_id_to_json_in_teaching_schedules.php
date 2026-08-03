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
        Schema::table('teaching_schedules', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['time_slot_id']);
        });
        
        Schema::table('teaching_schedules', function (Blueprint $table) {
            // Change time_slot_id from bigint to text to store JSON array
            $table->text('time_slot_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teaching_schedules', function (Blueprint $table) {
            // Revert back to bigint unsigned
            $table->unsignedBigInteger('time_slot_id')->change();
        });
        
        Schema::table('teaching_schedules', function (Blueprint $table) {
            // Recreate foreign key
            $table->foreign('time_slot_id')
                  ->references('id')
                  ->on('time_slots')
                  ->onDelete('cascade');
        });
    }
};
