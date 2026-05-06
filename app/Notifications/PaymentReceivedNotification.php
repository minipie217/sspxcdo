<?php

namespace App\Notifications;

use App\Models\TicketPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public TicketPayment $payment) {}

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
        $ticket  = $this->payment->ticket;
        $sponsor = $this->payment->sponsor;

        return (new MailMessage)
            ->subject('New Payment Pending Confirmation')
            ->greeting("Hello {$notifiable->name},")
            ->line("A payment has been submitted for ticket **{$ticket->ticket_number}**.")
            ->line("Sponsor: {$sponsor->fullName()}")
            ->line("Proof type: {$this->payment->proof_type->value}")
            ->action('Review Payment', url("/admin/payments/{$this->payment->id}"));
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
