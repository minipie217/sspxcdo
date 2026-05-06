<?php

namespace App\Notifications;

use App\Models\TicketPayment;
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
        
        return (new MailMessage)
            ->subject('Payment Confirmed — Ticket Sold!')
            ->greeting("Hello {$notifiable->first_name},")
            ->line("Your payment for ticket **{$ticket->ticket_number}** has been confirmed.")
            ->line("Raffle: {$ticket->raffle->title}")
            ->line("Draw Date: {$ticket->raffle->draw_date->format('M d, Y')}")
            ->line('Good luck!')
            ->action('View Raffle', url("/raffle/{$ticket->raffle_id}"));
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
