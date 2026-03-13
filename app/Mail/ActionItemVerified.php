<?php

namespace App\Mail;

use App\Models\ActionItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActionItemVerified extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ActionItem $actionItem;
    public User $assignee;
    public User $organizer;

    public function __construct(ActionItem $actionItem, User $assignee, User $organizer)
    {
        $this->actionItem = $actionItem;
        $this->assignee   = $assignee;
        $this->organizer  = $organizer;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->organizer->email, $this->organizer->name),
            replyTo: [
                new Address($this->organizer->email, $this->organizer->name),
            ],
            subject: '[✅ Tugas Diverifikasi] ' . $this->actionItem->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.meetings.action_item_verified',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
