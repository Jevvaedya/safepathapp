<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\SafeWalkController;
use App\Http\Controllers\FakeCallController;
use App\Http\Controllers\SettingsController; // Pastikan ini sudah ada atau ditambahkan
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/emergency-contacts', function () {
    
//     return view('dashboard', ['pageTitle' => 'Emergency Contacts']);
// })->middleware(['auth', 'verified'])->name('emergency.contacts.index');

// Route::get('/safe-walk', function () {
//     return view('dashboard', ['pageTitle' => 'Safe Walk']);
// })->middleware(['auth', 'verified'])->name('safewalk.index');

// Route::get('/fake-call', function () {
//     return view('dashboard', ['pageTitle' => 'Fake Call']);
// })->middleware(['auth', 'verified'])->name('fakecall.index');

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
    // RUTE BARU UNTUK SETTINGS
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/keyword', [SettingsController::class, 'updateKeyword'])->name('settings.updateKeyword');
     Route::post('/sos/notify-contact', [SettingsController::class, 'sendSosEmailNotification'])->name('sos.notifyContact');
});



require __DIR__.'/auth.php';