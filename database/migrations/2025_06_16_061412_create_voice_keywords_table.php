<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_voice_keywords_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voice_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link ke user
            $table->string('keyword'); // Kata kunci yang disimpan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voice_keywords');
    }
};