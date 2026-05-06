<?php

namespace App\Http\Controllers;

use App\Models\TicketPayment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index()
    {
        $pending = TicketPayment::with(['ticket.raffle', 'sponsor'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        $recent = TicketPayment::with(['ticket.raffle', 'sponsor', 'confirmedBy'])
            ->whereIn('status', ['confirmed', 'rejected'])
            ->latest()
            ->paginate(20);

        return view('admin.payments.index', compact('pending', 'recent'));
    }

    public function show(TicketPayment $payment)
    {
        $payment->load(['ticket.raffle', 'sponsor', 'confirmedBy']);

        return view('admin.payments.show', compact('payment'));
    }

    public function confirm(TicketPayment $payment)
    {
        $this->paymentService->confirm($payment, Auth::guard('web')->id());

        return redirect()->route('admin.payments.index')
            ->with('success', "Payment confirmed. Ticket {$payment->ticket->ticket_number} is now sold.");
    }

    public function reject(Request $request, TicketPayment $payment)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $this->paymentService->reject($payment, Auth::guard('web')->id(), $request->notes);

        return redirect()->route('admin.payments.index')
            ->with('success', "Payment rejected. Ticket {$payment->ticket->ticket_number} is back to available.");
    }
}