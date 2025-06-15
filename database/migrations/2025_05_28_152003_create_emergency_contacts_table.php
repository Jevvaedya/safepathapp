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
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id(); // Kolom ID otomatis (angka unik)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Kolom untuk ID pengguna pemilik kontak ini
                                                                            // constrained() -> merujuk ke tabel 'users' kolom 'id'
                                                                            // onDelete('cascade') -> jika user dihapus, kontaknya ikut terhapus
            $table->string('name'); // Nama kontak (teks)
            $table->string('phone'); // Nomor telepon kontak (teks)
            $table->string('email')->nullable(); // Email kontak (teks, boleh kosong/nullable)
            $table->string('relationship')->nullable(); // Hubungan dengan kontak (misal: keluarga, teman - teks, boleh kosong)
            $table->boolean('is_primary')->default(false); // Menandakan apakah ini kontak utama (true/false, defaultnya false)
            $table->timestamps(); // Otomatis membuat kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
    }
};