<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New project inquiry</title>
</head>
<body style="margin:0;padding:0;background:#f4f2ee;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;color:#1c1a17">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f2ee;padding:32px 16px">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background:#ffffff;border:1px solid #e2ddd4;border-radius:10px;overflow:hidden">
                    <tr>
                        <td style="padding:24px 28px;border-bottom:1px solid #e2ddd4">
                            <p style="margin:0;font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:#8a7a52">New project inquiry</p>
                            <h1 style="margin:8px 0 0;font-family:Georgia,'Times New Roman',serif;font-weight:400;font-size:28px;line-height:1.15">{{ $inquiry->name }}</h1>
                            <p style="margin:6px 0 0;font-size:13px;color:#6e675d">{{ $inquiry->created_at?->format('l, F j, Y · g:i A') }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 28px">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;line-height:1.7">
                                <tr><td style="color:#8a8478;width:130px;padding:4px 0">Email</td><td><a href="mailto:{{ $inquiry->email }}" style="color:#1c1a17">{{ $inquiry->email }}</a></td></tr>
                                <tr><td style="color:#8a8478;padding:4px 0">Business</td><td>{{ $inquiry->business_type ?: '—' }}</td></tr>
                                <tr><td style="color:#8a8478;padding:4px 0">Service</td><td>{{ $inquiry->service ?: '—' }}</td></tr>
                                <tr><td style="color:#8a8478;padding:4px 0">Budget</td><td>{{ $inquiry->budget ?: '—' }}</td></tr>
                            </table>

                            <div style="margin-top:24px;padding:18px 20px;background:#f8f6f2;border-left:2px solid #b99340;border-radius:4px">
                                <p style="margin:0;font-size:14px;line-height:1.75;white-space:pre-line">{{ $inquiry->message }}</p>
                            </div>

                            <p style="margin:26px 0 0">
                                <a href="{{ route('admin.inquiries.show', $inquiry) }}" style="display:inline-block;padding:12px 20px;background:#1c1a17;color:#ffffff;border-radius:6px;text-decoration:none;font-size:13px;font-weight:600">Open in the admin</a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 28px;border-top:1px solid #e2ddd4;font-size:11px;color:#8a8478">
                            Reply directly to this email to answer {{ $inquiry->name }}.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
