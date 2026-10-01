<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/alpha/include/mailer.php';

function email_wrap($content) {
    return '<!DOCTYPE html>'
        . '<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">'
        . '</head>'
        . '<body style="margin:0;padding:0;font-family:Inter,Arial,Helvetica,sans-serif;background:#f5f6f8;">'
        . '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f6f8;padding:32px 16px;">'
        . '<tr><td align="center">'

        // Card container
        . '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">'

        // Header
        . '<tr><td style="background:#00A7AA;padding:14px 24px;text-align:center;">'
        . '<a href="https://savmrl.it" style="text-decoration:none;display:inline-block;">'
        . '<img src="https://savmrl.it/alpha/images/icon-big.svg" alt="savmrl.it" width="64" style="display:block;margin:0 auto;height:auto;" />'
        . '</a>'
        . '</td></tr>'

        // Content
        . '<tr><td style="background:#ffffff;padding:36px 32px;">'
        . $content
        . '</td></tr>'

        // Footer
        . '<tr><td style="background:#00A7AA;padding:20px 24px;text-align:center;">'
        . '<p style="margin:0;font-size:13px;color:#ffffff;font-family:Inter,Arial,Helvetica,sans-serif;">Developed by Saverio Morelli</p>'
        . '<p style="margin:6px 0 0;font-size:11px;"><a href="https://savmrl.it" style="color:#d0f0f0;text-decoration:none;">savmrl.it</a></p>'
        . '</td></tr>'

        . '</table>'

        // Disclaimer
        . '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:560px;">'
        . '<tr><td style="padding:16px 8px;text-align:center;font-size:11px;color:#999;font-family:Inter,Arial,Helvetica,sans-serif;">'
        . 'This email was sent by savmrl.it &mdash; If you didn\'t request this, you can safely ignore it.'
        . '</td></tr></table>'

        . '</td></tr></table>'
        . '</body></html>';
}

function send_verification_email($to_email, $username, $verification_url) {
    $name = $username ?: 'there';
    $content = '<h2 style="margin:0 0 16px;color:#1a2b3c;font-family:Inter,Arial,Helvetica,sans-serif;font-size:22px;font-weight:700;">Verify your email</h2>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Hi ' . htmlspecialchars($name) . ',</p>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Thanks for signing up! Please verify your email address by clicking the button below.</p>'
        . '<p style="text-align:center;margin:28px 0;">'
        . '<a href="' . htmlspecialchars($verification_url) . '" style="display:inline-block;background:#00A7AA;color:#ffffff;text-decoration:none;padding:14px 36px;border-radius:8px;font-weight:600;font-size:15px;font-family:Inter,Arial,Helvetica,sans-serif;">Verify Email</a>'
        . '</p>'
        . '<p style="color:#999;font-size:12px;line-height:1.5;">Or copy this link:<br>'
        . '<a href="' . htmlspecialchars($verification_url) . '" style="color:#00A7AA;word-break:break-all;">' . htmlspecialchars($verification_url) . '</a></p>';
    return send_email($to_email, 'Verify your savmrl.it email', email_wrap($content));
}

function send_otp_email($to_email, $username, $otp_code) {
    $name = $username ?: 'there';
    $content = '<h2 style="margin:0 0 16px;color:#1a2b3c;font-family:Inter,Arial,Helvetica,sans-serif;font-size:22px;font-weight:700;">Your verification code</h2>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Hi ' . htmlspecialchars($name) . ',</p>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Use the following code to complete your login:</p>'
        . '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0;">'
        . '<tr><td align="center">'
        . '<div style="display:inline-block;background:#f5f6f8;border:2px solid #e2e6ea;border-radius:12px;padding:20px 40px;">'
        . '<span style="font-size:36px;font-weight:700;letter-spacing:8px;color:#00A7AA;font-family:Inter,Arial,Helvetica,sans-serif;">' . htmlspecialchars($otp_code) . '</span>'
        . '</div>'
        . '</td></tr></table>'
        . '<p style="color:#999;font-size:12px;line-height:1.5;">This code expires in 30 minutes. If you didn\'t try to log in, you can ignore this email.</p>';
    return send_email($to_email, 'Your savmrl.it login code', email_wrap($content));
}

function send_password_reset_email($to_email, $username, $otp_code) {
    $name = $username ?: 'there';
    $content = '<h2 style="margin:0 0 16px;color:#1a2b3c;font-family:Inter,Arial,Helvetica,sans-serif;font-size:22px;font-weight:700;">Password reset</h2>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Hi ' . htmlspecialchars($name) . ',</p>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">You requested to reset your password. Use the following code:</p>'
        . '<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0;">'
        . '<tr><td align="center">'
        . '<div style="display:inline-block;background:#f5f6f8;border:2px solid #e2e6ea;border-radius:12px;padding:20px 40px;">'
        . '<span style="font-size:36px;font-weight:700;letter-spacing:8px;color:#00A7AA;font-family:Inter,Arial,Helvetica,sans-serif;">' . htmlspecialchars($otp_code) . '</span>'
        . '</div>'
        . '</td></tr></table>'
        . '<p style="color:#999;font-size:12px;line-height:1.5;">This code expires in 30 minutes. If you didn\'t request a password reset, you can safely ignore this email.</p>';
    return send_email($to_email, 'savmrl.it — Password reset code', email_wrap($content));
}

function send_password_changed_email($to_email, $username) {
    $name = $username ?: 'there';
    $content = '<h2 style="margin:0 0 16px;color:#1a2b3c;font-family:Inter,Arial,Helvetica,sans-serif;font-size:22px;font-weight:700;">Password changed</h2>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Hi ' . htmlspecialchars($name) . ',</p>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Your password has been changed successfully. All other sessions have been logged out.</p>'
        . '<p style="color:#999;font-size:12px;line-height:1.5;">If you didn\'t make this change, please contact us immediately.</p>';
    return send_email($to_email, 'savmrl.it — Password changed', email_wrap($content));
}

function send_login_success_email($to_email, $username) {
    $name = $username ?: 'there';
    $ip = function_exists('getIpAddress') ? getIpAddress() : ($_SERVER['REMOTE_ADDR'] ?? 'Unknown');
    $time = date('d/m/Y H:i:s');
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? htmlspecialchars(substr($_SERVER['HTTP_USER_AGENT'], 0, 200)) : 'Unknown';
    $content = '<h2 style="margin:0 0 16px;color:#1a2b3c;font-family:Inter,Arial,Helvetica,sans-serif;font-size:22px;font-weight:700;">New login to your account</h2>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Hi ' . htmlspecialchars($name) . ',</p>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">A new login was detected on your savmrl.it account.</p>'
        . '<table cellpadding="0" cellspacing="0" border="0" style="margin:16px 0;font-size:14px;color:#6b7a8d;">'
        . '<tr><td style="padding:4px 16px 4px 0;font-weight:600;">IP Address:</td><td>' . htmlspecialchars($ip) . '</td></tr>'
        . '<tr><td style="padding:4px 16px 4px 0;font-weight:600;">Time:</td><td>' . $time . '</td></tr>'
        . '<tr><td style="padding:4px 16px 4px 0;font-weight:600;">Browser:</td><td style="word-break:break-all;">' . $ua . '</td></tr>'
        . '</table>'
        . '<p style="color:#999;font-size:12px;line-height:1.5;">If this wasn\'t you, change your password immediately and contact us.</p>';
    return send_email($to_email, 'savmrl.it — New login detected', email_wrap($content));
}

function send_login_failed_email($to_email, $username) {
    $name = $username ?: 'there';
    $ip = function_exists('getIpAddress') ? getIpAddress() : ($_SERVER['REMOTE_ADDR'] ?? 'Unknown');
    $time = date('d/m/Y H:i:s');
    $content = '<h2 style="margin:0 0 16px;color:#1a2b3c;font-family:Inter,Arial,Helvetica,sans-serif;font-size:22px;font-weight:700;">Failed login attempt</h2>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Hi ' . htmlspecialchars($name) . ',</p>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">A failed login attempt was made on your savmrl.it account.</p>'
        . '<table cellpadding="0" cellspacing="0" border="0" style="margin:16px 0;font-size:14px;color:#6b7a8d;">'
        . '<tr><td style="padding:4px 16px 4px 0;font-weight:600;">IP Address:</td><td>' . htmlspecialchars($ip) . '</td></tr>'
        . '<tr><td style="padding:4px 16px 4px 0;font-weight:600;">Time:</td><td>' . $time . '</td></tr>'
        . '</table>'
        . '<p style="color:#999;font-size:12px;line-height:1.5;">If this wasn\'t you, we recommend changing your password as a precaution.</p>';
    return send_email($to_email, 'savmrl.it — Failed login attempt', email_wrap($content));
}

function send_account_deleted_email($to_email, $username) {
    $name = $username ?: 'there';
    $content = '<h2 style="margin:0 0 16px;color:#1a2b3c;font-family:Inter,Arial,Helvetica,sans-serif;font-size:22px;font-weight:700;">Account deleted</h2>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Hi ' . htmlspecialchars($name) . ',</p>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Your savmrl.it account has been permanently deleted. All your sessions have been terminated.</p>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Your shortened links will remain active but are no longer associated with an account.</p>'
        . '<p style="color:#999;font-size:12px;line-height:1.5;">If you didn\'t request this, please contact us immediately.</p>';
    return send_email($to_email, 'savmrl.it — Account deleted', email_wrap($content));
}

function send_2fa_changed_email($to_email, $username, $enabled) {
    $name = $username ?: 'there';
    $status = $enabled ? 'enabled' : 'disabled';
    $content = '<h2 style="margin:0 0 16px;color:#1a2b3c;font-family:Inter,Arial,Helvetica,sans-serif;font-size:22px;font-weight:700;">Two-factor authentication ' . $status . '</h2>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Hi ' . htmlspecialchars($name) . ',</p>'
        . '<p style="color:#6b7a8d;line-height:1.6;font-size:15px;">Two-factor authentication has been <strong>' . $status . '</strong> on your savmrl.it account.</p>'
        . '<p style="color:#999;font-size:12px;line-height:1.5;">If you didn\'t make this change, please contact us immediately.</p>';
    return send_email($to_email, 'savmrl.it — 2FA ' . $status, email_wrap($content));
}
