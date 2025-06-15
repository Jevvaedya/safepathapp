<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\EmergencyContact;
use App\Models\SafeWalkSession;

class SafeWalkExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public EmergencyContact $emergencyContact;
    public SafeWalkSession $safeWalkSession;

    public function __construct(User $user, EmergencyContact $emergencyContact, SafeWalkSession $safeWalkSession)
    {
        $this->user = $user;
        $this->emergencyContact = $emergencyContact;
        $this->safeWalkSession = $safeWalkSession;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ALERT: Safe Walk Timer Expired for ' . $this->user->name,
        );
    }

    public function content(): Content
{

    $mapsUrl = "https://www.google.com/maps?q=" . $this->safeWalkSession->initial_latitude . "," . $this->safeWalkSession->initial_longitude;

    return new Content(
        markdown: 'emails.safe_walk_expired', // Menggunakan template Markdown
        with: [
            'userName' => $this->user->name,
            'emergencyContactName' => $this->emergencyContact->name,
            'startTime' => $this->safeWalkSession->start_time->format('F j, Y, g:i A T'),
            'duration' => $this->safeWalkSession->duration_minutes,
            'expiredTime' => $this->safeWalkSession->end_time ? $this->safeWalkSession->end_time->format('F j, Y, g:i A T') : 'N/A',
            'latitude' => $this->safeWalkSession->initial_latitude,
            'longitude' => $this->safeWalkSession->initial_longitude,
            'mapsUrl' => $mapsUrl, // Kirim URL Google Maps ke view
        ],
    );
    }

    public function attachments(): array
    {
        return [];
    }
}