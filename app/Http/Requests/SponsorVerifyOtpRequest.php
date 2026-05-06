<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SponsorVerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:sponsors,email',
            'otp'   => 'required|digits:6',
        ];
    }

    public function messages(): array
    {
        return [
            'otp.digits' => 'The verification code must be exactly 6 digits.',
        ];
    }
}