<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your password has been changed</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f5f7; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f5f7; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border: 1px solid #e5e7eb;">
                    <tr>
                        <td style="padding: 32px 40px 24px;">
                            <h1 style="margin: 0 0 8px; font-size: 20px; font-weight: 600; color: #1a1a1a;">Password Changed</h1>
                            <p style="margin: 0 0 24px; font-size: 14px; color: #6b7280; line-height: 1.5;">
                                Hello {{ $userName }}, your account password has been updated by an administrator.
                            </p>

                            @if(!empty($newPassword))
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9fafb; border: 1px solid #e5e7eb; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 4px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Your New Password</p>
                                        <p style="margin: 0; font-size: 18px; font-weight: 600; color: #1a1a1a; font-family: monospace; letter-spacing: 1px;">{{ $newPassword }}</p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <p style="margin: 0 0 6px; font-size: 13px; color: #6b7280; line-height: 1.5;">
                                @if(!empty($newPassword))
                                    Please change this password after your first login for security purposes.
                                @else
                                    If you did not expect this change, please contact your administrator immediately.
                                @endif
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 16px 40px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                                This is an automated message. Please do not reply.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
