<?php

namespace App\Http\Controllers;

use App\Http\Requests\SponsorRegisterRequest;
use App\Http\Requests\SponsorVerifyOtpRequest;
use App\Models\Sponsor;
use App\Services\SponsorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SponsorAuthController extends Controller
{
    public function __construct(private SponsorAuthService $authService) {}

    // Show registration form
    public function showRegister()
    {
        if (Auth::guard('sponsor')->check()) {
            return redirect()->route('raffle.index');
        }

        return view('sponsor.register');
    }

    // Store new sponsor + send OTP
    public function register(SponsorRegisterRequest $request)
    {
        $sponsor = $this->authService->register($request->validated());

        return redirect()->route('sponsor.register')
            ->with('otp_sent', true)
            ->with('otp_email', $sponsor->email)
            ->with('success', 'A 6-digit code has been sent to your email.');
    }

    // Show login form (existing sponsors)
    public function showLogin()
    {
        if (Auth::guard('sponsor')->check()) {
            return redirect()->route('raffle.index');
        }

        return view('sponsor.login');
    }

    // Send OTP to existing sponsor
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:sponsors,email',
        ], [
            'email.exists' => 'No account found with this email. Please register first.',
        ]);

        $sponsor = Sponsor::where('email', $request->email)->firstOrFail();

        $this->authService->sendOtp($sponsor);

        return redirect()->route('sponsor.login')
            ->with('otp_sent', true)
            ->with('otp_email', $sponsor->email)
            ->with('success', 'A verification code has been sent to your email.');
    }

    // Verify OTP — logs sponsor in
    public function verifyOtp(SponsorVerifyOtpRequest $request)
    {
        $sponsor = Sponsor::where('email', $request->email)->firstOrFail();

        if (! $this->authService->verifyOtp($sponsor, $request->otp)) {
            return back()
                ->with('otp_sent', true)
                ->with('otp_email', $request->email)
                ->withErrors(['otp' => 'Invalid or expired code. Please try again.']);
        }

        // Clear admin session before logging in as sponsor
        Auth::guard('web')->logout();

        Auth::guard('sponsor')->login($sponsor, remember: true);

        return redirect()->intended(route('raffle.index'))
            ->with('success', "Welcome, {$sponsor->first_name}!");
    }

    // Resend OTP
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:sponsors,email',
        ]);

        $sponsor = Sponsor::where('email', $request->email)->firstOrFail();

        $this->authService->sendOtp($sponsor);

        return back()
            ->with('otp_sent', true)
            ->with('otp_email', $sponsor->email)
            ->with('success', 'A new code has been sent to your email.');
    }

    // Logout
    public function logout()
    {
        Auth::guard('sponsor')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('sponsor.login')
            ->with('success', 'You have been logged out.');
    }
}