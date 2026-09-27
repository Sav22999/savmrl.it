<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header-alpha.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta-alpha.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/auth.php");
    auth_start_session();
    if (auth_get_current_user()) { header('Location: /account/'); exit; }
    global $title_header;
    ?>
    <title>Login — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
</header>
<main>
    <div class="horizontal-center">
        <div class="auth-form-container">
            <h2 class="title-section brilors">Login</h2>
            <div id="auth-message"></div>

            <div id="login-form-container">
                <form id="login-form" class="auth-form" onsubmit="submitLogin(event)">
                    <input type="email" id="email" class="input-link" placeholder="Email" required />
                    <input type="password" id="password" class="input-link" placeholder="Password" required />
                    <input type="submit" class="button-link" value="Login" />
                </form>
                <p class="text-align-center auth-link-text">
                    Don't have an account? <a href="/account/register/">Sign up</a>
                </p>
            </div>

            <div id="twofa-form-container" class="hidden">
                <p class="text-align-center">Enter the 6-digit code sent to your email.</p>
                <form id="twofa-form" class="auth-form" onsubmit="submitTwoFa(event)">
                    <input type="text" id="otp-code" class="input-link" placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" required />
                    <input type="submit" class="button-link" value="Verify" />
                </form>
            </div>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

<script>
let tempToken = null;

function showMessage(text, isError) {
    const el = document.getElementById('auth-message');
    el.className = isError ? 'error-message' : 'info-message';
    el.textContent = text;
}

function submitLogin(e) {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    fetch('/api/v2/auth/login/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({email, password}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code !== '200') {
            showMessage(data.description, true);
            return;
        }
        if (data.data && data.data.requires_2fa) {
            tempToken = data.data.temp_token;
            document.getElementById('login-form-container').classList.add('hidden');
            document.getElementById('twofa-form-container').classList.remove('hidden');
            showMessage('Verification code sent to your email', false);
            return;
        }
        if (data.data && data.data.user && data.data.user.preferred_language) {
            document.cookie = 'savmrl_lang=' + data.data.user.preferred_language + ';path=/;max-age=' + (365*86400) + ';SameSite=Lax';
        }
        window.location.href = '/account/';
    })
    .catch(() => showMessage('Connection error', true));
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
        window.location.href = '/account/';
    })
    .catch(() => showMessage('Connection error', true));
}
</script>
</body>
</html>
