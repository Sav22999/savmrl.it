<?php
http_response_code(410);
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>savmrl.it — Link temporarily suspended</title>
    <link rel="icon" type="image/svg+xml" href="/savmrl/images/icon.svg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6f7;
            color: #2d3436;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .container {
            text-align: center;
            max-width: 520px;
        }
        .icon {
            margin-bottom: 24px;
        }
        .icon svg {
            width: 80px;
            height: 80px;
        }
        h1 {
            font-size: 1.5em;
            font-weight: 700;
            color: #d63031;
            margin-bottom: 16px;
        }
        .subtitle {
            font-size: 1em;
            line-height: 1.7;
            color: #636e72;
            margin-bottom: 24px;
        }
        .legal {
            font-size: 0.85em;
            line-height: 1.6;
            color: #b2bec3;
            border-top: 1px solid #dfe6e9;
            padding-top: 20px;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #b2bec3;
            font-weight: 600;
            font-size: 0.9em;
            margin-top: 24px;
        }
        .brand img {
            width: 24px;
            height: 24px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">
                <circle cx="40" cy="40" r="35" fill="none" stroke="#d63031" stroke-width="2.5" opacity="0.2"/>
                <line x1="22" y1="22" x2="58" y2="58" stroke="#d63031" stroke-width="2.5" stroke-linecap="round"/>
                <line x1="58" y1="22" x2="22" y2="58" stroke="#d63031" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
        </div>
        <h1>Link temporarily suspended</h1>
        <p class="subtitle">
            This link is temporarily unavailable.<br>
            The savmrl.it service is temporarily suspended for maintenance. It will be restored soon.
        </p>
        <div class="legal">
            For inquiries: <a href="https://saveriomorelli.com/contact-me" style="color:#636e72;font-weight:600;">saveriomorelli.com/contact-me</a>
        </div>
        <span class="brand">
            <img src="/savmrl/images/icon.svg" alt="">
            savmrl.it
        </span>
    </div>
</body>
</html>
