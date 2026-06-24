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
        // Settings table uses key-value pairs, so we just need to insert the new settings
        // No need to alter table structure
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to drop columns since settings uses key-value structure
    }
};
