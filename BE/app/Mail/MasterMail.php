<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MasterMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $tieuDe;
    public string $viewMail;
    public array $noiDung;

    public function __construct(string $tieuDe, string $viewMail, array $noiDung = [])
    {
        $this->tieuDe = $tieuDe;
        $this->viewMail = $viewMail;
        $this->noiDung = $noiDung;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->tieuDe,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->viewMail,
            with: array_merge($this->noiDung, [
                'data' => $this->noiDung,
            ]),
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
