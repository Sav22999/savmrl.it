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
    <title><?php echo t('signup', $lang); ?> — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
</header>
<main>
    <div class="horizontal-center">
        <div class="auth-form-container">
            <h2 class="title-section brilors"><?php echo t('signup', $lang); ?></h2>
            <div id="auth-message"></div>

            <form id="register-form" class="auth-form" onsubmit="submitRegister(event)">
                <input type="text" id="username" class="input-link" placeholder="<?php echo t('username', $lang); ?>" pattern="[a-zA-Z0-9_.\-]+" minlength="2" maxlength="100" required />
                <input type="email" id="email" class="input-link" placeholder="<?php echo t('email', $lang); ?>" required />
                <input type="password" id="password" class="input-link" placeholder="<?php echo t('password-min', $lang); ?>" minlength="8" required />
                <input type="password" id="password-confirm" class="input-link" placeholder="<?php echo t('confirm-password', $lang); ?>" minlength="8" required />
                <input type="submit" class="button-link" value="<?php echo t('signup', $lang); ?>" />
            </form>
            <p class="text-align-center auth-link-text">
                <?php echo t('has-account', $lang); ?> <a href="/alpha/account/login/"><?php echo t('login', $lang); ?></a>
            </p>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer-alpha.php"); ?>
</footer>

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

    const data = {
        username: document.getElementById('username').value,
        email: document.getElementById('email').value,
        password: password,
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
