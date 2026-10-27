<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/include/meta.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/include/translations.php"); ?>

    <?php
    global $title_header;
    $lang = detectLanguage();
    $title = "savmrl.it - Changelog";
    ?>
    <title><?php echo $title; ?></title>
    <style>
        .changelog { max-width: 720px; margin: 0 auto; padding: 0 16px; }
        .changelog-entry { margin-bottom: 32px; position: relative; padding-left: 24px; border-left: 2px solid var(--color-border); }
        .changelog-entry:last-child { margin-bottom: 0; }
        .changelog-dot { position: absolute; left: -7px; top: 6px; width: 12px; height: 12px; border-radius: 50%; background: var(--color-primary); }
        .changelog-entry:first-child .changelog-dot { width: 14px; height: 14px; left: -8px; top: 5px; box-shadow: 0 0 0 3px rgba(0, 150, 136, 0.2); }
        .changelog-date { font-size: 0.8em; color: var(--color-text-secondary); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .changelog-build { font-size: 0.75em; color: var(--color-text-secondary); font-family: monospace; margin-left: 8px; }
        .changelog-title { font-size: 1.1em; font-weight: 600; margin: 4px 0 8px; color: var(--color-text); }
        .changelog-list { list-style: none; padding: 0; margin: 0; }
        .changelog-list li { padding: 3px 0; font-size: 0.9em; color: var(--color-text-secondary); position: relative; padding-left: 16px; }
        .changelog-list li::before { content: ''; position: absolute; left: 0; top: 10px; width: 6px; height: 6px; border-radius: 50%; background: var(--color-border); }
        .changelog-tag { display: inline-block; font-size: 0.7em; font-weight: 600; padding: 2px 8px; border-radius: 10px; text-transform: uppercase; letter-spacing: 0.3px; margin-right: 4px; vertical-align: middle; }
        .tag-new { background: rgba(76, 175, 80, 0.15); color: #388e3c; }
        .tag-fix { background: rgba(244, 67, 54, 0.15); color: #d32f2f; }
        .tag-improve { background: rgba(33, 150, 243, 0.15); color: #1976d2; }
        .tag-security { background: rgba(255, 152, 0, 0.15); color: #f57c00; }
    </style>
</head>
<body>

<header>
    <?php echo $title_header; ?>
</header>
<main>
    <div class="horizontal-center">
        <h2 class="title-section brilors">Changelog</h2>
        <div class="big-space"></div>

        <div class="changelog">

            <div class="changelog-entry">
                <div class="changelog-dot"></div>
                <div class="changelog-date">27 October 2026 <span class="changelog-build">build 20261027</span></div>
                <div class="changelog-title">Access code encryption fix & admin improvements</div>
                <ul class="changelog-list">
                    <li><span class="changelog-tag tag-fix">Fix</span> Fixed critical bug where adding an access code via "edit link" did not encrypt the destination URL</li>
                    <li><span class="changelog-tag tag-fix">Fix</span> Access code form now POSTs directly to the short link URL instead of using query parameters</li>
                    <li><span class="changelog-tag tag-fix">Fix</span> Decryption failures now show a specific error page instead of "Invalid link"</li>
                    <li><span class="changelog-tag tag-fix">Fix</span> Edit link modal errors are now displayed inside the popup instead of behind it</li>
                    <li><span class="changelog-tag tag-improve">Improve</span> Redesigned access code management UI with toggle panels for change/remove/add</li>
                    <li><span class="changelog-tag tag-improve">Improve</span> Admin link search now supports searching by owner email</li>
                    <li><span class="changelog-tag tag-improve">Improve</span> Search results highlight matching text in admin panel</li>
                    <li><span class="changelog-tag tag-new">New</span> Changelog page</li>
                    <li><span class="changelog-tag tag-security">Security</span> Changed <code>redirect_link</code> column from VARCHAR(255) to TEXT to prevent encrypted data truncation</li>
                </ul>
            </div>

            <div class="changelog-entry">
                <div class="changelog-dot"></div>
                <div class="changelog-date">12 October 2026 <span class="changelog-build">build 20261012</span></div>
                <div class="changelog-title">New portal launched</div>
                <ul class="changelog-list">
                    <li><span class="changelog-tag tag-new">New</span> Completely redesigned website with new UI/UX</li>
                    <li><span class="changelog-tag tag-new">New</span> User accounts with dashboard, link management and statistics</li>
                    <li><span class="changelog-tag tag-new">New</span> Admin panel with link moderation and user management</li>
                    <li><span class="changelog-tag tag-new">New</span> Link reporting system (phishing, spam, illegal, other)</li>
                    <li><span class="changelog-tag tag-new">New</span> Two-factor authentication via email</li>
                    <li><span class="changelog-tag tag-new">New</span> Access code protection with AES-256-GCM encryption</li>
                    <li><span class="changelog-tag tag-new">New</span> Redirect delay with countdown timer</li>
                    <li><span class="changelog-tag tag-new">New</span> Multi-language support (IT, EN, FR, DE, ES)</li>
                    <li><span class="changelog-tag tag-new">New</span> API v2</li>
                    <li><span class="changelog-tag tag-new">New</span> ALTCHA captcha integration</li>
                </ul>
            </div>

            <div class="changelog-entry">
                <div class="changelog-dot"></div>
                <div class="changelog-date">3 September 2025 <span class="changelog-build">v2.0</span></div>
                <div class="changelog-title">Version 2.0</div>
                <ul class="changelog-list">
                    <li><span class="changelog-tag tag-new">New</span> Alpha version of the new portal</li>
                    <li><span class="changelog-tag tag-improve">Improve</span> New UI/UX design</li>
                    <li><span class="changelog-tag tag-improve">Improve</span> Improved header and navigation</li>
                </ul>
            </div>

            <div class="changelog-entry">
                <div class="changelog-dot"></div>
                <div class="changelog-date">31 January 2024</div>
                <div class="changelog-title">Custom links & improvements</div>
                <ul class="changelog-list">
                    <li><span class="changelog-tag tag-new">New</span> Custom short link names</li>
                    <li><span class="changelog-tag tag-improve">Improve</span> Improved redirect flow via <code>/r/</code></li>
                    <li><span class="changelog-tag tag-fix">Fix</span> Various bug fixes and UI improvements</li>
                </ul>
            </div>

        </div>

        <div class="big-space"></div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php"); ?>
</footer>

</body>
</html>
