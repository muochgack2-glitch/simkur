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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Mata Pelajaran (e.g., Matematika, Bahasa Indonesia)
            $table->string('code', 10)->nullable(); // Kode Mata Pelajaran (e.g., MTK, BIND)
            $table->text('description')->nullable(); // Deskripsi mata pelajaran
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
