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
            //
        Schema::table('users', function (Blueprint $table) {
            $table->string('agama')->nullable()->after('nis');
        });
    }

    /**
     * Reverse the migrations.
     */
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('agama');
        });
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};