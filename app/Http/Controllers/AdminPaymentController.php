<?php

namespace App\Http\Controllers;

use App\Models\TicketPayment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index()
    {
        // Group pending by sponsor
        $pending = TicketPayment::with(['ticket.raffle', 'sponsor'])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->groupBy('sponsor_id');

        $recent = TicketPayment::with(['ticket.raffle', 'sponsor', 'confirmedBy'])
            ->whereIn('status', ['confirmed', 'rejected'])
            ->latest()
            ->paginate(20);

        return view('admin.payments.index', compact('pending', 'recent'));
    }

    public function show(TicketPayment $payment)
    {
        $payment->load(['ticket.raffle', 'sponsor', 'confirmedBy']);

        // Load ALL pending payments for the same sponsor + raffle
        $allPayments = TicketPayment::with('ticket')
            ->where('sponsor_id', $payment->sponsor_id)
            ->where('status', 'pending')
            ->whereHas('ticket', fn($q) => $q->where(
                'raffle_id', $payment->ticket->raffle_id
            ))
            ->get();

        // Reserved tickets with no payment yet
        $reservedTickets = \App\Models\Ticket::where('sponsor_id', $payment->sponsor_id)
            ->where('raffle_id', $payment->ticket->raffle_id)
            ->where('status', 'reserved')
            ->get();

        return view('admin.payments.show', compact('payment', 'allPayments', 'reservedTickets'));
    }

    public function confirm(TicketPayment $payment)
    {
        $this->paymentService->confirm($payment, auth()->id());

        return redirect()->route('admin.payments.index')
            ->with('success', "Payment confirmed. Ticket {$payment->ticket->ticket_number} is now sold.");
    }

    // Confirm all pending payments for a sponsor in a raffle at once
    public function confirmAll(Request $request)
    {
        $request->validate([
            'sponsor_id' => 'required|exists:sponsors,id',
            'raffle_id'  => 'required|exists:raffles,id',
        ]);

        $payments = TicketPayment::where('sponsor_id', $request->sponsor_id)
            ->where('status', 'pending')
            ->whereHas('ticket', fn($q) => $q->where('raffle_id', $request->raffle_id))
            ->get();

        foreach ($payments as $payment) {
            $this->paymentService->confirm($payment, auth()->id());
        }

        return redirect()->route('admin.payments.index')
            ->with('success', "Confirmed {$payments->count()} payment(s).");
    }

    // Reject all pending payments for a sponsor in a raffle at once
    public function rejectAll(Request $request)
    {
        $request->validate([
            'sponsor_id' => 'required|exists:sponsors,id',
            'raffle_id'  => 'required|exists:raffles,id',
            'notes'      => 'nullable|string|max:500',
        ]);

        $payments = TicketPayment::where('sponsor_id', $request->sponsor_id)
            ->where('status', 'pending')
            ->whereHas('ticket', fn($q) => $q->where('raffle_id', $request->raffle_id))
            ->get();

        foreach ($payments as $payment) {
            $this->paymentService->reject($payment, auth()->id(), $request->notes);
        }

        return redirect()->route('admin.payments.index')
            ->with('success', "Rejected {$payments->count()} payment(s). Tickets released.");
    }

    public function reject(Request $request, TicketPayment $payment)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $this->paymentService->reject($payment, auth()->id(), $request->notes);

        return redirect()->route('admin.payments.index')
            ->with('success', "Payment rejected. Ticket {$payment->ticket->ticket_number} released.");
    }
}