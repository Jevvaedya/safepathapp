<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\SafeWalkController;
use App\Http\Controllers\FakeCallController;
use App\Http\Controllers\DashboardController; // PENTING: Tambahkan ini
use App\Http\Controllers\UserKeywordController; // BARU: Tambahkan ini

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// PENTING: Route dashboard diubah untuk menggunakan Controller
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('emergency-contacts', EmergencyContactController::class);
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
    
    // BARU: RUTE UNTUK MANAJEMEN KEYWORD
    Route::get('/settings/keywords', [UserKeywordController::class, 'index'])->name('keywords.index');
    Route::post('/settings/keywords', [UserKeywordController::class, 'store'])->name('keywords.store');
    Route::delete('/settings/keywords/{keyword}', [UserKeywordController::class, 'destroy'])->name('keywords.destroy');
});



require __DIR__.'/auth.php';