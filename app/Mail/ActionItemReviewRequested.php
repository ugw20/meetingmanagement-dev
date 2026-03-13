<?php

namespace App\Mail;

use App\Models\ActionItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ActionItemReviewRequested extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ActionItem $actionItem;
    public User $organizer;
    public User $reporter;
    public Collection $files;

    public function __construct(ActionItem $actionItem, User $organizer, User $reporter, Collection $files)
    {
        $this->actionItem = $actionItem;
        $this->organizer  = $organizer;
        $this->reporter   = $reporter;
        $this->files      = $files;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->reporter->email, $this->reporter->name),
            replyTo: [
                new Address($this->reporter->email, $this->reporter->name),
            ],
            subject: '[Review Diperlukan] ' . $this->actionItem->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.meetings.action_item_review_requested',
        );
    }

    /**
     * Lampirkan semua file bukti pelapor langsung ke email.
     */
    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->files as $file) {
            $fullPath = Storage::disk('public')->path($file->file_path);

            if (file_exists($fullPath)) {
                $attachments[] = Attachment::fromPath($fullPath)
                    ->as($file->file_name)
                    ->withMime($file->file_type ?? 'application/octet-stream');
            }
        }

        return $attachments;
    }
}

