<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmergencyContact;
use App\Mail\SosAlertNotification;

class SettingsController extends Controller
{
    /**
     * Method index() dihapus karena halaman /settings sudah tidak ada lagi.
     * Tugasnya untuk menampilkan data awal sekarang ditangani oleh DashboardController.
     */

    /**
     * Memperbarui kata kunci SOS milik pengguna.
     */
    public function updateKeyword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'keyword' => [
                'required', 'string', 'min:3', 'max:20', 'regex:/^[a-zA-Z0-9\s]+$/i'
            ],
        ]);

        $newKeyword = strtolower(trim($validated['keyword']));

        $user->voice_sos_keyword = $newKeyword;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => __('Kata kunci berhasil diperbarui!'),
            'newKeyword' => $newKeyword
        ]);
    }

    /**
     * Mengirim notifikasi email SOS ke kontak darurat utama.
     */
    public function sendSosEmailNotification(Request $request)
    {
        $user = Auth::user();

        // Cari kontak utama yang memiliki email valid
        $primaryContact = EmergencyContact::where('user_id', $user->id)
                                          ->where('is_primary', true)
                                          ->whereNotNull('email')
                                          ->where('email', '<>', '')
                                          ->first();

        if (!$primaryContact) {
            Log::warning("User {$user->id} triggered SOS but no valid primary contact with email found.");
            return response()->json([
                'success' => false,
                'message' => __('Kontak utama dengan alamat email yang valid tidak ditemukan atau belum diatur.')
            ], 404);
        }

        // Ambil data lokasi dari request
        $locationData = $request->input('location', null);
        if ($locationData && (!isset($locationData['latitude']) || !isset($locationData['longitude']))) {
            $locationData = null; // Abaikan jika formatnya tidak sesuai
        }

        try {
            // Kirim email menggunakan Mailable
            Mail::to($primaryContact->email)->send(new SosAlertNotification($user, $primaryContact, $locationData));

            Log::info("SOS Email notification successfully sent for user {$user->id} to {$primaryContact->email}.");
            return response()->json([
                'success' => true,
                'message' => __('Notifikasi SOS telah dikirim melalui email ke kontak utama Anda: ') . $primaryContact->name
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send SOS email for user {$user->id} to {$primaryContact->email}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Gagal mengirim email notifikasi SOS. Silakan coba lagi atau hubungi kontak secara manual.')
            ], 500);
        }
    }
}