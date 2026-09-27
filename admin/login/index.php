<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header-alpha.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta-alpha.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/admin-auth.php");
    admin_auth_start_session();
    if (admin_get_current()) { header('Location: /admin/'); exit; }
    global $title_header;
    ?>
    <title>Admin Login — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
</header>
<main>
    <div class="horizontal-center">
        <div class="auth-form-container">
            <h2 class="title-section brilors">Admin Login</h2>
            <div id="auth-message"></div>

            <form id="admin-login-form" class="auth-form" onsubmit="submitAdminLogin(event)">
                <input type="text" id="username" class="input-link" placeholder="Username" autocomplete="username" required />
                <input type="password" id="password" class="input-link" placeholder="Password" autocomplete="current-password" required />
                <input type="submit" class="button-link" value="Login" />
            </form>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

<script>
function showMessage(text, isError) {
    const el = document.getElementById('auth-message');
    el.className = isError ? 'error-message' : 'info-message';
    el.textContent = text;
}

function submitAdminLogin(e) {
    e.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    fetch('/api/v2/admin/login/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({username, password}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code !== '200') {
            showMessage(data.description, true);
            return;
        }
        window.location.href = '/admin/';
    })
    .catch(() => showMessage('Connection error', true));
}
</script>
</body>
</html>
