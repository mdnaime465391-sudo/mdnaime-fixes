<?php

namespace App\Notifications;

use App\Models\Inquiry;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewInquiry extends Notification
{
    use Queueable;

    public function __construct(public Inquiry $inquiry) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New project inquiry — '.$this->inquiry->name)
            ->replyTo($this->inquiry->email, $this->inquiry->name)
            ->view('emails.new-inquiry', [
                'inquiry' => $this->inquiry,
                'settings' => Setting::current(),
            ]);
    }
}
