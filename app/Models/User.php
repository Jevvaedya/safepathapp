<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\EmergencyContact;
use App\Models\SafeWalkSession;
use App\Models\UserCustomAudio;
use App\Models\VoiceKeyword; // <--- TAMBAHKAN INI

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'voice_sos_keyword', // Catatan: Kolom ini mungkin tidak diperlukan lagi jika kita pakai tabel relasi
        'custom_fake_call_audio_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the emergency contacts for the user.
     */
    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function safeWalkSessions(): HasMany
    {
        return $this->hasMany(SafeWalkSession::class);
    }
    
    public function customAudios(): HasMany
    {
        return $this->hasMany(UserCustomAudio::class);
    }

    /**
     * Get the custom voice keywords for the user.  <--- TAMBAHKAN METHOD INI
     */
    public function voiceKeywords(): HasMany
    {
        return $this->hasMany(VoiceKeyword::class);
    }
}