<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header-alpha.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta-alpha.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/auth.php");
    auth_start_session();
    $user = auth_get_current_user();
    if (!$user) { header('Location: /account/login/'); exit; }
    global $title_header;

    $link_to_edit = isset($_GET['link']) ? htmlspecialchars($_GET['link']) : '';
    ?>
    <title>Settings — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/auth-header.php"); ?>
</header>
<main>
    <div class="horizontal-center">
        <div class="auth-form-container settings-container">
            <h2 class="title-section brilors">Account settings</h2>
            <div id="settings-message"></div>

            <?php if ($link_to_edit && $user['pro_features']): ?>
            <div class="settings-section">
                <h3>Edit link: <?php echo $link_to_edit; ?></h3>
                <form id="link-settings-form" class="auth-form" onsubmit="saveLinkSettings(event)">
                    <input type="hidden" id="link-name" value="<?php echo $link_to_edit; ?>" />
                    <label>Expiry date</label>
                    <input type="date" id="link-expiry" class="input-link" />
                    <label>Max openings</label>
                    <input type="number" id="link-openings" class="input-link" min="1" />
                    <label>New access code (leave empty to keep current)</label>
                    <input type="password" id="link-access-code" class="input-link" placeholder="New access code" />
                    <input type="submit" class="button-link" value="Save link settings" />
                </form>
            </div>
            <hr />
            <?php endif; ?>

            <div class="settings-section">
                <h3>Change password</h3>
                <form id="password-form" class="auth-form" onsubmit="changePassword(event)">
                    <input type="password" id="current-password" class="input-link" placeholder="Current password" required />
                    <input type="password" id="new-password" class="input-link" placeholder="New password (min 8 chars)" minlength="8" required />
                    <input type="password" id="confirm-password" class="input-link" placeholder="Confirm new password" minlength="8" required />
                    <input type="submit" class="button-link" value="Change password" />
                </form>
            </div>

            <div class="settings-section">
                <h3>Two-factor authentication</h3>
                <p>Status: <strong id="twofa-status"><?php echo $user['two_fa_enabled'] ? 'Enabled' : 'Disabled'; ?></strong></p>
                <?php if ($user['two_fa_enabled']): ?>
                    <button class="button-link" onclick="toggle2FA(false)">Disable 2FA</button>
                <?php else: ?>
                    <button class="button-link" onclick="toggle2FA(true)">Enable 2FA</button>
                <?php endif; ?>
                <div id="twofa-verify" class="hidden">
                    <p>Enter the code sent to your email:</p>
                    <form class="auth-form" onsubmit="verify2FAToggle(event)">
                        <input type="text" id="twofa-code" class="input-link" placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required />
                        <input type="submit" class="button-link" value="Confirm" />
                    </form>
                </div>
            </div>

            <div class="settings-section">
                <h3>Active sessions</h3>
                <div id="sessions-list">Loading...</div>
            </div>

            <div class="settings-section">
                <h3>Back</h3>
                <a href="/account/" class="button-link" style="display:inline-block;text-decoration:none;">Back to dashboard</a>
            </div>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

<script>
const currentSessionId = '<?php echo htmlspecialchars($current_session_id ?? ''); ?>';
let twofaTempToken = null;
let twofaEnabling = true;

function showMsg(text, isError) {
    const el = document.getElementById('settings-message');
    el.className = isError ? 'error-message' : 'info-message';
    el.textContent = text;
    setTimeout(() => { el.textContent = ''; el.className = ''; }, 5000);
}

function changePassword(e) {
    e.preventDefault();
    const curr = document.getElementById('current-password').value;
    const newP = document.getElementById('new-password').value;
    const conf = document.getElementById('confirm-password').value;
    if (newP !== conf) { showMsg('Passwords do not match', true); return; }

    fetch('/api/v2/user/change-password/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({current_password: curr, new_password: newP}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            showMsg('Password changed. You will be logged out.', false);
            setTimeout(() => window.location.href = '/account/login/', 2000);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg('Connection error', true));
}

function toggle2FA(enable) {
    twofaEnabling = enable;
    fetch('/api/v2/user/toggle-2fa/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({enable}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200' && data.data && data.data.temp_token) {
            twofaTempToken = data.data.temp_token;
            document.getElementById('twofa-verify').classList.remove('hidden');
            showMsg('Verification code sent to your email', false);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg('Connection error', true));
}

function verify2FAToggle(e) {
    e.preventDefault();
    const code = document.getElementById('twofa-code').value;

    fetch('/api/v2/user/confirm-2fa/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({temp_token: twofaTempToken, code, enable: twofaEnabling}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            showMsg('2FA ' + (twofaEnabling ? 'enabled' : 'disabled') + ' successfully', false);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg('Connection error', true));
}

function saveLinkSettings(e) {
    e.preventDefault();
    const name = document.getElementById('link-name').value;
    const data = {name};
    const expiry = document.getElementById('link-expiry').value;
    const openings = document.getElementById('link-openings').value;
    const access_code = document.getElementById('link-access-code').value;
    if (expiry) data.expiry_date = expiry;
    if (openings) data.openings_limit = parseInt(openings);
    if (access_code) data.access_code = access_code;

    fetch('/api/v2/user/links/update/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(result => {
        if (result.code === '200') {
            showMsg('Link settings updated', false);
        } else {
            showMsg(result.description, true);
        }
    })
    .catch(() => showMsg('Connection error', true));
}

// Load sessions
fetch('/api/v2/user/sessions/')
    .then(r => r.json())
    .then(data => {
        if (data.code !== '200') return;
        const container = document.getElementById('sessions-list');
        let html = '';
        data.data.sessions.forEach(s => {
            const isCurrent = s.id.substring(0, 8) === currentSessionId.substring(0, 8);
            html += '<div class="session-item">'
                + '<span>' + (s.user_agent || 'Unknown device').substring(0, 50) + '</span>'
                + '<span>' + s.ip_address + '</span>'
                + '<span>' + new Date(s.last_activity).toLocaleString() + '</span>'
                + (isCurrent ? '<span class="badge badge-active">Current</span>' : '<button onclick="revokeSession(\'' + s.id + '\')">Revoke</button>')
                + '</div>';
        });
        container.innerHTML = html || '<p>No active sessions</p>';
    })
    .catch(() => {});

function revokeSession(id) {
    fetch('/api/v2/user/sessions/revoke/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({session_id: id}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            showMsg('Session revoked', false);
            window.location.reload();
        } else {
            showMsg(data.description, true);
        }
    });
}
</script>
</body>
</html>
