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

        if ($user && is_null($user->email_verified_at)) {
            // If they're already on the OTP page or resend route, let them through
            if ($request->routeIs('otp.show') || $request->routeIs('otp.send') || $request->routeIs('otp.verify')) {
                return $next($request);
            }

            return redirect()->route('otp.show');
        }

        return $next($request);
    }
}