<?php

namespace App\Http\Controllers;

use App\Models\EmergencyContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user(); // Dapatkan user yang sedang login

        // Ambil semua kontak darurat milik user tersebut, urutkan berdasarkan nama
        $contacts = $user->emergencyContacts()->orderBy('name', 'asc')->get();
        // 'emergencyContacts()' adalah nama method relasi yang kita buat di model User
        // orderBy('name', 'asc') -> untuk mengurutkan berdasarkan kolom 'name' secara ascending (A-Z)
        // get() -> untuk mengambil semua data yang cocok

        // Kirim data $contacts ke view
        return view('emergency_contacts.index', compact('contacts'));
        // compact('contacts') adalah cara singkat untuk membuat array ['contacts' => $contacts]

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('emergency_contacts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'required|email|max:255',
        'relationship' => 'nullable|string|max:100',
        'is_primary' => 'nullable|boolean',
    ]);

    $user = Auth::user();

    $contactData = $validatedData;
    $isNewContactPrimary = $request->has('is_primary'); // Cek apakah kontak baru ini mau jadi primary

    // Jika kontak baru ini akan dijadikan primary,
    // maka set semua kontak lain milik user ini yang mungkin sudah primary menjadi false (tidak primary)
    if ($isNewContactPrimary) {
        $user->emergencyContacts()->update(['is_primary' => false]);
    }

    // Baru set status primary untuk kontak baru ini
    $contactData['is_primary'] = $isNewContactPrimary; 

    // Simpan kontak baru
    $user->emergencyContacts()->create($contactData);

    return redirect()->route('emergency-contacts.index')
                     ->with('success', 'Emergency contact added successfully!');

    }

    /**
     * Display the specified resource.
     */
    public function show(EmergencyContact $emergencyContact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmergencyContact $emergencyContact)
    {
        if ($emergencyContact->user_id !== Auth::id()) {
            // Jika bukan miliknya, tampilkan error 403 (Forbidden/Dilarang)
            abort(403, 'ANDA TIDAK BERHAK MENGAKSES HALAMAN INI.'); 
        }

        // Jika milik pengguna, tampilkan view form edit dengan data kontak tersebut
        return view('emergency_contacts.edit', compact('emergencyContact'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmergencyContact $emergencyContact)
    {
                // Langkah 1: Otorisasi (Pastikan user hanya bisa update kontaknya sendiri)
        if ($emergencyContact->user_id !== Auth::id()) {
            abort(403, 'ANDA TIDAK BERHAK MELAKUKAN TINDAKAN INI.');
        }

        // Langkah 2: Validasi Input Data dari Form
        // Aturan validasi mirip dengan store(), tapi untuk email unik perlu penyesuaian jika diterapkan
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255', // Untuk sementara, validasi email sederhana dulu
                                                // Validasi email unik yang mengabaikan email saat ini:
                                                // Rule::unique('emergency_contacts')->ignore($emergencyContact->id)->where(...)
            'relationship' => 'nullable|string|max:100',
            'is_primary' => 'nullable|boolean',
        ]);

        // Langkah 3: Siapkan data untuk diupdate, termasuk menangani checkbox 'is_primary'
        $updateData = $validatedData;
        $updateData['is_primary'] = $request->has('is_primary');

        // (Opsional Lanjutan untuk is_primary: Jika kontak ini dijadikan primary,
        //  maka kontak primary user yang lain (jika ada) harus di-set jadi false.
        //  Ini bisa kita tambahkan nanti agar hanya ada satu kontak primary.)
        if ($updateData['is_primary']) {
            // Set semua kontak lain milik user ini menjadi not primary
            Auth::user()->emergencyContacts()->where('id', '!=', $emergencyContact->id)->update(['is_primary' => false]);
        }


        // Langkah 4: Update data kontak
        $emergencyContact->update($updateData);

        // Langkah 5: Redirect ke halaman daftar kontak dengan pesan sukses
        return redirect()->route('emergency-contacts.index')
                         ->with('success', 'Emergency contact updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmergencyContact $emergencyContact)
    {
                // Langkah 1: Otorisasi (Pastikan user hanya bisa delete kontaknya sendiri)
        if ($emergencyContact->user_id !== Auth::id()) {
            abort(403, 'ANDA TIDAK BERHAK MELAKUKAN TINDAKAN INI.');
        }

        // Langkah 2: Hapus data kontak dari database
        $emergencyContact->delete();

        // Langkah 3: Redirect ke halaman daftar kontak dengan pesan sukses
        return redirect()->route('emergency-contacts.index')
                         ->with('success', 'Emergency contact deleted successfully!');
    }
}
