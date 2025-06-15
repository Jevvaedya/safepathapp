<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_user_custom_audios_table.php (nama file bisa berbeda)
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // GANTI NAMA TABEL DI SINI MENJADI SINGULAR
        Schema::create('user_custom_audio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        // GANTI NAMA TABEL DI SINI MENJADI SINGULAR
        Schema::dropIfExists('user_custom_audio');
    }
};