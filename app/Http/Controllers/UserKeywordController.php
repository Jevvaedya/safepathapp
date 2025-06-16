<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserKeyword; // Pastikan model sudah dibuat

class UserKeywordController extends Controller
{
    /**
     * Menampilkan halaman manajemen keyword beserta daftar keyword milik user.
     */
    public function index()
    {
        $user = Auth::user();
        $keywords = $user->keywords()->orderBy('created_at', 'desc')->get();

        return view('settings.keywords', [
            'pageTitle' => 'Custom Emergency Keywords',
            'keywords' => $keywords
        ]);
    }

    /**
     * Menyimpan keyword baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|min:3|max:50|unique:user_keywords,keyword,NULL,id,user_id,' . Auth::id(),
        ]);

        Auth::user()->keywords()->create([
            'keyword' => strtolower($request->keyword),
        ]);

        return back()->with('success', 'Keyword berhasil ditambahkan!');
    }

    /**
     * Menghapus keyword dari database.
     */
    public function destroy(UserKeyword $keyword)
    {
        // Pastikan user hanya bisa menghapus keyword miliknya sendiri
        if ($keyword->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak menghapus keyword ini.');
        }

        $keyword->delete();

        return back()->with('success', 'Keyword berhasil dihapus.');
    }
}