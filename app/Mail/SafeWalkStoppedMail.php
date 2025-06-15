<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // Opsional
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\EmergencyContact;
use App\Models\SafeWalkSession;

class SafeWalkStoppedMail extends Mailable // Opsional: implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public EmergencyContact $emergencyContact;
    public SafeWalkSession $safeWalkSession;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, EmergencyContact $emergencyContact, SafeWalkSession $safeWalkSession)
    {
        $this->user = $user;
        $this->emergencyContact = $emergencyContact;
        $this->safeWalkSession = $safeWalkSession;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Safe Walk Ended: ' . $this->user->name, // Subjek email
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Pastikan end_time tidak null sebelum diformat, meskipun seharusnya sudah diisi oleh controller
        $endTimeFormatted = $this->safeWalkSession->end_time 
                            ? $this->safeWalkSession->end_time->format('F j, Y, g:i A T') 
                            : 'Not specified';

        return new Content(
            markdown: 'emails.safe_walk_stopped', // Menggunakan template Markdown emails.safe_walk_stopped
            with: [
                'userName' => $this->user->name,
                'emergencyContactName' => $this->emergencyContact->name,
                'endTime' => $endTimeFormatted, // Kirim waktu berakhir yang sudah diformat
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}