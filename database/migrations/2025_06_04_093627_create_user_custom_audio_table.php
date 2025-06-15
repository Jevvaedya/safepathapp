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
        // Nama tabel akan 'user_custom_audios' (plural dari UserCustomAudio)
        Schema::create('user_custom_audios', function (Blueprint $table) {
            $table->id(); // Kolom ID auto-increment (Primary Key)
            
            // Kolom untuk foreign key ke tabel users
            // 'users' adalah nama tabel pengguna Anda (default Laravel)
            // onDelete('cascade') berarti jika user dihapus, semua audio kustomnya juga akan terhapus
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('file_name');      // Untuk menyimpan nama asli file yang diunggah
            $table->string('file_path');      // Path relatif penyimpanan file di disk (misal: 'custom_audios/user_1/audio.mp3')
            // Anda bisa menambahkan kolom lain jika perlu, misalnya 'display_name' jika ingin pengguna memberi nama khusus.
            
            $table->timestamps(); // Otomatis membuat kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_custom_audios');
    }
};