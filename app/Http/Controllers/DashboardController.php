<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ambil keyword kustom milik pengguna
        $userKeywords = $user->keywords()->pluck('keyword')->toArray();

        // Kirim data ke view 'dashboard'
        return view('dashboard', [
            // 'pageTitle' => 'SOS Alerts', // ini opsional jika Anda butuh judul
            'userKeywords' => $userKeywords
        ]);
    }
}