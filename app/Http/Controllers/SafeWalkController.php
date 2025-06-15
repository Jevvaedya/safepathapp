<?php

namespace App\Http\Controllers;

use App\Models\SafeWalkSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\SafeWalkStartedMail; 
use App\Mail\SafeWalkStoppedMail;
use App\Mail\SafeWalkExpiredMail; // Pastikan ini juga sudah kamu buat Mailable-nya
use Illuminate\Support\Facades\Mail;

class SafeWalkController extends Controller
{
    /**
     * Display the Safe Walk starting page.
     */
    public function index()
    {
        // Untuk sekarang, kita hanya tampilkan view-nya saja
        return view('safe_walk.index');
    }

    public function start(Request $request)
    {
        $validatedData = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'duration' => 'required|integer|min:5',
        ]);

        $user = Auth::user();

        $newSession = $user->safeWalkSessions()->create([
            'start_time' => Carbon::now(), 
            'duration_minutes' => $validatedData['duration'],
            'initial_latitude' => $validatedData['latitude'],
            'initial_longitude' => $validatedData['longitude'],
            'status' => 'active', 
        ]);

        Log::info('Safe Walk session created:', $newSession->toArray());

        $message = 'Safe Walk session started and saved! Notification status unknown.'; 

        $primaryContact = $user->emergencyContacts()
                               ->where('is_primary', true)    
                               ->whereNotNull('email')        
                               ->where('email', '!=', '')     
                               ->first();                     

        if ($primaryContact) {
            try {
                Mail::to($primaryContact->email)->send(new SafeWalkStartedMail($user, $primaryContact, $newSession));
                Log::info('Safe Walk started notification email sent to: ' . $primaryContact->email . ' for user ID: ' . $user->id);
                $message = 'Safe Walk session started and saved! Notification email process initiated.';
            } catch (\Exception $e) {
                Log::error('Failed to send Safe Walk started email for user ID: ' . $user->id . ' to ' . $primaryContact->email . '. Error: ' . $e->getMessage());
                $message = 'Safe Walk session started and saved! Failed to send notification email.';
            }
        } else {
            Log::warning('No primary emergency contact with a valid email found for user ID: ' . $user->id . ' to send Safe Walk notification.');
            $message = 'Safe Walk session started and saved! No primary contact email for notification.';
        }

        return response()->json([
            'message' => $message, 
            'session_id' => $newSession->id,
            'data' => $newSession 
        ]);
    }

    public function stop(Request $request)
    {
        $validatedData = $request->validate([
            'session_id' => 'required|integer|exists:safe_walk_sessions,id',
        ]);

        $user = Auth::user();
        $session = SafeWalkSession::find($validatedData['session_id']);

        if (!$session || $session->user_id !== $user->id) {
            return response()->json(['message' => 'Safe Walk session not found or unauthorized.'], 403);
        }

        if ($session->status !== 'active') {
            return response()->json(['message' => 'Safe Walk session is no longer active.'], 400);
        }

        $session->status = 'completed_by_user'; 
        $session->end_time = Carbon::now(); 
        $session->save();

        Log::info('Safe Walk session stopped by user:', $session->toArray());

        $messageForResponse = 'Safe Walk session stopped successfully.'; 

        $primaryContact = $user->emergencyContacts()
                               ->where('is_primary', true)    
                               ->whereNotNull('email')        
                               ->where('email', '!=', '')     
                               ->first();                     

        if ($primaryContact) {
            try {
                Mail::to($primaryContact->email)->send(new SafeWalkStoppedMail($user, $primaryContact, $session));
                Log::info('Safe Walk STOPPED notification email sent to: ' . $primaryContact->email . ' for user ID: ' . $user->id);
                $messageForResponse = 'Safe Walk session stopped successfully! Notification sent.';
            } catch (\Exception $e) {
                Log::error('Failed to send Safe Walk STOPPED email for user ID: ' . $user->id . ' to ' . $primaryContact->email . '. Error: ' . $e->getMessage());
                $messageForResponse = 'Safe Walk session stopped successfully! Failed to send stop notification email.';
            }
        } else {
            Log::warning('No primary emergency contact with a valid email found for user ID: ' . $user->id . ' to send Safe Walk STOPPED notification.');
            $messageForResponse = 'Safe Walk session stopped successfully! No primary contact email for stop notification.';
        }
        return response()->json([
            'message' => $messageForResponse,
            'data' => $session
        ]);
    }

    public function expire(Request $request)
    {
        // 👇 BAGIAN VALIDASI YANG PENTING ADA DI SINI 👇
        $validatedData = $request->validate([
            'session_id' => 'required|integer|exists:safe_walk_sessions,id',
        ]);

        $user = Auth::user();
        // Gunakan $validatedData['session_id'] untuk mengambil ID sesi
        $session = SafeWalkSession::find($validatedData['session_id']);

        // Otorisasi dasar
        if (!$session || $session->user_id !== $user->id) {
            return response()->json(['message' => 'Safe Walk session not found or unauthorized for expiry.'], 403);
        }

        $messageForResponse = 'Safe Walk session was already inactive or status unchanged.'; 

        // Hanya update dan kirim email jika statusnya masih 'active'
        if ($session->status === 'active') {
            $session->status = 'expired_timer'; 
            $session->end_time = Carbon::now(); 
            $session->save();

            Log::info('Safe Walk session marked as EXPIRED by timer:', $session->toArray());

            $primaryContact = $user->emergencyContacts()
                                   ->where('is_primary', true)
                                   ->whereNotNull('email')->where('email', '!=', '')
                                   ->first();
            
            if ($primaryContact) {
                try {
                    Mail::to($primaryContact->email)->send(new SafeWalkExpiredMail($user, $primaryContact, $session));
                    Log::info('Safe Walk EXPIRED notification email sent to: ' . $primaryContact->email . ' for user ID: ' . $user->id);
                    $messageForResponse = 'Safe Walk session marked as expired by timer and notification sent.';
                } catch (\Exception $e) {
                    Log::error('Failed to send Safe Walk EXPIRED email for user ID: ' . $user->id . '. Error: ' . $e->getMessage());
                    $messageForResponse = 'Safe Walk session marked as expired by timer. Failed to send notification email.';
                }
            } else {
                Log::warning('No primary contact for EXPIRED Safe Walk notification for user ID: ' . $user->id);
                $messageForResponse = 'Safe Walk session marked as expired by timer. No primary contact for notification.';
            }

            return response()->json([
                'message' => $messageForResponse,
                'data' => $session
            ]);
        }

        // Jika status sudah bukan 'active', kirim respons bahwa tidak ada perubahan
        return response()->json([
            'message' => $messageForResponse, 
            'data' => $session
        ], 200); 
    }
}