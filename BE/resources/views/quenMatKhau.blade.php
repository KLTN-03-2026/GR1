<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f7fb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f7fb; padding:40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px; background:#ffffff; border-radius:20px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.08);">
                    <tr>
                        <td style="background:#0ea5e9; padding:28px 32px; text-align:center;">
                            <h1 style="margin:0; font-size:24px; color:#ffffff;">Khôi phục mật khẩu</h1>
                            <p style="margin:8px 0 0; font-size:14px; color:rgba(255,255,255,0.9);">
                                DaNang Travel
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:36px 32px;">
                            <p style="margin:0 0 16px; font-size:16px;">
                                Xin chào <strong>{{ $ten }}</strong>,
                            </p>

                            <p style="margin:0 0 16px; font-size:15px; line-height:1.7; color:#4b5563;">
                                Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.
                                Nhấn vào nút bên dưới để tạo mật khẩu mới.
                            </p>

                            <div style="text-align:center; margin:32px 0;">
                                <a href="{{ $link }}"
                                   style="display:inline-block; padding:14px 28px; background:#2563eb; color:#ffffff; text-decoration:none; border-radius:12px; font-weight:bold; font-size:15px;">
                                    Đặt lại mật khẩu
                                </a>
                            </div>

                            <p style="margin:0 0 12px; font-size:14px; color:#6b7280; line-height:1.7;">
                                Nếu nút không hoạt động, bạn có thể sao chép liên kết sau và dán vào trình duyệt:
                            </p>

                            <p style="margin:0 0 20px; word-break:break-all;">
                                <a href="{{ $link }}" style="color:#0ea5e9; font-size:14px; text-decoration:none;">
                                    {{ $link }}
                                </a>
                            </p>

                            <div style="padding:16px; background:#f9fafb; border-radius:12px; border:1px solid #e5e7eb;">
                                <p style="margin:0; font-size:13px; color:#6b7280; line-height:1.7;">
                                    Nếu bạn không yêu cầu đổi mật khẩu, hãy bỏ qua email này.
                                    Vì lý do bảo mật, vui lòng không chia sẻ liên kết này cho người khác.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px; text-align:center; background:#f9fafb; border-top:1px solid #e5e7eb;">
                            <p style="margin:0; font-size:13px; color:#9ca3af;">
                                © {{ date('Y') }} DaNang Travel. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
