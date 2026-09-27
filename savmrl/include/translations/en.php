<?php
//EN
$strings = [
    // Main page
    'payoff' => 'The best free link shortener',
    'basic' => 'Basic',
    'advanced' => 'Advanced',
    'insert-link' => 'Insert your link (URL) here!',
    'drag-n-drop' => 'Release the link to paste it in the input field',
    'copy-link' => 'Copy the shortened link',
    'generate-another' => 'Generate another link',
    'see-stats' => 'See click statistics',
    'link-copied' => 'Link copied!',
    'redirect-link' => 'Redirect link:',
    'link-renamed' => 'Link renamed correctly from <b>{{source-link}}</b> to <b>{{new-link}}</b>',
    'generate-link' => 'Generate link',
    'generated-link' => 'Generated new link',
    'error-1' => '<i>[{{timestamp}}]</i> Error (<b>{{code}}</b>): <i>{{description}}</i>',
    'error-2' => 'Error. Try again later, check the URL is valid or contact a maintainer.',
    'error-generic' => 'Error. Try again later, check the URL is valid or contact a maintainer.',
    'advanced-text-1' => 'The link expires after',
    'advanced-text-2' => 'openings <b>or</b> on',
    'advanced-text-3' => 'You can set an access code',
    'max-openings' => 'Max openings',
    'expiry-date' => 'Expiry date',
    'access-code' => 'Access code',
    'optional' => 'Optional',
    'generate-qr' => 'Generate QR code',
    'redirect-delay' => 'Redirect delay (sec)',
    'redirect-delay-info' => 'seconds before redirect',
    'report-link' => 'Report this link',
    'report-reason' => 'Select a reason:',
    'report-phishing' => 'Phishing',
    'report-spam' => 'Spam',
    'report-illegal' => 'Illegal content',
    'report-other' => 'Other',
    'report-success' => 'Thank you for your report. The link has been flagged for review.',
    'report-error' => 'Error submitting report. Please try again.',
    'redirecting' => 'Redirecting...',
    'redirect-manual' => 'If the redirect does not work, click here',

    // Account suggestion
    'account-suggestion' => 'Create an account to manage your links, view statistics, and more.',
    'create-account' => 'Create account',
    'no-thanks' => 'No thanks',

    // Auth
    'login' => 'Login',
    'logout' => 'Logout',
    'signup' => 'Sign up',
    'email' => 'Email',
    'password' => 'Password',
    'username' => 'Username',
    'confirm-password' => 'Confirm password',
    'password-min' => 'Password (min 8 characters)',
    'no-account' => "Don't have an account?",
    'has-account' => 'Already have an account?',
    'enter-2fa' => 'Enter the 6-digit code sent to your email.',
    'verify' => 'Verify',
    'code-sent' => 'Verification code sent to your email',
    'account-created' => 'Account created! Check your email to verify your address.',
    'connection-error' => 'Connection error',
    'passwords-mismatch' => 'Passwords do not match',

    // Dashboard
    'your-links' => 'Your links',
    'settings' => 'Settings',
    'pro-account' => 'Pro account',
    'loading' => 'Loading...',
    'no-links' => 'No links yet.',
    'create-first' => 'Create your first link',
    'clicks' => 'clicks',
    'expires' => 'Expires:',
    'max-label' => 'Max:',
    'openings' => 'openings',
    'rename' => 'Rename',
    'expire' => 'Expire',
    'edit-settings' => 'Edit settings',
    'stats' => 'Stats',
    'confirm-expire' => 'Are you sure you want to expire this link? This cannot be undone.',
    'link-expired' => 'Link expired',
    'link-renamed-to' => 'Link renamed to',
    'enter-new-name' => 'Enter new name for the link:',

    // Settings
    'account-settings' => 'Account settings',
    'change-password' => 'Change password',
    'current-password' => 'Current password',
    'new-password' => 'New password (min 8 chars)',
    'confirm-new-password' => 'Confirm new password',
    'two-factor-auth' => 'Two-factor authentication',
    'status' => 'Status',
    'enabled' => 'Enabled',
    'disabled' => 'Disabled',
    'enable-2fa' => 'Enable 2FA',
    'disable-2fa' => 'Disable 2FA',
    'enter-code' => 'Enter the code sent to your email:',
    'confirm' => 'Confirm',
    'active-sessions' => 'Active sessions',
    'current' => 'Current',
    'revoke' => 'Revoke',
    'back-to-dashboard' => 'Back to dashboard',
    'password-changed' => 'Password changed. You will be logged out.',
    'session-revoked' => 'Session revoked',
    '2fa-enabled' => '2FA enabled successfully',
    '2fa-disabled' => '2FA disabled successfully',
    'preferred-language' => 'Preferred language',
    'language-saved' => 'Language preference saved',
    'save' => 'Save',

    // Link settings (pro)
    'edit-link' => 'Edit link:',
    'destination-url' => 'Destination URL',
    'destination-encrypted' => 'Encrypted (provide access code to change)',
    'destination-access-code-hint' => 'This link is protected by an access code. To change the destination, you must also enter the current access code.',
    'new-access-code' => 'New access code (leave empty to keep current)',
    'save-link-settings' => 'Save link settings',
    'link-settings-updated' => 'Link settings updated',

    // Badges
    'badge-active' => 'Active',
    'badge-expired' => 'Expired',
    'badge-reported' => 'Reported',
    'badge-blocked' => 'Blocked by admin',

    // Redirect page errors
    'error-admin-blocked' => 'This link has been blocked by an administrator for violating our terms of service.',

    // Footer
    'privacy' => 'Privacy policy',
    'terms' => 'Terms of service',
    'addons' => 'Web browser add-ons',
    'account' => 'Account',

    // Terms acceptance
    'accept-terms' => 'I have read and accept the <a href="/savmrl/privacy/" target="_blank">Privacy Policy</a> and the <a href="/savmrl/terms/" target="_blank">Terms of Service</a>',
    'must-accept-terms' => 'You must accept the Privacy Policy and Terms of Service',

    // Login required
    'login-required' => 'Account required',
    'login-required-desc' => 'For legal reasons, you need an account to create shortened links. Registration is free.',
    'verify-email-required' => 'Email verification required',
    'verify-email-required-desc' => 'Please verify your email address before creating links. Check your inbox for the verification code.',
    'email-not-verified' => 'Your email is not verified. Please verify your email before logging in.',
    'resend-verification' => 'Resend verification email',
    'verification-resent' => 'Verification email resent. Check your inbox.',

    // Delete account
    'delete-account' => 'Delete account',
    'delete-account-desc' => 'This will permanently delete your account and all your links. This cannot be undone.',
    'enter-password-to-delete' => 'Enter your password to confirm deletion:',
    'account-deleted' => 'Your account has been deleted.',
    'confirm-delete-account' => 'Confirm account deletion',

    // Password change OTP
    'verify-code-to-change-password' => 'Enter the verification code sent to your email to confirm the password change:',
    'password-change-code-sent' => 'A verification code has been sent to your email.',

    // Language selector
    'language' => 'Language',
];
?>
