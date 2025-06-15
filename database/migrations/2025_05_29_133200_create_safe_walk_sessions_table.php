<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('safe_walk_sessions', function (Blueprint $table) {
            $table->id(); // ID unik untuk setiap sesi
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User pemilik sesi ini
            $table->timestamp('start_time'); // Waktu mulai sesi
            $table->integer('duration_minutes'); // Durasi sesi dalam menit
            $table->decimal('initial_latitude', 10, 7); // Latitude awal (presisi 10 digit, 7 di belakang koma)
            $table->decimal('initial_longitude', 10, 7); // Longitude awal (presisi 10 digit, 7 di belakang koma)
            $table->string('status')->default('active'); // Status sesi: 'active', 'completed', 'cancelled', 'expired'
            $table->timestamp('end_time')->nullable(); // Waktu berakhir sesi (bisa null jika masih aktif)
            $table->timestamps(); // created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safe_walk_sessions');
    }
};