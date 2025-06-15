<?php

namespace App\Http\Controllers;

use App\Models\SafeWalkSession;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

        public function stop(Request $request)
    {
        $validatedData = $request->validate([
            'session_id' => 'required|integer|exists:safe_walk_sessions,id',
        ]);

        $user = Auth::user();
        $session = SafeWalkSession::find($validatedData['session_id']);

        // Otorisasi: Cek apakah sesi ada, milik user ini, dan masih aktif
        if (!$session || $session->user_id !== $user->id) {
            return response()->json(['message' => 'Safe Walk session not found or unauthorized.'], 403); // 403 Forbidden
        }

        if ($session->status !== 'active') {
            // Mungkin sudah dihentikan atau selesai, kirim respons yang sesuai
            return response()->json(['message' => 'Safe Walk session is no longer active.'], 400); // 400 Bad Request
        }

        // Update status sesi
        $session->status = 'completed_by_user'; // Atau bisa juga 'cancelled_by_user'
        $session->end_time = Carbon::now(); // Catat waktu berakhir
        $session->save();

        Log::info('Safe Walk session stopped by user:', $session->toArray());

        return response()->json([
            'message' => 'Safe Walk session stopped successfully.',
            'data' => $session
        ]);
    }
}
