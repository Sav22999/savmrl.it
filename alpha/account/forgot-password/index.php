<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/header.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/meta.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/translations.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/auth.php");
    auth_start_session();
    if (auth_get_current_user()) { header('Location: /alpha/account/'); exit; }
    global $title_header;
    $lang = detectLanguage();
    ?>
    <title><?php echo t('forgot-password-title', $lang); ?> — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
</header>
<main class="main-centered">
    <div class="horizontal-center">
        <div class="auth-form-container">
            <h2 class="title-section brilors"><?php echo t('forgot-password-title', $lang); ?></h2>
            <div id="auth-message"></div>

            <div id="step-email">
                <p class="text-align-center"><?php echo t('forgot-password-desc', $lang); ?></p>
                <form class="auth-form" onsubmit="requestReset(event)">
                    <input type="email" id="email" class="input-link" placeholder="<?php echo t('email', $lang); ?>" required />
                    <input type="submit" class="button-link" value="<?php echo t('send-reset-code', $lang); ?>" />
                </form>
                <p class="text-align-center auth-link-text">
                    <a href="/alpha/account/login/"><?php echo t('login', $lang); ?></a>
                </p>
            </div>

            <div id="step-reset" class="hidden">
                <p class="text-align-center"><?php echo t('enter-reset-code', $lang); ?></p>
                <form class="auth-form" onsubmit="submitReset(event)">
                    <input type="text" id="otp-code" class="input-link otp-input" placeholder="······" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" required />
                    <input type="password" id="new-password" class="input-link" placeholder="<?php echo t('new-password-label', $lang); ?>" minlength="8" required />
                    <input type="password" id="confirm-password" class="input-link" placeholder="<?php echo t('confirm-new-password-label', $lang); ?>" minlength="8" required />
                    <input type="submit" class="button-link" value="<?php echo t('reset-password', $lang); ?>" />
                </form>
                <button type="button" class="otp-resend" id="forgot-resend-btn" onclick="resendForgotOtp()"><?php echo t('resend-code', $lang); ?></button>
            </div>

            <div id="step-done" class="hidden">
                <p class="text-align-center"><?php echo t('password-reset-success', $lang); ?></p>
                <div class="auth-form">
                    <a href="/alpha/account/login/" class="button-link" style="display:block;text-align:center;text-decoration:none;"><?php echo t('login', $lang); ?></a>
                </div>
            </div>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/footer.php"); ?>
</footer>

<script>
let tempToken = null;

function showMessage(text, isError) {
    const el = document.getElementById('auth-message');
    el.className = isError ? 'error-message' : 'info-message';
    el.textContent = text;
}

function requestReset(e) {
    e.preventDefault();
    const email = document.getElementById('email').value;

    fetch('/api/v2/auth/forgot-password/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'request', email}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code !== '200') {
            showMessage(data.description, true);
            return;
        }
        tempToken = data.data && data.data.temp_token ? data.data.temp_token : null;
        document.getElementById('step-email').classList.add('hidden');
        document.getElementById('step-reset').classList.remove('hidden');
        showMessage(<?php echo json_encode(t('reset-code-sent', $lang)); ?>, false);
    })
    .catch(() => showMessage(<?php echo json_encode(t('connection-error', $lang)); ?>, true));
}

function submitReset(e) {
    e.preventDefault();
    const code = document.getElementById('otp-code').value;
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;

    if (newPassword !== confirmPassword) {
        showMessage(<?php echo json_encode(t('passwords-mismatch', $lang)); ?>, true);
        return;
    }

    fetch('/api/v2/auth/forgot-password/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'reset', temp_token: tempToken, code, new_password: newPassword}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code !== '200') {
            showMessage(data.description, true);
            return;
        }
        document.getElementById('step-reset').classList.add('hidden');
        document.getElementById('step-done').classList.remove('hidden');
        document.getElementById('auth-message').className = '';
        document.getElementById('auth-message').textContent = '';
    })
    .catch(() => showMessage(<?php echo json_encode(t('connection-error', $lang)); ?>, true));
}

function resendForgotOtp() {
    if (!tempToken) return;
    var btn = document.getElementById('forgot-resend-btn');
    btn.disabled = true;
    btn.textContent = '...';

    fetch('/api/v2/user/resend-otp/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({temp_token: tempToken, purpose: 'password_reset'}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            tempToken = data.data.temp_token;
            showMessage(<?php echo json_encode(t('code-resent', $lang)); ?>, false);
        } else {
            showMessage(data.description, true);
        }
        btn.disabled = false;
        btn.textContent = <?php echo json_encode(t('resend-code', $lang)); ?>;
    })
    .catch(() => {
        showMessage(<?php echo json_encode(t('connection-error', $lang)); ?>, true);
        btn.disabled = false;
        btn.textContent = <?php echo json_encode(t('resend-code', $lang)); ?>;
    });
}
</script>
</body>
</html>
