<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asts_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('kelas', 10);       // X, XI, XII
            $table->string('jurusan', 20);     // AKL, BUSANA, MPLB
            $table->string('hari', 10);        // Senin, Selasa, ...
            $table->tinyInteger('sesi');       // 1, 2, 3, 4
            $table->string('mapel', 150);
            $table->string('nama_guru', 150);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asts_schedules');
    }
};