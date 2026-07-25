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
        Schema::table('teaching_materials', function (Blueprint $table) {
            // Version tracking
            $table->unsignedBigInteger('parent_material_id')->nullable()->after('id');
            $table->integer('version_number')->default(1)->after('parent_material_id');
            $table->string('revision_notes')->nullable()->after('version_number');
            
            // Foreign key
            $table->foreign('parent_material_id')
                  ->references('id')
                  ->on('teaching_materials')
                  ->onDelete('set null');
            
            // Index for performance
            $table->index('parent_material_id');
            $table->index(['parent_material_id', 'version_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teaching_materials', function (Blueprint $table) {
            $table->dropForeign(['parent_material_id']);
            $table->dropIndex(['parent_material_id', 'version_number']);
            $table->dropIndex(['parent_material_id']);
            $table->dropColumn(['parent_material_id', 'version_number', 'revision_notes']);
        });
    }
};
