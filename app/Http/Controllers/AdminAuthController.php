<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AdminAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminRegisterRequest;

class AdminAuthController extends Controller
{
    public function __construct(private AdminAuthService $authService) {}

    // -----------------------------------------------------------------------
    // Register
    // -----------------------------------------------------------------------

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('admin.register');
    }

    public function register(AdminRegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());

        return redirect()->route('admin.login')
        ->with('otp_sent', true)
        ->with('otp_email', $user->email)
        ->with('success', 'Account created! Enter the verification code sent to your email.');
    }

    // Show login form
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('admin.login');
    }

    // Send OTP to admin email
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No admin account found with this email.',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        $this->authService->sendOtp($user);

        return redirect()->route('admin.login')
            ->with('otp_sent', true)
            ->with('otp_email', $user->email)
            ->with('success', 'A verification code has been sent to your email.');
    }

    // Verify OTP and log in
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        if (! $this->authService->verifyOtp($user, $request->otp)) {
            return back()
                ->with('otp_sent', true)
                ->with('otp_email', $request->email)
                ->withErrors(['otp' => 'Invalid or expired code. Please try again.']);
        }

        // Clear sponsor session before logging in as admin
        Auth::guard('sponsor')->logout();
        Auth::guard('web')->login($user, remember: true);

        return redirect()->intended(route('dashboard'))
            ->with('success', "Welcome back, {$user->name}!");
    }

    // Resend OTP
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        $this->authService->sendOtp($user);

        return back()
            ->with('otp_sent', true)
            ->with('otp_email', $user->email)
            ->with('success', 'A new code has been sent to your email.');
    }

    // Logout
    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'You have been logged out.');
    }
}