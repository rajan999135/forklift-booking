<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Not logged in — skip
        if (!$user) {
            return $next($request);
        }

        // ✅ Admin users skip OTP entirely
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Already verified — no problem
        if (!is_null($user->email_verified_at)) {
            return $next($request);
        }

        // On OTP related routes — let through
        if ($request->routeIs('otp.*')) {
            return $next($request);
        }

        // Regular user not verified — send to OTP page
        return redirect()->route('otp.show');
    }
}