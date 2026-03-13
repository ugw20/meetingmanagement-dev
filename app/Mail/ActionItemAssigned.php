<?php

namespace App\Mail;

use App\Models\ActionItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActionItemAssigned extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $actionItem;
    public $participant;
    public $meeting;
    public $senderName;
    public $senderEmail;

    public function __construct(ActionItem $actionItem, User $participant, $senderName = null, $senderEmail = null)
    {
        $this->actionItem = $actionItem;
        $this->participant = $participant;
        $this->meeting = $actionItem->meeting;
        $this->senderName = $senderName ?? config('mail.from.name');
        $this->senderEmail = $senderEmail ?? config('mail.from.address');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($this->senderEmail, $this->senderName),
            subject: 'Tugas Baru: ' . $this->actionItem->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.meetings.action_item_assigned',
        );
    }
}
