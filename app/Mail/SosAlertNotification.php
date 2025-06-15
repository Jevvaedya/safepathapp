<?php

namespace App\Mail;

use App\Models\User; // Pengguna yang memicu SOS
use App\Models\EmergencyContact; // Kontak darurat yang akan menerima email
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SosAlertNotification extends Mailable
{
    use Queueable, SerializesModels;

    public User $sosUser;
    public EmergencyContact $contactRecipient;
    public ?array $locationData; // Data lokasi opsional: ['latitude' => ..., 'longitude' => ...]

    /**
     * Create a new message instance.
     */
    public function __construct(User $sosUser, EmergencyContact $contactRecipient, ?array $locationData = null)
    {
        $this->sosUser = $sosUser;
        $this->contactRecipient = $contactRecipient;
        $this->locationData = $locationData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'PEMBERITAHUAN DARURAT SOS - ' . $this->sosUser->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.sos.notification',
            with: [
                'userName' => $this->sosUser->name,
                'contactName' => $this->contactRecipient->name,
                'location' => $this->locationData,
                'time' => now()->format('d F Y, H:i:s T')
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