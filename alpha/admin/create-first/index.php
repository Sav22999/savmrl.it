<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/header.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/meta.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/translations.php");

    global $title_header, $localhost_db, $username_db, $password_db, $database_savmrl;
    $lang = detectLanguage();

    $admin_exists = false;
    $c = new mysqli($localhost_db, $username_db, $password_db, $database_savmrl);
    if (!$c->connect_error) {
        $c->set_charset("utf8mb4");
        $r = $c->query("SELECT COUNT(*) AS cnt FROM `admins_savmrl`");
        if ($r && $row = $r->fetch_assoc()) {
            $admin_exists = (int)$row['cnt'] > 0;
        }
        $c->close();
    }
    ?>
    <title>Create Admin — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
</header>
<main class="main-centered">
    <div class="horizontal-center">
        <div class="auth-form-container">
            <?php if ($admin_exists): ?>
                <h2 class="title-section brilors">Admin already exists</h2>
                <p class="text-align-center">An administrator account has already been created.</p>
                <p class="text-align-center"><a href="/alpha/admin/login/" class="button-link" style="display:inline-block;margin-top:16px;text-decoration:none;">Go to login</a></p>
            <?php else: ?>
                <h2 class="title-section brilors">Create first admin</h2>
                <p class="text-align-center" style="color:#6b7a8d;margin-bottom:20px;">Create the first administrator account for savmrl.it</p>
                <div id="auth-message"></div>

                <form id="create-admin-form" class="auth-form" onsubmit="submitCreateAdmin(event)">
                    <input type="text" id="username" class="input-link" placeholder="Username" autocomplete="username" required minlength="2" />
                    <input type="email" id="email" class="input-link" placeholder="Email" autocomplete="email" required />
                    <input type="password" id="password" class="input-link" placeholder="Password (min. 8 characters)" autocomplete="new-password" required minlength="8" />
                    <input type="password" id="password-confirm" class="input-link" placeholder="Confirm password" autocomplete="new-password" required minlength="8" />
                    <input type="submit" class="button-link" value="Create admin" />
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/footer.php"); ?>
</footer>

<?php if (!$admin_exists): ?>
<script>
function showMessage(text, isError) {
    var el = document.getElementById('auth-message');
    el.className = isError ? 'error-message' : 'info-message';
    el.textContent = text;
}

function submitCreateAdmin(e) {
    e.preventDefault();
    var username = document.getElementById('username').value.trim();
    var email = document.getElementById('email').value.trim();
    var password = document.getElementById('password').value;
    var confirm = document.getElementById('password-confirm').value;

    if (password !== confirm) {
        showMessage('Passwords do not match', true);
        return;
    }

    if (password.length < 8) {
        showMessage('Password must be at least 8 characters', true);
        return;
    }

    fetch('/api/v2/admin/create-first/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({username: username, email: email, password: password}),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.code === '201') {
            showMessage('Admin account created! Redirecting to login...', false);
            setTimeout(function() { window.location.href = '/alpha/admin/login/'; }, 2000);
        } else {
            showMessage(data.description, true);
        }
    })
    .catch(function() { showMessage('Connection error', true); });
}
</script>
<?php endif; ?>
</body>
</html>
