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
    <title><?php echo t('signup', $lang); ?> — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
</header>
<main class="main-centered">
    <div class="horizontal-center">
        <div class="auth-form-container">
            <h2 class="title-section brilors"><?php echo t('signup', $lang); ?></h2>
            <div id="auth-message"></div>

            <form id="register-form" class="auth-form" onsubmit="submitRegister(event)">
                <input type="text" id="username" class="input-link" placeholder="<?php echo t('username', $lang); ?>" pattern="[a-zA-Z0-9_.\-]+" minlength="2" maxlength="100" required />
                <input type="email" id="email" class="input-link" placeholder="<?php echo t('email', $lang); ?>" required />
                <input type="password" id="password" class="input-link" placeholder="<?php echo t('password-min', $lang); ?>" minlength="8" required />
                <input type="password" id="password-confirm" class="input-link" placeholder="<?php echo t('confirm-password', $lang); ?>" minlength="8" required />
                <div class="terms-checkbox-row">
                    <input type="checkbox" id="accept_terms" required />
                    <label for="accept_terms"><?php echo t('accept-terms', $lang); ?></label>
                </div>
                <altcha-widget challengeurl="/api/v2/altcha/challenge/" strings='<?php echo htmlspecialchars(json_encode([
                    'label' => t('altcha-label', $lang),
                    'verifying' => t('altcha-verifying', $lang),
                    'verified' => t('altcha-verified', $lang),
                    'error' => t('altcha-error', $lang),
                ])); ?>'></altcha-widget>
                <input type="submit" class="button-link" value="<?php echo t('signup', $lang); ?>" />
            </form>
            <p class="text-align-center auth-link-text">
                <?php echo t('has-account', $lang); ?> <a href="/alpha/account/login/"><?php echo t('login', $lang); ?></a>
            </p>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/footer.php"); ?>
</footer>

<script async defer src="https://cdn.jsdelivr.net/npm/altcha/dist/altcha.min.js" type="module"></script>
<script>
function showMessage(text, isError) {
    const el = document.getElementById('auth-message');
    el.className = isError ? 'error-message' : 'info-message';
    el.textContent = text;
}

function submitRegister(e) {
    e.preventDefault();
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password-confirm').value;

    if (password !== confirm) {
        showMessage(<?php echo json_encode(t('passwords-mismatch', $lang)); ?>, true);
        return;
    }

    if (!document.getElementById('accept_terms').checked) {
        showMessage(<?php echo json_encode(t('must-accept-terms', $lang)); ?>, true);
        return;
    }

    const altchaWidget = document.querySelector('altcha-widget');
    const altchaValue = altchaWidget ? altchaWidget.value : '';
    if (!altchaValue) {
        showMessage(<?php echo json_encode(t('captcha-required', $lang)); ?>, true);
        return;
    }

    const data = {
        username: document.getElementById('username').value,
        email: document.getElementById('email').value,
        password: password,
        terms_accepted: true,
        altcha: altchaValue,
    };

    fetch('/api/v2/auth/register/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(result => {
        if (result.code === '201') {
            showMessage(<?php echo json_encode(t('account-created', $lang)); ?>, false);
            document.getElementById('register-form').reset();
        } else {
            showMessage(result.description, true);
        }
    })
    .catch(() => showMessage(<?php echo json_encode(t('connection-error', $lang)); ?>, true));
}
</script>
</body>
</html>
