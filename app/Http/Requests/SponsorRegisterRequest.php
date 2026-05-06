<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SponsorRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'      => 'required|in:Mr,Mrs,Ms',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:sponsors,email',
            'phone'      => 'required|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered. Please log in instead.',
        ];
    }
}