<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SafeWalkSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'duration_minutes',
        'initial_latitude',
        'initial_longitude',
        'status',
        // 'end_time' biasanya diisi nanti saat sesi berakhir
    ];

    // Atur agar start_time dan end_time otomatis di-cast sebagai objek Carbon (tanggal/waktu)
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}