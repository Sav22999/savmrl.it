<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header-alpha.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta-alpha.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/translations.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/auth.php");
    auth_start_session();
    if (auth_get_current_user()) { header('Location: /alpha/account/'); exit; }
    global $title_header;
    $lang = detectLanguage();
    ?>
    <title><?php echo t('login', $lang); ?> — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
</header>
<main>
    <div class="horizontal-center">
        <div class="auth-form-container">
            <h2 class="title-section brilors"><?php echo t('login', $lang); ?></h2>
            <div id="auth-message"></div>

            <div id="login-form-container">
                <form id="login-form" class="auth-form" onsubmit="submitLogin(event)">
                    <input type="email" id="email" class="input-link" placeholder="<?php echo t('email', $lang); ?>" required />
                    <input type="password" id="password" class="input-link" placeholder="<?php echo t('password', $lang); ?>" required />
                    <input type="submit" class="button-link" value="<?php echo t('login', $lang); ?>" />
                </form>
                <p class="text-align-center auth-link-text">
                    <?php echo t('no-account', $lang); ?> <a href="/alpha/account/register/"><?php echo t('signup', $lang); ?></a>
                </p>
            </div>

            <div id="verify-email-container" class="hidden">
                <p class="text-align-center"><?php echo t('verify-email-required-desc', $lang); ?></p>
                <div class="auth-form">
                    <button class="button-link" onclick="resendVerification()"><?php echo t('resend-verification', $lang); ?></button>
                </div>
                <p class="text-align-center auth-link-text" style="margin-top:12px;">
                    <a href="/alpha/account/verify/"><?php echo t('verify', $lang); ?></a>
                </p>
            </div>

            <div id="twofa-form-container" class="hidden">
                <p class="text-align-center"><?php echo t('enter-2fa', $lang); ?></p>
                <form id="twofa-form" class="auth-form" onsubmit="submitTwoFa(event)">
                    <input type="text" id="otp-code" class="input-link" placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" required />
                    <input type="submit" class="button-link" value="<?php echo t('verify', $lang); ?>" />
                </form>
            </div>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer-alpha.php"); ?>
</footer>

<script>
let tempToken = null;

function showMessage(text, isError) {
    const el = document.getElementById('auth-message');
    el.className = isError ? 'error-message' : 'info-message';
    el.textContent = text;
}

let loginEmail = null;

function submitLogin(e) {
    e.preventDefault();
    loginEmail = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    fetch('/api/v2/auth/login/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({email: loginEmail, password}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.data && data.data.email_not_verified) {
            document.getElementById('login-form-container').classList.add('hidden');
            document.getElementById('verify-email-container').classList.remove('hidden');
            showMessage(<?php echo json_encode(t('email-not-verified', $lang)); ?>, true);
            return;
        }
        if (data.code !== '200') {
            showMessage(data.description, true);
            return;
        }
        if (data.data && data.data.requires_2fa) {
            tempToken = data.data.temp_token;
            document.getElementById('login-form-container').classList.add('hidden');
            document.getElementById('twofa-form-container').classList.remove('hidden');
            showMessage(<?php echo json_encode(t('code-sent', $lang)); ?>, false);
            return;
        }
        if (data.data && data.data.user && data.data.user.preferred_language) {
            document.cookie = 'savmrl_lang=' + data.data.user.preferred_language + ';path=/;max-age=' + (365*86400) + ';SameSite=Lax';
        }
        window.location.href = '/alpha/account/';
    })
    .catch(() => showMessage(<?php echo json_encode(t('connection-error', $lang)); ?>, true));
}

function resendVerification() {
    if (!loginEmail) return;
    fetch('/api/v2/auth/resend-verification/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({email: loginEmail}),
    })
    .then(r => r.json())
    .then(() => showMessage(<?php echo json_encode(t('verification-resent', $lang)); ?>, false))
    .catch(() => showMessage(<?php echo json_encode(t('connection-error', $lang)); ?>, true));
}

function submitTwoFa(e) {
    e.preventDefault();
    const code = document.getElementById('otp-code').value;

    fetch('/api/v2/auth/verify-2fa/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({temp_token: tempToken, code}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code !== '200') {
            showMessage(data.description, true);
            return;
        }
        if (data.data && data.data.user && data.data.user.preferred_language) {
            document.cookie = 'savmrl_lang=' + data.data.user.preferred_language + ';path=/;max-age=' + (365*86400) + ';SameSite=Lax';
        }
        window.location.href = '/alpha/account/';
    })
    .catch(() => showMessage(<?php echo json_encode(t('connection-error', $lang)); ?>, true));
}
</script>
</body>
</html>
