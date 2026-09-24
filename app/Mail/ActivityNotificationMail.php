<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivityNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public UserNotification $notification;
    public ?User $user;
    public ?string $certificateUrl;
    public bool $isCertificate;

    /**
     * Create a new message instance.
     */
    public function __construct(UserNotification $notification, ?User $user = null)
    {
        $this->notification = $notification;
        $this->user = $user ?? $notification->user;

        // Check if this notification is for a course certificate
        $isCourseType = ($this->notification->type === 'kursus');
        $hasCertKeywords = str_contains(strtolower($this->notification->title), 'sijil')
            || str_contains(strtolower($this->notification->message), 'sijil');
        $hasCertUrl = $this->notification->action_url && str_contains($this->notification->action_url, 'sijil');

        $this->isCertificate = ($isCourseType && ($hasCertKeywords || $hasCertUrl)) || $hasCertUrl;
        $this->certificateUrl = $this->isCertificate ? ($this->notification->action_url ?: url('/kursus')) : null;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $moduleName = match ($this->notification->type) {
            'kursus' => 'Kursus Latihan',
            'klinik' => 'Klinik Haiwan',
            'eptr' => 'Ternakan Ruminan (e-PTR)',
            'pawah' => 'Program Pawah',
            'naimbif' => 'Program NAIMbif',
            'epu' => 'Unggas (e-PU)',
            'inventori' => 'Inventori & Stor',
            'kenderaan' => 'Tempahan Kenderaan',
            'media' => 'Tempahan Media',
            default => 'e-JPVNK',
        };

        return new Envelope(
            from: new Address(
                config('mail.from.address', 'notifikasi@veterinar.kelantan.gov.my'),
                config('mail.from.name', 'Jabatan Perkhidmatan Veterinar Kelantan')
            ),
            subject: "[{$moduleName}] {$this->notification->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.activity_notification',
            with: [
                'notification' => $this->notification,
                'user' => $this->user,
                'isCertificate' => $this->isCertificate,
                'certificateUrl' => $this->certificateUrl,
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
