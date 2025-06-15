<?php

namespace App\Models; // Pastikan namespace ini sesuai dengan struktur direktori Anda

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Jika Anda menggunakan accessor untuk URL

class UserCustomAudio extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     * Laravel secara default akan menggunakan bentuk plural snake_case dari nama model (user_custom_audios).
     * Jika nama tabel Anda berbeda (misalnya singular seperti 'user_custom_audio'),
     * Anda perlu mendefinisikannya secara eksplisit di sini.
     */
    protected $table = 'user_custom_audios'; // <--- DI SINI TEMPATNYA

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        // tambahkan kolom lain jika ada yang fillable
    ];

    /**
     * Accessor untuk mendapatkan URL publik dari file audio.
     */
    public function getAudioUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Mendefinisikan relasi bahwa audio ini dimiliki oleh seorang User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}