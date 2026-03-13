<?php

namespace App\Mail;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MinuteTakerAssigned extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $meeting;
    public $participant;
    public $senderName;
    public $senderEmail;

    public function __construct(Meeting $meeting, User $participant, $senderName = null, $senderEmail = null)
    {
        $this->meeting = $meeting;
        $this->participant = $participant;
        $this->senderName = $senderName ?? config('mail.from.name');
        $this->senderEmail = $senderEmail ?? config('mail.from.address');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($this->senderEmail, $this->senderName),
            subject: 'Penugasan Notulensi: ' . $this->meeting->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.meetings.minute_taker_assigned',
        );
    }
}
