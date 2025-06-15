<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // TAMBAHKAN INI
use App\Models\User;

class EmergencyContact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'relationship',
        'is_primary',
        // user_id akan kita set secara otomatis berdasarkan pengguna yang sedang login,
        // jadi tidak perlu dimasukkan ke $fillable jika kita menggunakan metode relasi untuk create.
    ];

    /**
     * Get the user that owns the emergency contact.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
