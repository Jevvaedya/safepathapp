<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\SafeWalkController;
use App\Http\Controllers\FakeCallController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('emergency-contacts', EmergencyContactController::class);
});

Route.middleware(['auth', 'verified'])->group(function () {

    // RUTE SAFE WALK
    Route::get('/safe-walk', [SafeWalkController::class, 'index'])->name('safewalk.index');
    Route::post('/safe-walk/start', [SafeWalkController::class, 'start'])->name('safewalk.start');
    Route::post('/safe-walk/stop', [SafeWalkController::class, 'stop'])->name('safewalk.stop');
    Route::post('/safe-walk/expire', [SafeWalkController::class, 'expire'])->name('safewalk.expire');

    // RUTE FAKE CALL
    Route::get('/fake-call', [FakeCallController::class, 'index'])->name('fakecall.index');
    Route::post('/fake-call/generate-audio', [FakeCallController::class, 'generateAudio'])->name('fakecall.generateAudio');
    Route::post('/fake-call/upload-custom-audio', [FakeCallController::class, 'uploadCustomAudio'])->name('fakecall.uploadCustomAudio');
    
    // --- PERBAIKAN DI SINI ---
    // Menambahkan parameter {id} untuk menangkap ID audio yang akan dihapus.
    Route::delete('/fake-call/custom-audio/{id}', [FakeCallController::class, 'deleteCustomAudio'])->name('fakecall.deleteCustomAudio');
    
    // RUTE SETTINGS
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/keyword', [SettingsController::class, 'updateKeyword'])->name('settings.updateKeyword');
    Route::post('/sos/notify-contact', [SettingsController::class, 'sendSosEmailNotification'])->name('sos.notifyContact');
});

require __DIR__.'/auth.php';