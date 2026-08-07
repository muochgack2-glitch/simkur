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
        Schema::table('teaching_journals', function (Blueprint $table) {
            // Ubah learning_objective dari string (varchar 255) ke text untuk menampung data yang lebih panjang
            $table->text('learning_objective')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teaching_journals', function (Blueprint $table) {
            // Kembalikan ke string jika rollback
            $table->string('learning_objective')->nullable()->change();
        });
    }
};
