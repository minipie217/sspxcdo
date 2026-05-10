<?php

namespace App\Notifications;

use App\Models\TicketPayment;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public TicketPayment $payment)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->payment->ticket;
        
        $template = app(EmailTemplateService::class)->render('payment_confirmed', [
            'name'          => $notifiable->first_name,
            'ticket_number' => $ticket->ticket_number,
            'raffle_title'  => $ticket->raffle->title,
            'draw_date'     => $ticket->raffle->draw_date->format('M d, Y'),
        ]);

        return (new MailMessage)
            ->subject($template['subject'])
            ->view('emails.template', ['body' => $template['body']]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
