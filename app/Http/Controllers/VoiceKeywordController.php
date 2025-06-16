<?php

// app/Http/Controllers/VoiceKeywordController.php
namespace App\Http\Controllers;

use App\Models\VoiceKeyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VoiceKeywordController extends Controller
{
    // Menampilkan halaman pengaturan kata kunci
    public function index()
    {
        $user = Auth::user();
        // Ambil semua keyword milik user, gabungkan menjadi satu string dipisahkan koma
        $keywords = $user->voiceKeywords()->pluck('keyword')->implode(', ');

        return view('voice-keywords.index', [
            'currentKeywords' => $keywords,
            'pageTitle' => 'Custom Voice Keywords' // Judul Halaman
        ]);
    }

    // Menyimpan kata kunci yang diinput dari form
    public function store(Request $request)
    {
        $request->validate([
            'keywords' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // 1. Hapus semua keyword lama milik user ini
        $user->voiceKeywords()->delete();

        // 2. Jika ada input baru, proses dan simpan
        if ($request->filled('keywords')) {
            // Pecah string berdasarkan koma, bersihkan spasi, dan hapus entri kosong
            $keywords = array_filter(array_map('trim', explode(',', $request->keywords)));

            foreach ($keywords as $word) {
                // Simpan setiap kata kunci ke database
                VoiceKeyword::create([
                    'user_id' => $user->id,
                    'keyword' => Str::lower($word) // Simpan dalam huruf kecil
                ]);
            }
        }

        return back()->with('status', 'Keywords saved successfully!');
    }
}
