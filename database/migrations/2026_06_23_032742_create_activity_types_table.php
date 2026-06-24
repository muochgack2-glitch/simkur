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
        Schema::create('activity_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique()->comment('MPLS, PTS, PAS, PAT, etc');
            $table->enum('category', ['akademik', 'non_akademik'])->default('akademik');
            $table->string('default_color', 7)->default('#3B82F6')->comment('Hex color');
            $table->boolean('is_holiday')->default(false)->comment('Apakah termasuk hari libur');
            $table->boolean('is_exam')->default(false)->comment('Apakah termasuk hari ujian');
            $table->boolean('is_system')->default(true)->comment('System default atau custom');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('category');
            $table->index('is_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_types');
    }
};
