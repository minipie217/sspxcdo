<?php

namespace App\Http\Controllers;

use App\Enums\RaffleStatus;
use App\Enums\TicketStatus;
use App\Models\Raffle;
use App\Models\Sponsor;
use App\Models\Ticket;
use App\Models\TicketPayment;
use App\Models\Setting;
use Illuminate\Support\Carbon;

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

        // Pending payments
        $pendingPayments = TicketPayment::where('status', 'pending')->count();        

        // Reserved tickets expiring soon (within 10 minutes)
        $expiringReservations = Ticket::where('status', TicketStatus::Reserved)
            ->where('reserved_until', '>', now())
            ->where('reserved_until', '<', now()->addMinutes(10))
            ->whereDoesntHave('payment', fn($q) => $q->where('status', 'pending'))
            ->count();

        // Generating raffles
        $generatingRaffles = Raffle::where('status', RaffleStatus::Generating)->count();    

        // Raffle performance
        $rafflePerformance = Raffle::withCount([
            'tickets as total_ticket_count',
            'tickets as sold_count'   => fn($q) => $q->where('status', TicketStatus::Sold),
            'tickets as reserved_count' => fn($q) => $q->where('status', TicketStatus::Reserved),
        ])
        ->with('prizes')
        ->whereIn('status', [RaffleStatus::Active, RaffleStatus::Closed, RaffleStatus::Generating])
        ->latest()
        ->get()
        ->map(function ($raffle) {
            $raffle->funds_raised = $raffle->sold_count * $raffle->ticket_price;
            $raffle->progress     = $raffle->total_tickets > 0
                ? round(($raffle->sold_count / $raffle->total_tickets) * 100, 1)
                : 0;
            return $raffle;
        });

        $limit = (int) Setting::get('recent_updates_limit', 10);

        // Recent updates — last 10 across payments and new sponsors
        $recentPayments = TicketPayment::with(['ticket.raffle', 'sponsor', 'confirmedBy'])
            ->whereIn('status', ['confirmed', 'rejected'])
            ->latest('confirmed_at')
            ->limit($limit)
            ->get()
            ->map(fn($p) => [
                'type'      => 'payment',
                'status'    => $p->status->value,
                'message'   => $p->status->value === 'confirmed'
                    ? "Ticket {$p->ticket->ticket_number} confirmed for {$p->sponsor->fullName()}"
                    : "Ticket {$p->ticket->ticket_number} rejected for {$p->sponsor->fullName()}",
                'sub'       => $p->ticket->raffle->title,
                'time'      => $p->confirmed_at,
                'link'      => route('admin.payments.show', $p),
            ]);

        $recentSponsors = Sponsor::latest()
        ->limit($limit)
        ->get()
        ->map(fn($s) => [
            'type'    => 'sponsor',
            'status'  => 'new',
            'message' => "{$s->fullName()} registered as a sponsor",
            'sub'     => $s->email,
            'time'    => $s->created_at,
            'link'    => null,
        ]);

        // Merge and sort by time descending
        $recentUpdates = $recentPayments
            ->concat($recentSponsors)
            ->sortByDesc('time')
            ->take($limit)
            ->values();

        return view('dashboard', compact(
            'totalFunds',
            'activeRaffles',
            'ticketsSold',
            'totalSponsors',
            'pendingPayments',
            'expiringReservations',
            'generatingRaffles',
            'rafflePerformance',
            'recentUpdates',
            'limit'
        ));
    }
}