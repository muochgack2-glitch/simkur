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
        Schema::create('teaching_material_attachments', function (Blueprint $table) {
            $table->id();
            
            // Foreign Key
            $table->foreignId('teaching_material_id')->constrained('teaching_materials')->onDelete('cascade');
            
            // File Info
            $table->string('file_name'); // Original filename
            $table->string('file_path', 500)->nullable(); // Storage path (nullable for links)
            $table->string('file_type', 50); // pdf, docx, pptx, xlsx, jpg, png, mp4, link
            $table->bigInteger('file_size')->nullable(); // in bytes (null for links)
            $table->string('external_link', 500)->nullable(); // For external links (YouTube, Google Drive)
            
            // Attachment Type & Metadata
            $table->enum('attachment_type', [
                'main',                     // Dokumen Utama (required)
                'lkpd',                     // Lembar Kerja Peserta Didik
                'presentation',             // Presentasi/Slide
                'video',                    // Video Pembelajaran
                'assessment',               // Instrumen Asesmen
                'rubric',                   // Rubrik Penilaian
                'answer_key',               // Kunci Jawaban
                'reading_material',         // Bahan Bacaan
                'other'                     // Lainnya
            ])->default('other');
            
            $table->boolean('is_primary')->default(false); // Main file atau lampiran
            $table->text('description')->nullable(); // Deskripsi attachment (optional)
            $table->integer('sort_order')->default(0); // Urutan tampilan
            
            // Tracking
            $table->integer('download_count')->default(0);
            
            // Audit
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes
            $table->index('teaching_material_id');
            $table->index('attachment_type');
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_material_attachments');
    }
};
