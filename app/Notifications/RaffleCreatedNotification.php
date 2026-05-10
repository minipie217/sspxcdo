<?php

namespace App\Notifications;

use App\Models\Raffle;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RaffleCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Raffle $raffle) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $template = app(EmailTemplateService::class)->render('raffle_created', [
            'name'               => $notifiable->first_name,
            'raffle_title'       => $this->raffle->title,
            'raffle_description' => $this->raffle->description ?? '',
            'ticket_price'       => number_format($this->raffle->ticket_price, 2),
            'draw_date'          => $this->raffle->draw_date->format('M d, Y'),
            'raffle_url'         => route('raffle.show', $this->raffle),
        ]);

        return (new MailMessage)
            ->subject($template['subject'])
            ->view('emails.template', ['body' => $template['body']]);
    }
}