<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskDueReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Task $task) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lembrete: "'.$this->task->title.'" vence em breve',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.task-due-reminder',
        );
    }
}
