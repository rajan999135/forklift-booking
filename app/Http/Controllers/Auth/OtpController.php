<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OtpController extends Controller
{
    /**
     * Show the OTP verification page.
     */
    public function show(): View
    {
        return view('auth.verify-otp');
    }

    /**
     * Send a fresh OTP to the user's email.
     */
    public function send(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Generate a 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save OTP and expiry (10 minutes)
        $user->update([
            'otp'            => bcrypt($otp),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP email
        \Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return back()->with('status', 'otp-sent');
    }

    /**
     * Verify the submitted OTP.
     */
    public function verify(Request $request): RedirectResponse
{
    $request->validate([
        'otp' => ['required', 'string', 'size:6'],
    ]);

    $user = $request->user();

    // Check if OTP has expired
    if (!$user->otp_expires_at || now()->isAfter($user->otp_expires_at)) {
        return back()->withErrors(['otp' => 'Your OTP has expired. Please request a new one.']);
    }

    // Check if OTP matches
    if (!\Hash::check($request->otp, $user->otp)) {
        return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
    }

    // Mark email as verified and clear OTP
    $user->markEmailAsVerified();
    $user->update([
        'otp'            => null,
        'otp_expires_at' => null,
    ]);

    return redirect()->route('verification.success');
}
}
