<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // Opsional, jika ingin email dikirim via antrian nanti
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User; // Untuk data pengguna yang memulai Safe Walk
use App\Models\EmergencyContact; // Untuk data kontak darurat yang menerima email
use App\Models\SafeWalkSession; // Untuk data sesi Safe Walk

class SafeWalkStartedMail extends Mailable // Bisa juga implements ShouldQueue jika mau pakai antrian
{
    use Queueable, SerializesModels;

    public User $user; // Properti untuk menyimpan data User
    public EmergencyContact $emergencyContact; // Properti untuk menyimpan data EmergencyContact
    public SafeWalkSession $safeWalkSession; // Properti untuk menyimpan data SafeWalkSession

    /**
     * Create a new message instance.
     * Constructor ini akan menerima data saat kita membuat objek email baru.
     */
    public function __construct(User $user, EmergencyContact $emergencyContact, SafeWalkSession $safeWalkSession)
    {
        $this->user = $user;
        $this->emergencyContact = $emergencyContact;
        $this->safeWalkSession = $safeWalkSession;
    }

    /**
     * Get the message envelope.
     * Mendefinisikan subjek, pengirim (opsional jika sudah di .env), dll.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Safe Walk Started: ' . $this->user->name, // Contoh subjek email dinamis
            // Jika mau override pengirim dari .env, bisa tambahkan:
            // from: new \Illuminate\Mail\Mailables\Address('noreply@safepath.com', 'SafePath Notification'),
        );
    }

    /**
     * Get the message content definition.
     * Mendefinisikan view mana yang digunakan dan data apa yang dikirim ke view.
     */
    public function content(): Content
    {
        // Buat URL Google Maps dari latitude dan longitude
        $mapsUrl = "https://www.google.com/maps?q=" . $this->safeWalkSession->initial_latitude . "," . $this->safeWalkSession->initial_longitude;

        return new Content(
            markdown: 'emails.safe_walk_started', // Menggunakan template Markdown yang sudah kita buat
            with: [ // Data yang akan dikirim ke view emails.safe_walk_started.blade.php
                'userName' => $this->user->name,
                'emergencyContactName' => $this->emergencyContact->name,
                'startTime' => $this->safeWalkSession->start_time->format('F j, Y, g:i A T'), // Format tanggal agar mudah dibaca
                'duration' => $this->safeWalkSession->duration_minutes,
                'latitude' => $this->safeWalkSession->initial_latitude,
                'longitude' => $this->safeWalkSession->initial_longitude,
                'mapsUrl' => $mapsUrl, // Kirim URL Google Maps ke view
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
        return []; // Kita tidak punya lampiran untuk email ini
    }
}