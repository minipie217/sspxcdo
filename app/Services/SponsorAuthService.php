<?php

namespace App\Services;

use App\Models\Sponsor;
use App\Notifications\SponsorOtpNotification;
use Illuminate\Support\Facades\RateLimiter;

class SponsorAuthService
{
    private const OTP_EXPIRY_MINUTES = 15;
    private const MAX_OTP_ATTEMPTS   = 5;

    public function register(array $data): Sponsor
    {
        $sponsor = Sponsor::create($data);

        $this->sendOtp($sponsor);

        return $sponsor;
    }

    public function sendOtp(Sponsor $sponsor): void
    {
        $otp = (string) random_int(100000, 999999);

        $sponsor->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
        ]);

        // Use login template if sponsor already verified, register template if not
        $templateKey = $sponsor->email_verified_at
            ? 'sponsor_login_otp'
            : 'sponsor_registration_otp';

        $sponsor->notify(new SponsorOtpNotification($otp, $templateKey));
    }

    public function verifyOtp(Sponsor $sponsor, string $otp): bool
    {
        $key = "otp.attempts.{$sponsor->id}";

        // Block after too many wrong attempts
        if (RateLimiter::tooManyAttempts($key, self::MAX_OTP_ATTEMPTS)) {
            return false;
        }

        // Wrong code or expired
        if ($sponsor->otp !== $otp || now()->isAfter($sponsor->otp_expires_at)) {
            RateLimiter::hit($key, 60 * self::OTP_EXPIRY_MINUTES);
            return false;
        }

        // Success — clear OTP and mark email verified
        $sponsor->update([
            'otp'               => null,
            'otp_expires_at'    => null,
            'email_verified_at' => now(),
        ]);

        RateLimiter::clear($key);

        return true;
    }
}