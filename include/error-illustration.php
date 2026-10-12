<?php
if (!isset($illustration)) $illustration = 'not-found';

if ($illustration === 'not-found'): ?>
<svg class="error-illustration" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="100" cy="80" r="60" fill="var(--color-bg-secondary)" opacity="0.5"/>
    <path d="M70 90 L90 70 L100 80 L120 55 L140 90 Z" fill="var(--color-border)" opacity="0.6"/>
    <rect x="55" y="45" width="90" height="70" rx="8" stroke="var(--color-text-secondary)" stroke-width="2.5" fill="none"/>
    <line x1="55" y1="55" x2="145" y2="55" stroke="var(--color-text-secondary)" stroke-width="2.5"/>
    <circle cx="65" cy="50" r="2" fill="var(--color-error)"/>
    <circle cx="73" cy="50" r="2" fill="var(--color-warning)"/>
    <circle cx="81" cy="50" r="2" fill="var(--color-success)"/>
    <text x="100" y="135" text-anchor="middle" font-size="40" font-weight="700" fill="var(--color-text-secondary)" opacity="0.3" font-family="var(--font-body)">404</text>
</svg>
<?php elseif ($illustration === 'expired'): ?>
<svg class="error-illustration" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="100" cy="75" r="60" fill="var(--color-bg-secondary)" opacity="0.5"/>
    <circle cx="100" cy="75" r="38" stroke="var(--color-text-secondary)" stroke-width="2.5" fill="none"/>
    <circle cx="100" cy="75" r="3" fill="var(--color-text-secondary)"/>
    <line x1="100" y1="75" x2="100" y2="52" stroke="var(--color-text-secondary)" stroke-width="2.5" stroke-linecap="round"/>
    <line x1="100" y1="75" x2="118" y2="82" stroke="var(--color-text-secondary)" stroke-width="2" stroke-linecap="round"/>
    <line x1="128" y1="50" x2="140" y2="38" stroke="var(--color-error)" stroke-width="3" stroke-linecap="round"/>
    <line x1="140" y1="50" x2="128" y2="38" stroke="var(--color-error)" stroke-width="3" stroke-linecap="round"/>
</svg>
<?php elseif ($illustration === 'locked'): ?>
<svg class="error-illustration" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="100" cy="80" r="60" fill="var(--color-bg-secondary)" opacity="0.5"/>
    <rect x="75" y="75" width="50" height="40" rx="6" stroke="var(--color-text-secondary)" stroke-width="2.5" fill="none"/>
    <path d="M85 75 V63 A15 15 0 0 1 115 63 V75" stroke="var(--color-text-secondary)" stroke-width="2.5" fill="none" stroke-linecap="round"/>
    <circle cx="100" cy="93" r="5" fill="var(--color-text-secondary)"/>
    <line x1="100" y1="98" x2="100" y2="105" stroke="var(--color-text-secondary)" stroke-width="2.5" stroke-linecap="round"/>
</svg>
<?php elseif ($illustration === 'blocked'): ?>
<svg class="error-illustration" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="100" cy="80" r="60" fill="var(--color-error-bg)" opacity="0.4"/>
    <circle cx="100" cy="80" r="40" stroke="var(--color-error)" stroke-width="3" fill="none"/>
    <line x1="72" y1="108" x2="128" y2="52" stroke="var(--color-error)" stroke-width="3" stroke-linecap="round"/>
</svg>
<?php elseif ($illustration === 'warning'): ?>
<svg class="error-illustration" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
    <circle cx="100" cy="80" r="60" fill="var(--color-warning-bg)" opacity="0.4"/>
    <path d="M100 40 L145 120 L55 120 Z" stroke="var(--color-warning)" stroke-width="2.5" fill="none" stroke-linejoin="round"/>
    <line x1="100" y1="65" x2="100" y2="92" stroke="var(--color-warning)" stroke-width="3" stroke-linecap="round"/>
    <circle cx="100" cy="105" r="3" fill="var(--color-warning)"/>
</svg>
<?php endif; ?>
