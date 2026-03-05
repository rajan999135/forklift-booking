<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (Throwable $e) {
            return redirect('/login')->withErrors(['social_login' => 'An error occurred during social login.']);
        }

        $email = $socialUser->getEmail();

        // Check for existing user by provider ID
        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        // Check by email if not found by provider
        if (!$user && $email) {
            $user = User::where('email', $email)->first();
        }

        // Create new user if doesn't exist
        if (!$user) {
            $user = User::create([
                'name'              => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'email'             => $email,
                'provider'          => $provider,
                'provider_id'       => $socialUser->getId(),
                'password'          => bcrypt(Str::random(32)),
                'email_verified_at' => null, // not verified yet
            ]);
        }

        // Update provider info if user existed but logged in via different method
        if ($user->provider !== $provider || $user->provider_id !== $socialUser->getId()) {
            $user->update([
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }

        // Log in the user
        Auth::login($user);

        // If email is NOT verified → generate OTP and send to OTP page
        if (!$user->hasVerifiedEmail()) {
            $this->sendOtp($user);
            return redirect()->route('otp.show');
        }

        // Email is verified → go to dashboard
        return redirect()->intended('/dashboard');
    }

    /**
     * Generate and send OTP to the user.
     */
    private function sendOtp(User $user): void
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp'            => bcrypt($otp),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));
    }
}