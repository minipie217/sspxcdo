<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReserveTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('sponsor')->check();
    }

    public function rules(): array
    {
        return [
            'use_other_name'    => 'boolean',
            'holder_first_name' => 'required_if:use_other_name,true|nullable|string|max:255',
            'holder_last_name'  => 'required_if:use_other_name,true|nullable|string|max:255',
        ];
    }
}