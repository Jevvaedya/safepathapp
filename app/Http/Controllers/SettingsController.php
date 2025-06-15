<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // Ditambahkan untuk mengirim email
use Illuminate\Support\Facades\Log;   // Ditambahkan untuk logging
use App\Models\EmergencyContact;      // Pastikan model ini ada dan benar path-nya
use App\Mail\SosAlertNotification;    // Pastikan Mailable ini ada dan benar path-nya
// use Illuminate\Validation\Rule; // Tidak digunakan di method baru, bisa dihapus jika tidak dipakai di tempat lain

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ambil keyword pengguna, jika tidak ada, gunakan default 'tolong'
        $currentKeyword = $user->voice_sos_keyword ?? 'tolong';

        return view('settings.index', [
            'pageTitle' => __('Settings'),
            'currentKeyword' => $currentKeyword
        ]);
    }

    public function updateKeyword(Request $request)
    {
        $user = Auth::user();

        // Validasi input kata kunci
        // Regex: hanya huruf, angka, dan spasi. Minimal 3, maksimal 20 karakter.
        $validated = $request->validate([
            'keyword' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[a-zA-Z0-9\s]+$/i' // i untuk case-insensitive saat validasi inputnya
            ],
        ]);

        // Simpan kata kunci dalam huruf kecil dan hilangkan spasi di awal/akhir
        $newKeyword = strtolower(trim($validated['keyword']));

        $user->voice_sos_keyword = $newKeyword;
        $user->save();

        // Mengembalikan respons JSON untuk AJAX request
        return response()->json([
            'success' => true,
            'message' => __('Kata kunci berhasil diperbarui!'),
            'newKeyword' => $newKeyword
        ]);
    }

    /**
     * Send SOS email notification to the user's primary emergency contact.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSosEmailNotification(Request $request)
    {
        $user = Auth::user();

        // Cari kontak utama yang memiliki email
        // Pastikan model EmergencyContact Anda memiliki kolom user_id, is_primary, dan email
        $primaryContact = EmergencyContact::where('user_id', $user->id)
                                          ->where('is_primary', true)
                                          ->whereNotNull('email')
                                          ->where('email', '<>', '') // Pastikan email tidak string kosong
                                          ->first();

        if (!$primaryContact) {
            Log::warning("User {$user->id} triggered SOS but no valid primary contact with email found.");
            return response()->json([
                'success' => false,
                'message' => __('Kontak utama dengan alamat email yang valid tidak ditemukan atau belum diatur.')
            ], 404); // Not Found
        }

        // Ambil data lokasi dari request jika ada
        // JavaScript akan mengirimkan ini sebagai objek: { latitude: ..., longitude: ... }
        $locationData = $request->input('location', null);
        if ($locationData && (!isset($locationData['latitude']) || !isset($locationData['longitude']))) {
            $locationData = null; // Abaikan jika formatnya tidak sesuai
        }


        try {
            // Kirim email menggunakan Mailable SosAlertNotification
            Mail::to($primaryContact->email)->send(new SosAlertNotification($user, $primaryContact, $locationData));

            Log::info("SOS Email notification successfully sent for user {$user->id} to primary contact {$primaryContact->email}.");
            return response()->json([
                'success' => true,
                'message' => __('Notifikasi SOS telah dikirim melalui email ke kontak utama Anda: ') . $primaryContact->name
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send SOS email for user {$user->id} to {$primaryContact->email}: " . $e->getMessage(), [
                'exception' => $e // Log detail exception untuk debugging
            ]);
            return response()->json([
                'success' => false,
                'message' => __('Gagal mengirim email notifikasi SOS. Silakan coba lagi atau hubungi kontak secara manual.')
            ], 500); // Internal Server Error
        }
    }
}
