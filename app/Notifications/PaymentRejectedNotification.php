<?php

namespace App\Notifications;

use App\Models\TicketPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public TicketPayment $payment,
        public ?string $notes = null
    ){}

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
        return (new MailMessage)
            ->subject('Payment Rejected')
            ->greeting("Hello {$notifiable->first_name},")
            ->line("Your payment for ticket **{$this->payment->ticket->ticket_number}** has been rejected.")
            ->line('Please submit a new payment or contact support for assistance.')
            ->action('Submit New Payment', url("/raffle/{$this->payment->ticket->raffle_id}/tickets/{$this->payment->ticket->id}/payments"));
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
