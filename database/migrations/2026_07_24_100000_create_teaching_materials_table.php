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
        Schema::create('teaching_materials', function (Blueprint $table) {
            $table->id();
            
            // Metadata Dasar
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', [
                'atp',                      // Alur Tujuan Pembelajaran
                'cp',                       // Capaian Pembelajaran
                'modul_ajar',               // Modul Ajar (Lengkap dengan LKPD, Asesmen, Rubrik)
                'buku_teks',                // Buku Teks / E-Book
                'video_pembelajaran',       // Video Pembelajaran
                'presentasi_infografis',    // Presentasi / Infografis
                'bahan_bacaan',             // Bahan Bacaan / Artikel
                'bank_soal',                // Bank Soal / Paket Soal (UTS, UAS, Kuis)
                'rubrik_penilaian_umum',    // Rubrik Penilaian Umum
                'job_sheet',                // Job Sheet / Panduan Praktikum
                'teaching_factory',         // Teaching Factory
                'pkl'                       // PKL (Praktik Kerja Lapangan)
            ]);
            
            // Relasi dengan Kurikulum
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->enum('grade', ['X', 'XI', 'XII'])->nullable();
            $table->enum('phase', ['E', 'F'])->nullable(); // E=Kelas X, F=Kelas XI-XII
            $table->enum('semester', ['1', '2'])->nullable();
            
            // File Management
            $table->enum('file_type', ['pdf', 'docx', 'pptx', 'xlsx', 'jpg', 'png', 'mp4', 'link']);
            $table->string('file_path', 500)->nullable();
            $table->bigInteger('file_size')->nullable(); // in bytes
            $table->string('external_link', 500)->nullable();
            
            // Integrasi 8 Dimensi Profil Lulusan
            $table->boolean('dimension_1_beriman')->default(false);
            $table->boolean('dimension_2_kebinekaan')->default(false);
            $table->boolean('dimension_3_gotong_royong')->default(false);
            $table->boolean('dimension_4_mandiri')->default(false);
            $table->boolean('dimension_5_bernalar_kritis')->default(false);
            $table->boolean('dimension_6_kreatif')->default(false);
            $table->boolean('dimension_7_numerasi')->default(false);
            $table->boolean('dimension_8_literasi')->default(false);
            
            // Metadata Tambahan
            $table->json('tags')->nullable(); // ["kewirausahaan", "digital"]
            $table->json('target_class_ids')->nullable(); // [1,2,3]
            $table->boolean('is_public')->default(false);
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            
            // Approval Workflow
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected'])->default('draft');
            $table->text('approval_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Audit Trail
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index('category');
            $table->index('status');
            $table->index('grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_materials');
    }
};
