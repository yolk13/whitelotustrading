<?php

class Mail
{
    public static function send(string $to, string $subject, string $body, ?string $replyTo = null): bool
    {
        $from = sprintf('%s <%s>', SITE_NAME, ADMIN_EMAIL);

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: $from\r\n";
        $headers .= "X-Mailer: White Lotus CMS\r\n";

        if ($replyTo) {
            $headers .= "Reply-To: $replyTo\r\n";
        }

        $fullSubject = sprintf('[%s] %s', SITE_NAME, $subject);

        return mail($to, $fullSubject, $body, $headers);
    }

    public static function sendInquiryNotification(array $inquiry): bool
    {
        $body = <<<HTML
<div style="font-family: system-ui, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #1a1a2e;">New Inquiry Received</h2>
    <table style="width: 100%; border-collapse: collapse;">
        <tr><td style="padding: 8px; border-bottom: 1px solid #eee; color: #666;">From</td><td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">{$inquiry['name']}</td></tr>
        <tr><td style="padding: 8px; border-bottom: 1px solid #eee; color: #666;">Email</td><td style="padding: 8px; border-bottom: 1px solid #eee;"><a href="mailto:{$inquiry['email']}">{$inquiry['email']}</a></td></tr>
        <tr><td style="padding: 8px; border-bottom: 1px solid #eee; color: #666;">Phone</td><td style="padding: 8px; border-bottom: 1px solid #eee;">{$inquiry['phone']}</td></tr>
        <tr><td style="padding: 8px; border-bottom: 1px solid #eee; color: #666;">Company</td><td style="padding: 8px; border-bottom: 1px solid #eee;">{$inquiry['company']}</td></tr>
        <tr><td style="padding: 8px; border-bottom: 1px solid #eee; color: #666;">Division</td><td style="padding: 8px; border-bottom: 1px solid #eee;">{$inquiry['division']}</td></tr>
        <tr><td style="padding: 8px; border-bottom: 1px solid #eee; color: #666;">Subject</td><td style="padding: 8px; border-bottom: 1px solid #eee; font-weight: bold;">{$inquiry['subject']}</td></tr>
    </table>
    <div style="margin-top: 16px; padding: 16px; background: #f8f9fa; border-radius: 8px;">
        <p style="margin: 0; color: #333;">{$inquiry['message']}</p>
    </div>
    <p style="margin-top: 24px; font-size: 12px; color: #999;"><a href="https://whitelotusfze.com/admin/inquiries">View in Admin Panel</a></p>
</div>
HTML;

        return self::send(ADMIN_EMAIL, "New Inquiry: {$inquiry['subject']}", $body, "{$inquiry['name']} <{$inquiry['email']}>");
    }

    public static function sendInquiryConfirmation(array $inquiry): bool
    {
        $body = <<<HTML
<div style="font-family: system-ui, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #1a1a2e;">Thank You for Your Inquiry</h2>
    <p>Dear {$inquiry['name']},</p>
    <p>Thank you for reaching out to White Lotus Trading - F.Z.E. We have received your inquiry and our team will review it shortly.</p>
    <p><strong>Subject:</strong> {$inquiry['subject']}</p>
    <p><strong>Division:</strong> {$inquiry['division']}</p>
    <div style="padding: 16px; background: #f8f9fa; border-radius: 8px; margin: 16px 0;">
        <p style="margin: 0; color: #333;"><em>{$inquiry['message']}</em></p>
    </div>
    <p>We aim to respond within 24 hours. If you have any urgent matters, please contact us directly.</p>
    <hr style="border: none; border-top: 1px solid #eee; margin: 24px 0;">
    <p style="font-size: 12px; color: #999;">White Lotus Trading - F.Z.E.<br>Dubai, United Arab Emirates</p>
</div>
HTML;

        return self::send($inquiry['email'], "We received your inquiry: {$inquiry['subject']}", $body);
    }

    public static function sendInquiryReply(array $inquiry, string $reply): bool
    {
        $body = <<<HTML
<div style="font-family: system-ui, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #1a1a2e;">Response to Your Inquiry</h2>
    <p>Dear {$inquiry['name']},</p>
    <p>Regarding your inquiry: <strong>{$inquiry['subject']}</strong></p>
    <div style="padding: 16px; background: #f8f9fa; border-radius: 8px; margin: 16px 0;">
        <p style="margin: 0; color: #333;">{$reply}</p>
    </div>
    <p>If you have further questions, please don't hesitate to contact us.</p>
    <hr style="border: none; border-top: 1px solid #eee; margin: 24px 0;">
    <p style="font-size: 12px; color: #999;">White Lotus Trading - F.Z.E.<br>Dubai, United Arab Emirates</p>
</div>
HTML;

        return self::send($inquiry['email'], "Re: {$inquiry['subject']}", $body);
    }
}
