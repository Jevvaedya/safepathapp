<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // <-- Sudah benar
use App\Models\VoiceKeyword; // <-- Sudah benar
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\SafeWalkController;
use App\Http\Controllers\FakeCallController;
use App\Http\Controllers\VoiceKeywordController; // <-- Sudah benar

Route::get('/', function () {
    return view('welcome');
});

// ========================================================================
// PERUBAHAN DI SINI: Route Dashboard diperbarui sesuai kebutuhan
// ========================================================================
Route::get('/dashboard', function () {
    $user = Auth::user();
    // 1. Ambil data keyword sekali saja untuk efisiensi
    $userKeywordsQuery = $user->voiceKeywords()->pluck('keyword');

    // 2. Kirim data ke view dashboard dalam DUA format
    return view('dashboard', [
        'pageTitle'       => 'SOS Alerts', // Menambahkan pageTitle agar konsisten
        'userKeywords'    => $userKeywordsQuery->toArray(), // Format ARRAY untuk JavaScript
        'currentKeywords' => $userKeywordsQuery->implode(', '), // Format STRING untuk form input
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');
// ========================================================================
// AKHIR DARI PERUBAHAN
// ========================================================================


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('emergency-contacts', EmergencyContactController::class);

    // ROUTE UNTUK HALAMAN PENGATURAN KATA KUNCI SUARA
    Route::get('/voice-keywords', [VoiceKeywordController::class, 'index'])->name('voice.keywords.index');
    Route::post('/voice-keywords', [VoiceKeywordController::class, 'store'])->name('voice.keywords.store');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // RUTE SAFE WALK
    Route::get('/safe-walk', [SafeWalkController::class, 'index'])->name('safewalk.index');
    Route::post('/safe-walk/start', [SafeWalkController::class, 'start'])->name('safewalk.start');
    Route::post('/safe-walk/stop', [SafeWalkController::class, 'stop'])->name('safewalk.stop');
    Route::post('/safe-walk/expire', [SafeWalkController::class, 'expire'])->name('safewalk.expire');

    // RUTE FAKE CALL
    Route::get('/fake-call', [FakeCallController::class, 'index'])->name('fakecall.index');
    Route::post('/fake-call/generate-audio', [FakeCallController::class, 'generateAudio'])->name('fakecall.generateAudio');
    Route::post('/fake-call/upload-custom-audio', [FakeCallController::class, 'uploadCustomAudio'])->name('fakecall.uploadCustomAudio'); 
    Route::delete('/fake-call/delete-custom-audio', [FakeCallController::class, 'deleteCustomAudio'])->name('fakecall.deleteCustomAudio');
    
});



require __DIR__.'/auth.php';