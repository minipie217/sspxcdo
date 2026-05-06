<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AdminOtpNotification;
use Illuminate\Support\Facades\RateLimiter;

class AdminAuthService
{
    private const OTP_EXPIRY_MINUTES = 15;
    private const MAX_OTP_ATTEMPTS   = 5;

    public function register(array $data): User
    {
        $user = User::create([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        $this->sendOtp($user);

        return $user;
    }

    public function sendOtp(User $user): void
    {
        $otp = (string) random_int(100000, 999999);

        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
        ]);

        $user->notify(new AdminOtpNotification($otp));
    }

    public function verifyOtp(User $user, string $otp): bool
    {
        $key = "admin.otp.attempts.{$user->id}";

        if (RateLimiter::tooManyAttempts($key, self::MAX_OTP_ATTEMPTS)) {
            return false;
        }

        if ($user->otp !== $otp || now()->isAfter($user->otp_expires_at)) {
            RateLimiter::hit($key, 60 * self::OTP_EXPIRY_MINUTES);
            return false;
        }

        $user->update([
            'otp'            => null,
            'otp_expires_at' => null,
        ]);

        RateLimiter::clear($key);

        return true;
    }
}