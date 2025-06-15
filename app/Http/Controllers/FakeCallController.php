<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\UserCustomAudio; // <-- TAMBAHKAN INI

class FakeCallController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Mengambil semua audio kustom milik pengguna yang login
        $userCustomAudios = UserCustomAudio::where('user_id', $user->id)
                                           ->orderBy('file_name', 'asc')
                                           ->get();

        // $jsTranslations tetap sama seperti yang Anda miliki
        $jsTranslations = [
            'fake_call_scheduled' => __('Fake call scheduled, will start in'),
            'activating_fake_call' => __('Activating Fake Call...'),
            'initiating_fake_call' => __('Fake Call Initiating...'),
            'voice_label' => __('Voice:'),
            'topic_label' => __('Topic:'),
            'delay_label' => __('(This call was scheduled for :delay delay)'),
            'ringtone_playing_placeholder' => __('Ringtone playing... Call screen simulation coming soon!'),
            'call_connected' => __('Call Connected'),
            'simulating_conversation' => __('Simulating conversation...'),
            'speaking_part' => __('Speaking part :part of :total...'),
            'dialog_finished' => __('Conversation finished.'),
            'call_ended_speech_finished' => __('Call ended (speech finished).'),
            'start_fake_call_text' => __('Start Fake Call'),
            'incoming_call_text' => __('Incoming Call...'),
            'could_not_play_ringtone' => __('Could not play ringtone.'),
            'ringtone_playback_error' => __('Ringtone playback error.'),
            'speech_error' => __('Speech error:'),
            'speech_unsupported' => __('Speech synthesis not supported by this browser.'),
            'no_voices_trigger' => __('No voices available, trying to trigger load.'),
            'no_specific_voice' => __('No specific voice found, using browser default for language:'),
            'using_voice' => __('Using voice:'),
            'audio_error_generic' => __('Error with audio:'),
            'unknown_error' => __('Unknown error.'),
            'script_unavailable' => __('Sorry, the script for this topic is not available right now.'),
            // Terjemahan baru yang mungkin berguna untuk UI audio kustom
            'delete_selected_recording' => __('Delete Selected Recording'),
            'select_your_recording' => __('Select Your Recording:'),
            'use_ai_generated_voice' => __('-- Use AI Generated Voice --'),
            'no_custom_recordings_yet' => __('No custom recordings uploaded yet.'),
            'upload_your_own_recording_title' => __('Use Your Own Recording'),
            'or_upload_new_one' => __('Or, upload a new one:'),
            'choose_audio_file_prompt' => __('Choose audio file (MP3, WAV, AAC, OGG - Max 5MB):'),
            'upload_recording_button' => __('Upload Recording'),
            'deleting_status' => __('Deleting...'),
            'delete_confirm_message' => __('Are you sure you want to delete this recording: '),
            'select_record_to_delete_alert' => __('Please select a recording to delete.'),
        ];

        return view('fake_call.index', [ // Ganti 'fake_call.index' dengan path view Anda yang benar
            'jsTranslations' => $jsTranslations,
            'userCustomAudios' => $userCustomAudios, // Kirim collection audio kustom
            'userName' => $user->name, // Kirim nama user jika diperlukan oleh Blade/JS
        ]);
    }

    public function generateAudio(Request $request)
    {
        // ... (Method generateAudio Anda tetap sama)
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
            Log::error('ElevenLabs API Key atau Voice ID (female/male) belum diatur.', [
                'apiKeySet' => !empty($apiKey),
                'femaleVoiceId' => $femaleVoiceIdFromEnv,
                'maleVoiceId' => $maleVoiceIdFromEnv,
                'gender' => $gender,
                'voiceIdUsed' => $voiceIdToUse,
            ]);
            return response()->json(['success' => false, 'message' => 'Konfigurasi suara belum lengkap.'], 500);
        }

        $ttsEndpoint = "https://api.elevenlabs.io/v1/text-to-speech/{$voiceIdToUse}?output_format=mp3_44100_128";

        try {
            $response = Http::withHeaders([
                'xi-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->post($ttsEndpoint, [
                'text' => $textToSpeak,
                'model_id' => 'eleven_multilingual_v2',
            ]);

            if (!$response->successful()) {
                Log::error('Gagal generate audio dari ElevenLabs', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json(['success' => false, 'message' => 'Gagal menghasilkan audio dari ElevenLabs.'], $response->status());
            }

            $audioContent = $response->body();
            $fileName = 'fake_call_audio_' . Str::random(10) . '.mp3';
            $directory = 'temp_audio/fake_calls';

            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            Storage::disk('public')->put("{$directory}/{$fileName}", $audioContent);
            $audioFileUrl = Storage::url("{$directory}/{$fileName}");

            return response()->json([
                'success' => true,
                'audioUrl' => $audioFileUrl,
                'generated_text_for_debug' => $textToSpeak
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Koneksi gagal ke ElevenLabs', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Tidak dapat terhubung ke layanan suara.'], 503);
        } catch (\Exception $e) {
            Log::error('Kesalahan saat generate audio', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghasilkan audio.'], 500);
        }
    }

    public function uploadCustomAudio(Request $request)
    {
        $request->validate([
            'custom_audio' => 'required|file|mimes:mp3,wav,aac,ogg|max:5120', // 5MB
        ]);

        $user = Auth::user();
        $file = $request->file('custom_audio');
        $originalFileName = $file->getClientOriginalName();
        // Buat nama file unik untuk menghindari konflik, tapi simpan nama asli untuk display
        $uniqueFileName = 'user_' . $user->id . '_custom_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
        
        // Simpan ke disk 'public', dalam folder 'custom_fake_audios/{user_id}'
        $path = $file->storeAs('custom_fake_audios/' . $user->id, $uniqueFileName, 'public');

        // Buat record baru di database
        $audioRecord = UserCustomAudio::create([
            'user_id' => $user->id,
            'file_name' => $originalFileName, // Simpan nama asli untuk ditampilkan
            'file_path' => $path, // Path relatif di disk 'public'
        ]);

        return response()->json([
            'success' => true,
            'message' => __('File audio kustom berhasil diunggah.'),
            'id' => $audioRecord->id, // ID dari record baru, PENTING untuk delete
            'audio_url' => $audioRecord->audio_url, // Gunakan accessor dari model
            'file_name' => $audioRecord->file_name
        ]);
    }

    // Terima $audioId sebagai parameter dari route
    public function deleteCustomAudio(Request $request, $audioId)
    {
        $user = Auth::user();
        $audioRecord = UserCustomAudio::find($audioId);

        if (!$audioRecord) {
            return response()->json(['success' => false, 'message' => __('Rekaman tidak ditemukan.')], 404);
        }

        // Verifikasi kepemilikan
        if ($audioRecord->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => __('Anda tidak berhak menghapus rekaman ini.')], 403);
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