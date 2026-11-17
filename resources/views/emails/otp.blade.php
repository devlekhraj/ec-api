<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Password Reset OTP</title>
  <style>
    body { margin: 0; padding: 0; background: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    .wrapper { max-width: 520px; margin: 40px auto; background: #ffffff; border-radius: 10px; overflow: hidden; }
    .header { background: linear-gradient(135deg, #3fc183, #4f46e5); padding: 32px 40px; text-align: center; }
    .header h1 { margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; }
    .body { padding: 32px 40px; }
    .greeting { font-size: 15px; color: #374151; margin-bottom: 16px; }
    .description { font-size: 14px; color: #6b7280; line-height: 1.6; margin-bottom: 28px; }
    .otp-box { text-align: center; margin: 0 auto 28px; }
    .otp-code { display: inline-block; font-size: 38px; font-weight: 800; letter-spacing: 10px; color: #1e1e2e; background: #f0fdf4; border: 2px dashed #3fc183; border-radius: 10px; padding: 16px 32px; }
    .expire-note { font-size: 13px; color: #9ca3af; text-align: center; margin-bottom: 24px; }
    .warning { font-size: 13px; color: #6b7280; background: #fef9c3; border-left: 3px solid #f59e0b; border-radius: 4px; padding: 12px 16px; margin-bottom: 24px; }
    .footer { background: #f9fafb; padding: 20px 40px; text-align: center; border-top: 1px solid #e5e7eb; }
    .footer p { margin: 0; font-size: 12px; color: #9ca3af; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <h1>🔐 Password Reset</h1>
    </div>
    <div class="body">
      <p class="greeting">Hello, <strong>{{ $recipientName }}</strong></p>
      <p class="description">
        We received a request to reset your password. Use the OTP below to proceed.
        Do not share this code with anyone.
      </p>

      <div class="otp-box">
        <div class="otp-code">{{ $otp }}</div>
      </div>

      <p class="expire-note">This code expires in <strong>{{ $expiresInMin }} minutes</strong>.</p>

      <div class="warning">
        If you did not request a password reset, please ignore this email. Your account remains secure.
      </div>
    </div>
    <div class="footer">
      <p>{{ config('app.name') }} &mdash; Admin Panel</p>
    </div>
  </div>
</body>
</html>
