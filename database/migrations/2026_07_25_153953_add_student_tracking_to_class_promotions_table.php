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
        Schema::table('class_promotions', function (Blueprint $table) {
            $table->json('student_details')->nullable()->after('promotion_summary');
            $table->boolean('is_rolled_back')->default(false)->after('notes');
            $table->timestamp('rolled_back_at')->nullable()->after('is_rolled_back');
            $table->unsignedBigInteger('rolled_back_by')->nullable()->after('rolled_back_at');
            
            $table->foreign('rolled_back_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_promotions', function (Blueprint $table) {
            $table->dropForeign(['rolled_back_by']);
            $table->dropColumn(['student_details', 'is_rolled_back', 'rolled_back_at', 'rolled_back_by']);
        });
    }
};
