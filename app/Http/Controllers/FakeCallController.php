<?php

namespace App\Http\Controllers;

use App\Models\UserCustomAudio; // Pastikan ini adalah nama Model Anda yang benar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FakeCallController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // PENYEMPURNAAN: Mengambil audio melalui relasi di model User
        // Ini lebih bersih dan terjamin hanya mengambil milik user yang login.
        $userCustomAudios = $user->customAudios()
                                 ->orderBy('file_name', 'asc')
                                 ->get();

        // Anda tidak perlu mengirim array terjemahan yang besar dari controller.
        // Fitur __() Laravel sudah bisa diakses langsung di file JavaScript Blade.
        // Namun, jika Anda membutuhkannya, biarkan saja. Untuk kerapian, saya akan hapus sementara.

        return view('fake_call.index', [
            'userCustomAudios' => $userCustomAudios,
            'userName' => $user->name,
        ]);
    }

    public function generateAudio(Request $request)
    {
        // Method generateAudio Anda sudah terlihat bagus dan tidak perlu diubah.
        // ... (seluruh isi method generateAudio Anda diletakkan di sini)
        $validatedData = $request->validate([
            'topic_value' => 'required|string',
            'gender' => 'required|string',
        ]);

        $topic = $validatedData['topic_value'];
        $gender = $validatedData['gender'];
        $textToSpeak = "Maaf, skrip untuk topik ini belum disiapkan.";

        if ($topic === 'boss') {
            $textToSpeak = ($gender === 'female') 
                ? "Halo, ini dari kantor. ......... Kamu lagi di mana ya? .................. Tolong segera ke kantor ya. ............ Ada update penting soal proyek baru. ............... Oke deh, saya tunggu ya. Ini urgen! ............ " 
                : "Halo, ini dari kantor. ......... Posisi di mana? .................. Tolong segera ke kantor ya. ............ Ada update penting soal proyek baru. ............... Sip, ditunggu ya. Makasih. ............ ";
        } elseif ($topic === 'delivery') {
            $textToSpeak = ($gender === 'female')
                ? "Permisi, ... saya kurir JNE, .................. ada paket nih. ............ Alamatnya sudah sesuai kan ya? ........................ Baik, paketnya akan segera datang. ............... Terima kasih. ......"
                : "Permisi, ... saya kurir JNE, .................. ada paket nih. ............ Alamatnya sudah sesuai kan ya? ........................ Oke, paketnya akan segera datang ya. ............... Makasih! ......";
        } elseif ($topic === 'friend_urgent') {
            $textToSpeak = ($gender === 'female')
                ? "Eh, lo dimana sih? ............ Gawat banget. ............ Gue butuh banget bantuan lo sekarang, please! ............... Bisa ketemu sekarang nggak? ............ Penting banget, sumpah! ............ Oke, shareloc aja ya. Cepetan! ........."
                : "Woy! Lo dimana? ............ Gue butuh bantuan lo banget. ............... Bisa ketemu gue sekarang? ............ Mendesak banget ini! ............... Sip, shareloc aja ya. Buru! .........";
        } elseif ($topic === 'family_checkin') {
            $textToSpeak = ($gender === 'female')
                ? "Halo sayang. .............. Kamu lagi di mana, Nak? Sama siapa? ........................ Baik-baik aja kan? Mama cuma mau mastiin. .................. Jangan lupa shareloc ke Mama ya. ............... Ya udah, hati-hati. ........."
                : "Halo. ............... Lagi di mana sekarang? Lagi ngapain? ........................ Oh gitu, syukurlah kalau aman. Ayah cuma mau cek aja. .................. Jaga diri baik-baik ya. ............... Jangan lupa shareloc ke Ayah ya. ......... Oke deh, Ayah tutup dulu. ...";
        }

        $apiKey = env('ELEVENLABS_API_KEY');
        $femaleVoiceIdFromEnv = env('ELEVENLABS_VOICE_ID_FEMALE_ID');
        $maleVoiceIdFromEnv = env('ELEVENLABS_VOICE_ID_MALE_ID');
        $voiceIdToUse = ($gender === 'female') ? $femaleVoiceIdFromEnv : $maleVoiceIdFromEnv;

        if (empty($apiKey) || empty($voiceIdToUse)) {
            Log::error('ElevenLabs API Key atau Voice ID belum diatur.');
            return response()->json(['success' => false, 'message' => 'Konfigurasi suara belum lengkap.'], 500);
        }

        try {
            $response = Http::withHeaders(['xi-api-key' => $apiKey])->timeout(30)->post("https://api.elevenlabs.io/v1/text-to-speech/{$voiceIdToUse}?output_format=mp3_44100_128", [
                'text' => $textToSpeak, 'model_id' => 'eleven_multilingual_v2',
            ]);
            if (!$response->successful()) {
                Log::error('Gagal generate audio dari ElevenLabs', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json(['success' => false, 'message' => 'Gagal menghasilkan audio dari server.'], $response->status());
            }
            $fileName = 'fake_call_audio_' . Str::random(10) . '.mp3';
            Storage::disk('public')->put("temp_audio/{$fileName}", $response->body());
            return response()->json(['success' => true, 'audioUrl' => Storage::url("temp_audio/{$fileName}")]);
        } catch (\Exception $e) {
            Log::error('Kesalahan saat generate audio', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan internal saat menghasilkan audio.'], 500);
        }
    }

    public function uploadCustomAudio(Request $request)
    {
        // --- PERBAIKAN 1 ---
        // Mengubah 'custom_audio' menjadi 'audio_file' agar cocok dengan nama input di form HTML
        $request->validate([
            'audio_file' => 'required|file|mimes:mp3,wav,aac,ogg|max:5120', // 5MB
        ]);

        $user = auth()->user();
        // --- PERBAIKAN 2 ---
        // Mengambil file dengan nama 'audio_file'
        $file = $request->file('audio_file');
        
        $originalFileName = $file->getClientOriginalName();
        $uniqueFileName = 'user_' . $user->id . '_custom_' . time() . '.' . $file->getClientOriginalExtension();
        
        $path = $file->storeAs('custom_audios/' . $user->id, $uniqueFileName, 'public');

        // PENYEMPURNAAN: Menggunakan relasi untuk membuat record, ini otomatis mengisi user_id
        $audioRecord = $user->customAudios()->create([
            'file_name' => $originalFileName,
            'file_path' => $path,
        ]);

        return response()->json([
            'success'   => true,
            'message'   => __('File audio kustom berhasil diunggah.'),
            'id'        => $audioRecord->id,
            'audio_url' => $audioRecord->audio_url, // Menggunakan accessor dari model
            'file_name' => $audioRecord->file_name
        ]);
    }

    public function deleteCustomAudio($audioId)
    {
        $user = auth()->user();
        // Mengambil record audio milik user yang login saja untuk keamanan
        $audioRecord = $user->customAudios()->find($audioId);

        if (!$audioRecord) {
            return response()->json(['success' => false, 'message' => __('Rekaman tidak ditemukan.')], 404);
        }

        // Hapus file dari storage
        if (Storage::disk('public')->exists($audioRecord->file_path)) {
            Storage::disk('public')->delete($audioRecord->file_path);
        }

        // Hapus record dari database
        $audioRecord->delete();

        return response()->json(['success' => true, 'message' => __('File audio kustom berhasil dihapus.')]);
    }
}