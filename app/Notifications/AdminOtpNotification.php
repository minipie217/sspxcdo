<?php

namespace App\Notifications;

use App\Services\EmailTemplateService;
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
        $template = app(EmailTemplateService::class)->render('admin_otp', [
            'name' => $notifiable->name,
            'otp'  => $this->otp,
        ]);

        return (new MailMessage)
            ->subject($template['subject'])
            ->view('emails.template', ['body' => $template['body']]);
    }
}