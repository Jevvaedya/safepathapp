<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom voice_sos_keyword setelah kolom lain (misal: password)
            // Tipe data VARCHAR(255) sudah cukup, bisa disesuaikan.
            // `nullable()` agar data lama tidak error.
            // `default(null)` untuk menegaskan nilai defaultnya.
            $table->string('voice_sos_keyword', 255)->nullable()->default(null)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Perintah ini akan dijalankan jika Anda melakukan rollback migration
            $table->dropColumn('voice_sos_keyword');
        });
    }
};