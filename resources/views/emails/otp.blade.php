<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Verification Code</title>
</head>
<body style="margin:0;padding:0;background:#f0f4f3;font-family:'Segoe UI',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="520" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.07);">

                    <!-- Header -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#16a34a,#22c55e);padding:32px;text-align:center;">
                            <div style="font-size:28px;margin-bottom:8px;">🚜</div>
                            <h1 style="color:#fff;margin:0;font-size:1.3rem;font-weight:800;">Forklift Booking</h1>
                            <p style="color:rgba(255,255,255,0.8);margin:4px 0 0;font-size:0.8rem;">Equipment Rental Platform</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:36px 40px;">
                            <p style="color:#374151;font-size:0.95rem;margin:0 0 8px;">Hello, <strong>{{ $name }}</strong>!</p>
                            <p style="color:#6b7280;font-size:0.875rem;line-height:1.6;margin:0 0 28px;">
                                Use the verification code below to activate your account. This code expires in <strong>10 minutes</strong>.
                            </p>

                            <!-- OTP Box -->
                            <div style="background:#f0fdf4;border:2px dashed #16a34a;border-radius:12px;padding:24px;text-align:center;margin-bottom:28px;">
                                <p style="color:#6b7280;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 8px;font-weight:600;">Your Verification Code</p>
                                <p style="font-size:2.5rem;font-weight:800;color:#16a34a;letter-spacing:0.3em;margin:0;">{{ $otp }}</p>
                            </div>

                            <p style="color:#9ca3af;font-size:0.78rem;line-height:1.5;margin:0;">
                                If you did not create an account with Forklift Booking, please ignore this email. Do not share this code with anyone.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f9fafb;padding:20px 40px;border-top:1px solid #f3f4f6;text-align:center;">
                            <p style="color:#9ca3af;font-size:0.75rem;margin:0;">© {{ date('Y') }} Forklift Booking. All rights reserved.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
