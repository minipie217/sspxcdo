<?php

namespace App\Http\Controllers;

use App\Enums\RaffleStatus;
use App\Enums\TicketStatus;
use App\Models\Raffle;
use App\Models\Sponsor;
use App\Models\Ticket;
use App\Models\TicketPayment;

class DashboardController extends Controller
{
    public function index()
    {
        // Total funds raised — confirmed payments only
        $totalFunds = TicketPayment::where('ticket_payments.status', 'confirmed')
            ->join('tickets', 'ticket_payments.ticket_id', '=', 'tickets.id')
            ->join('raffles', 'tickets.raffle_id', '=', 'raffles.id')
            ->sum('raffles.ticket_price');

        // Active raffles
        $activeRaffles = Raffle::where('status', RaffleStatus::Active)->count();

        // Tickets sold
        $ticketsSold = Ticket::where('status', TicketStatus::Sold)->count();

        // Registered sponsors
        $totalSponsors = Sponsor::count();

        return view('dashboard', compact(
            'totalFunds',
            'activeRaffles',
            'ticketsSold',
            'totalSponsors',
        ));
    }
}