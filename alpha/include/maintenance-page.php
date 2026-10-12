<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>savmrl.it — Maintenance</title>
    <link rel="icon" type="image/svg+xml" href="/alpha/images/icon.svg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
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
            max-width: 460px;
        }
        .illustration {
            margin-bottom: 32px;
            animation: fadeInUp 0.6s ease-out;
        }
        .illustration svg {
            width: 120px;
            height: 120px;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .gear { animation: spin 60s linear infinite; transform-origin: 60px 60px; }
        h1 {
            font-size: 1.8em;
            font-weight: 600;
            color: #00A7AA;
            margin-bottom: 16px;
            animation: fadeInUp 0.6s ease-out 0.15s both;
        }
        p {
            font-size: 1.05em;
            line-height: 1.6;
            color: #636e72;
            margin-bottom: 40px;
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #00A7AA;
            font-weight: 600;
            font-size: 1em;
            animation: fadeInUp 0.6s ease-out 0.45s both;
        }
        .brand img {
            width: 28px;
            height: 28px;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="illustration">
            <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                <g class="gear">
                    <path d="M60 15 L65 25 L75 20 L72 32 L83 33 L76 42 L86 48 L75 52 L80 63 L68 60 L67 72 L60 63 L53 72 L52 60 L40 63 L45 52 L34 48 L44 42 L37 33 L48 32 L45 20 L55 25 Z" fill="none" stroke="#00A7AA" stroke-width="2.5" stroke-linejoin="round"/>
                    <circle cx="60" cy="43" r="12" fill="none" stroke="#00A7AA" stroke-width="2.5"/>
                </g>
                <circle cx="60" cy="95" r="4" fill="#00A7AA" opacity="0.5">
                    <animate attributeName="opacity" values="0.5;1;0.5" dur="2s" repeatCount="indefinite"/>
                </circle>
                <circle cx="45" cy="100" r="2.5" fill="#00A7AA" opacity="0.3">
                    <animate attributeName="opacity" values="0.3;0.7;0.3" dur="2s" begin="0.3s" repeatCount="indefinite"/>
                </circle>
                <circle cx="75" cy="100" r="2.5" fill="#00A7AA" opacity="0.3">
                    <animate attributeName="opacity" values="0.3;0.7;0.3" dur="2s" begin="0.6s" repeatCount="indefinite"/>
                </circle>
            </svg>
        </div>
        <h1>Be right back</h1>
        <p>We're doing a little maintenance.<br>The site will be back shortly — try again soon.</p>
        <a href="/" class="brand">
            <img src="/alpha/images/icon.svg" alt="savmrl.it">
            savmrl.it
        </a>
    </div>
</body>
</html>
