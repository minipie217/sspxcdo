<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRaffleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('web')->check();
    }

    public function rules(): array
    {
        return [
            // Raffle details
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',

            // Ticket config
            'ticket_price'      => 'required|numeric|min:0',
            'total_tickets'     => 'required|integer|min:1|max:100000',
            'ticket_digits'     => 'required|integer|min:1|max:10',
            'ticket_prefix'     => 'nullable|string|max:10',

            // Prizes
            'first_prize'       => 'required|string|max:255',
            'second_prize'      => 'required|string|max:255',
            'third_prize'       => 'required|string|max:255',
            'consolation_count' => 'nullable|integer|min:0|max:1000',
            'consolation_name'  => 'nullable|string|max:255',

            // Schedule
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'draw_date'         => 'required|date',

            // Status
            'status'            => 'required|in:draft,active,closed',
        ];
    }

    public function messages(): array
    {
        return [
            'draw_date.required' => 'Please set a draw date.',
            'total_tickets.max'  => 'Maximum of 100,000 tickets per raffle.',
        ];
    }
}