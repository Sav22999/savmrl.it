<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header-alpha.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta-alpha.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/translations.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/auth.php");
    auth_start_session();
    $user = auth_get_current_user();
    if (!$user) { header('Location: /alpha/account/login/'); exit; }
    global $title_header, $current_session_id;
    $lang = detectLanguage();

    $link_to_edit = isset($_GET['link']) ? htmlspecialchars($_GET['link']) : '';
    ?>
    <title><?php echo t('account-settings', $lang); ?> — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/auth-header-alpha.php"); ?>
</header>
<main>
    <div class="horizontal-center">
        <div class="auth-form-container settings-container">
            <h2 class="title-section brilors"><?php echo t('account-settings', $lang); ?></h2>
            <div id="settings-message"></div>

            <?php if ($link_to_edit && $user['pro_features']): ?>
            <div class="settings-section">
                <h3><?php echo t('edit-link', $lang); ?> <?php echo $link_to_edit; ?></h3>
                <form id="link-settings-form" class="auth-form" onsubmit="saveLinkSettings(event)">
                    <input type="hidden" id="link-name" value="<?php echo $link_to_edit; ?>" />
                    <label><?php echo t('destination-url', $lang); ?></label>
                    <input type="url" id="link-destination" class="input-link" placeholder="https://example.com" />
                    <p class="input-hint" id="destination-hint" style="display:none;"><?php echo t('destination-access-code-hint', $lang); ?></p>
                    <label><?php echo t('expiry-date', $lang); ?></label>
                    <input type="date" id="link-expiry" class="input-link" />
                    <label><?php echo t('max-openings', $lang); ?></label>
                    <input type="number" id="link-openings" class="input-link" min="1" />
                    <label><?php echo t('new-access-code', $lang); ?></label>
                    <input type="password" id="link-access-code" class="input-link" placeholder="<?php echo t('new-access-code', $lang); ?>" />
                    <input type="submit" class="button-link" value="<?php echo t('save-link-settings', $lang); ?>" />
                </form>
            </div>
            <hr />
            <?php endif; ?>

            <div class="settings-section">
                <h3><?php echo t('preferred-language', $lang); ?></h3>
                <div class="language-selector">
                    <select id="language-select" onchange="saveLanguage(this.value)">
                        <option value="en" <?php if ($user['preferred_language'] === 'en') echo 'selected'; ?>>English</option>
                        <option value="it" <?php if ($user['preferred_language'] === 'it') echo 'selected'; ?>>Italiano</option>
                        <option value="fr" <?php if ($user['preferred_language'] === 'fr') echo 'selected'; ?>>Français</option>
                        <option value="de" <?php if ($user['preferred_language'] === 'de') echo 'selected'; ?>>Deutsch</option>
                        <option value="es" <?php if ($user['preferred_language'] === 'es') echo 'selected'; ?>>Español</option>
                    </select>
                </div>
            </div>

            <div class="settings-section">
                <h3><?php echo t('change-password', $lang); ?></h3>
                <div id="password-step1">
                    <form id="password-form" class="auth-form" onsubmit="requestPasswordChange(event)">
                        <input type="password" id="current-password" class="input-link" placeholder="<?php echo t('current-password', $lang); ?>" required />
                        <input type="password" id="new-password" class="input-link" placeholder="<?php echo t('new-password', $lang); ?>" minlength="8" required />
                        <input type="password" id="confirm-password" class="input-link" placeholder="<?php echo t('confirm-new-password', $lang); ?>" minlength="8" required />
                        <input type="submit" class="button-link" value="<?php echo t('change-password', $lang); ?>" />
                    </form>
                </div>
                <div id="password-step2" class="hidden">
                    <p><?php echo t('verify-code-to-change-password', $lang); ?></p>
                    <form class="auth-form" onsubmit="confirmPasswordChange(event)">
                        <input type="text" id="pw-otp-code" class="input-link" placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" required />
                        <input type="submit" class="button-link" value="<?php echo t('confirm', $lang); ?>" />
                    </form>
                </div>
            </div>

            <div class="settings-section">
                <h3><?php echo t('two-factor-auth', $lang); ?></h3>
                <p><?php echo t('status', $lang); ?>: <strong id="twofa-status"><?php echo $user['two_fa_enabled'] ? t('enabled', $lang) : t('disabled', $lang); ?></strong></p>
                <?php if ($user['two_fa_enabled']): ?>
                    <button class="button-link" onclick="toggle2FA(false)"><?php echo t('disable-2fa', $lang); ?></button>
                <?php else: ?>
                    <button class="button-link" onclick="toggle2FA(true)"><?php echo t('enable-2fa', $lang); ?></button>
                <?php endif; ?>
                <div id="twofa-verify" class="hidden">
                    <p><?php echo t('enter-code', $lang); ?></p>
                    <form class="auth-form" onsubmit="verify2FAToggle(event)">
                        <input type="text" id="twofa-code" class="input-link" placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required />
                        <input type="submit" class="button-link" value="<?php echo t('confirm', $lang); ?>" />
                    </form>
                </div>
            </div>

            <div class="settings-section">
                <h3><?php echo t('active-sessions', $lang); ?></h3>
                <div id="sessions-list"><?php echo t('loading', $lang); ?></div>
            </div>

            <div class="settings-section">
                <h3 style="color:var(--color-error);"><?php echo t('delete-account', $lang); ?></h3>
                <p style="font-size:0.9em;color:var(--color-text-secondary);"><?php echo t('delete-account-desc', $lang); ?></p>
                <div id="delete-step1">
                    <form class="auth-form" onsubmit="requestDeleteAccount(event)">
                        <input type="password" id="delete-password" class="input-link" placeholder="<?php echo t('password', $lang); ?>" required />
                        <input type="submit" class="btn btn-danger btn-block" value="<?php echo t('confirm-delete-account', $lang); ?>" />
                    </form>
                </div>
                <div id="delete-step2" class="hidden">
                    <p><?php echo t('enter-code', $lang); ?></p>
                    <form class="auth-form" onsubmit="confirmDeleteAccount(event)">
                        <input type="text" id="del-otp-code" class="input-link" placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" required />
                        <input type="submit" class="btn btn-danger btn-block" value="<?php echo t('confirm', $lang); ?>" />
                    </form>
                </div>
            </div>

            <div class="settings-section">
                <a href="/alpha/account/" class="button-link" style="display:inline-block;text-decoration:none;"><?php echo t('back-to-dashboard', $lang); ?></a>
            </div>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer-alpha.php"); ?>
</footer>

<script>
const currentSessionId = '<?php echo htmlspecialchars($current_session_id ?? ''); ?>';
let twofaTempToken = null;
let twofaEnabling = true;
const connErr = <?php echo json_encode(t('connection-error', $lang)); ?>;
const linkToEdit = <?php echo json_encode($link_to_edit); ?>;

if (linkToEdit) {
    fetch('/api/v2/user/links/?per_page=50')
        .then(r => r.json())
        .then(data => {
            if (data.code !== '200') return;
            const link = data.data.links.find(l => l.name === linkToEdit);
            if (!link) return;
            if (!link.has_access_code && link.original_url) {
                document.getElementById('link-destination').value = link.original_url;
            }
            if (link.has_access_code) {
                document.getElementById('destination-hint').style.display = '';
                document.getElementById('link-destination').placeholder = <?php echo json_encode(t('destination-encrypted', $lang)); ?>;
            }
            if (link.expiry_date) {
                document.getElementById('link-expiry').value = link.expiry_date;
            }
            if (link.openings_limit) {
                document.getElementById('link-openings').value = link.openings_limit;
            }
        })
        .catch(() => {});
}

function showMsg(text, isError) {
    const el = document.getElementById('settings-message');
    el.className = isError ? 'error-message' : 'info-message';
    el.textContent = text;
    setTimeout(() => { el.textContent = ''; el.className = ''; }, 5000);
}

function saveLanguage(lang) {
    fetch('/api/v2/user/language/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({language: lang}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            document.cookie = 'savmrl_lang=' + lang + ';path=/;max-age=' + (365*86400) + ';SameSite=Lax';
            showMsg(<?php echo json_encode(t('language-saved', $lang)); ?>, false);
            setTimeout(() => window.location.reload(), 800);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg(connErr, true));
}

let pwTempToken = null;
let pwNewPassword = null;

function requestPasswordChange(e) {
    e.preventDefault();
    const curr = document.getElementById('current-password').value;
    const newP = document.getElementById('new-password').value;
    const conf = document.getElementById('confirm-password').value;
    if (newP !== conf) { showMsg(<?php echo json_encode(t('passwords-mismatch', $lang)); ?>, true); return; }

    pwNewPassword = newP;

    fetch('/api/v2/user/change-password/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({step: 'request', current_password: curr, new_password: newP}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200' && data.data && data.data.requires_verification) {
            pwTempToken = data.data.temp_token;
            document.getElementById('password-step1').classList.add('hidden');
            document.getElementById('password-step2').classList.remove('hidden');
            showMsg(<?php echo json_encode(t('password-change-code-sent', $lang)); ?>, false);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg(connErr, true));
}

function confirmPasswordChange(e) {
    e.preventDefault();
    const code = document.getElementById('pw-otp-code').value;

    fetch('/api/v2/user/change-password/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({step: 'confirm', temp_token: pwTempToken, code: code, new_password: pwNewPassword}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            showMsg(<?php echo json_encode(t('password-changed', $lang)); ?>, false);
            setTimeout(() => window.location.href = '/alpha/account/login/', 2000);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg(connErr, true));
}

let delTempToken = null;

function requestDeleteAccount(e) {
    e.preventDefault();
    const password = document.getElementById('delete-password').value;

    fetch('/api/v2/user/delete/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({step: 'request', password: password}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200' && data.data && data.data.requires_verification) {
            delTempToken = data.data.temp_token;
            document.getElementById('delete-step1').classList.add('hidden');
            document.getElementById('delete-step2').classList.remove('hidden');
            showMsg(<?php echo json_encode(t('code-sent', $lang)); ?>, false);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg(connErr, true));
}

function confirmDeleteAccount(e) {
    e.preventDefault();
    const code = document.getElementById('del-otp-code').value;

    fetch('/api/v2/user/delete/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({step: 'confirm', temp_token: delTempToken, code: code}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            showMsg(<?php echo json_encode(t('account-deleted', $lang)); ?>, false);
            setTimeout(() => window.location.href = '/alpha/', 2000);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg(connErr, true));
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
            showMsg(<?php echo json_encode(t('code-sent', $lang)); ?>, false);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg(connErr, true));
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
            showMsg(twofaEnabling ? <?php echo json_encode(t('2fa-enabled', $lang)); ?> : <?php echo json_encode(t('2fa-disabled', $lang)); ?>, false);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg(connErr, true));
}

function saveLinkSettings(e) {
    e.preventDefault();
    const name = document.getElementById('link-name').value;
    const data = {name};
    const destination = document.getElementById('link-destination').value;
    const expiry = document.getElementById('link-expiry').value;
    const openings = document.getElementById('link-openings').value;
    const access_code = document.getElementById('link-access-code').value;
    if (destination) data.destination_url = destination;
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
        if (result.code === '200') showMsg(<?php echo json_encode(t('link-settings-updated', $lang)); ?>, false);
        else showMsg(result.description, true);
    })
    .catch(() => showMsg(connErr, true));
}

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
                + (isCurrent ? '<span class="badge badge-active">' + <?php echo json_encode(t('current', $lang)); ?> + '</span>' : '<button onclick="revokeSession(\'' + s.id + '\')">' + <?php echo json_encode(t('revoke', $lang)); ?> + '</button>')
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
            showMsg(<?php echo json_encode(t('session-revoked', $lang)); ?>, false);
            window.location.reload();
        } else showMsg(data.description, true);
    });
}
</script>
</body>
</html>
