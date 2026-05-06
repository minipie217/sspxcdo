<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminOtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $otp)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Admin Verification Code')
            ->greeting("Hello {$notifiable->name},")
            ->line('Use the code below to log in to the admin panel.')
            ->line("**{$this->otp}**")
            ->line('This code expires in 15 minutes.')
            ->line('If you did not request this, please ignore this email.');
    }
}