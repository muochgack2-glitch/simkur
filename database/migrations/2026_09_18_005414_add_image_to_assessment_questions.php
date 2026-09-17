<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('question_text')
                  ->comment('Path gambar soal di storage');
            $table->enum('image_position', ['above', 'below', 'beside_left'])
                  ->nullable()->after('image_path')
                  ->comment('Posisi gambar: di atas, di bawah, atau di samping kiri teks');
        });
    }
    public function down(): void {
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'image_position']);
        });
    }
};