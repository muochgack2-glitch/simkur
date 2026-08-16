<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('personal'); // personal, group
            $table->string('recipient');
            $table->text('message');
            $table->text('response')->nullable();
            $table->string('status')->default('pending'); // pending, sent, failed, error
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_logs');
    }
};