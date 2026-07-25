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
        Schema::create('teaching_material_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_material_id')->constrained('teaching_materials')->onDelete('cascade');
            $table->foreignId('shared_with_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('shared_with_class_id')->nullable()->constrained('classes')->onDelete('cascade');
            $table->boolean('can_edit')->default(false);
            $table->boolean('can_download')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_material_shares');
    }
};
