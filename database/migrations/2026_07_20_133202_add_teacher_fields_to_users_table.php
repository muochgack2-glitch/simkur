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
        Schema::table('users', function (Blueprint $table) {
            // Field untuk Guru
            $table->string('nip_nuptk', 20)->nullable()->after('username'); // NIP/NUPTK Guru
            $table->integer('beban_mengajar')->nullable()->after('major'); // Beban mengajar (jam/minggu)
            $table->json('taught_majors')->nullable()->after('beban_mengajar'); // Jurusan yang diampu (array: MPLB, AKL, BUSANA)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip_nuptk', 'beban_mengajar', 'taught_majors']);
        });
    }
};
