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

class ActionItemRevisionRequested extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ActionItem $actionItem;
    public User $assignee;
    public User $organizer;
    public string $revisionNotes;

    public function __construct(ActionItem $actionItem, User $assignee, User $organizer, string $revisionNotes)
    {
        $this->actionItem    = $actionItem;
        $this->assignee      = $assignee;
        $this->organizer     = $organizer;
        $this->revisionNotes = $revisionNotes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->organizer->email, $this->organizer->name),
            replyTo: [
                new Address($this->organizer->email, $this->organizer->name),
            ],
            subject: '[⚠️ Perlu Revisi] ' . $this->actionItem->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.meetings.action_item_revision_requested',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
