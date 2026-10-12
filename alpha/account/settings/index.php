<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/header.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/meta.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/translations.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/auth.php");
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
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/auth-header.php"); ?>
</header>
<main>
    <div class="horizontal-center">
        <div class="dashboard-container">
            <a href="/alpha/account/" class="back-nav-link"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 12H5m0 0l6-6m-6 6l6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg> <?php echo t('back-to-dashboard', $lang); ?></a>
            <h2 class="title-section brilors"><?php echo t('account-settings', $lang); ?></h2>
            <div id="settings-message"></div>

            <div class="settings-cards-grid">

                <?php if ($link_to_edit && $user['pro_features']): ?>
                <div class="settings-card" onclick="openSettingsModal('link-settings-modal')">
                    <div class="settings-card-content">
                        <div class="settings-card-icon">&#9881;</div>
                        <div class="settings-card-info">
                            <h3><?php echo t('edit-link', $lang); ?></h3>
                            <p><?php echo $link_to_edit; ?></p>
                        </div>
                    </div>
                    <span class="settings-card-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                </div>
                <?php endif; ?>

                <div class="settings-card" onclick="openSettingsModal('language-modal')">
                    <div class="settings-card-content">
                        <div class="settings-card-icon"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/><path d="M12 2c-4 4-4 14 0 20M12 2c4 4 4 14 0 20M2 12h20" stroke="currentColor" stroke-width="1.5"/></svg></div>
                        <div class="settings-card-info">
                            <h3><?php echo t('preferred-language', $lang); ?></h3>
                            <p id="current-lang-display"><?php
                                $langNames = ['en'=>'English','it'=>'Italiano','fr'=>'Français','de'=>'Deutsch','es'=>'Español'];
                                echo $langNames[$user['preferred_language']] ?? 'English';
                            ?></p>
                        </div>
                    </div>
                    <span class="settings-card-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                </div>

                <div class="settings-card" onclick="openSettingsModal('password-modal')">
                    <div class="settings-card-content">
                        <div class="settings-card-icon"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 16c0-2.828 0-4.243.879-5.121C3.757 10 5.172 10 8 10h8c2.828 0 4.243 0 5.121.879C22 11.757 22 13.172 22 16c0 2.828 0 4.243-.879 5.121C20.243 22 18.828 22 16 22H8c-2.828 0-4.243 0-5.121-.879C2 20.243 2 18.828 2 16z" stroke="currentColor" stroke-width="1.5"/><path d="M6 10V8a6 6 0 1112 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></div>
                        <div class="settings-card-info">
                            <h3><?php echo t('change-password', $lang); ?></h3>
                            <p><?php echo t('change-password-desc', $lang); ?></p>
                        </div>
                    </div>
                    <span class="settings-card-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                </div>

                <div class="settings-card" onclick="openSettingsModal('twofa-modal')">
                    <div class="settings-card-content">
                        <div class="settings-card-icon"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 2l7.794 2.938c.37.14.618.487.618.88v5.05c0 3.848-2.543 7.244-6.222 8.35L12 20l-2.19-.782C6.131 18.112 3.588 14.716 3.588 10.868V5.818c0-.393.247-.74.618-.88L12 2z" stroke="currentColor" stroke-width="1.5"/></svg></div>
                        <div class="settings-card-info">
                            <h3><?php echo t('two-factor-auth', $lang); ?></h3>
                            <p><?php echo t('status', $lang); ?>: <strong><?php echo $user['two_fa_enabled'] ? t('enabled', $lang) : t('disabled', $lang); ?></strong></p>
                        </div>
                    </div>
                    <span class="settings-card-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                </div>

                <div class="settings-card" onclick="openSettingsModal('sessions-modal')">
                    <div class="settings-card-content">
                        <div class="settings-card-icon"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 6c0-1.886 0-2.828.586-3.414C3.172 2 4.114 2 6 2h12c1.886 0 2.828 0 3.414.586C22 3.172 22 4.114 22 6v8c0 1.886 0 2.828-.586 3.414C20.828 18 19.886 18 18 18H6c-1.886 0-2.828 0-3.414-.586C2 16.828 2 15.886 2 14V6z" stroke="currentColor" stroke-width="1.5"/><path d="M8 22h8M12 18v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></div>
                        <div class="settings-card-info">
                            <h3><?php echo t('active-sessions', $lang); ?></h3>
                            <p id="sessions-count"><?php echo t('loading', $lang); ?></p>
                        </div>
                    </div>
                    <span class="settings-card-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                </div>

                <div class="settings-card settings-card-danger" onclick="openSettingsModal('delete-modal')">
                    <div class="settings-card-content">
                        <div class="settings-card-icon"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.5 6h-17M18.833 8.5l-.46 6.9c-.177 2.654-.265 3.981-1.13 4.79-.865.81-2.195.81-4.856.81h-.774c-2.66 0-3.99 0-4.856-.81-.865-.809-.953-2.136-1.13-4.79L5.167 8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M9.5 11l.5 5M14.5 11l-.5 5M6.5 6c.293-1.656 1.756-3 3.5-3h4c1.744 0 3.207 1.344 3.5 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></div>
                        <div class="settings-card-info">
                            <h3><?php echo t('delete-account', $lang); ?></h3>
                            <p><?php echo t('delete-account-desc', $lang); ?></p>
                        </div>
                    </div>
                    <span class="settings-card-arrow"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                </div>

            </div>

        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/footer.php"); ?>
</footer>

<!-- MODALS -->

<?php if ($link_to_edit && $user['pro_features']): ?>
<div class="modal-overlay" id="link-settings-modal" onclick="if(event.target===this)closeSettingsModal(this)">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?php echo t('edit-link', $lang); ?> <?php echo $link_to_edit; ?></h3>
            <button class="modal-close" onclick="closeSettingsModal(document.getElementById('link-settings-modal'))"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form id="link-settings-form" class="auth-form" onsubmit="saveLinkSettings(event)">
            <input type="hidden" id="link-name" value="<?php echo $link_to_edit; ?>" />
            <label><?php echo t('destination-url', $lang); ?></label>
            <input type="url" id="link-destination" class="input-link" placeholder="https://example.com" />
            <p class="input-hint" id="destination-hint" style="display:none;"><?php echo t('destination-access-code-hint', $lang); ?></p>
            <label><?php echo t('expiry-date', $lang); ?></label>
            <input type="date" id="link-expiry" class="input-link" />
            <label><?php echo t('max-openings', $lang); ?></label>
            <input type="number" id="link-openings" class="input-link" min="1" />
            <label><?php echo t('redirect-seconds', $lang); ?></label>
            <input type="number" id="link-redirect-seconds" class="input-link" min="5" max="30" placeholder="<?php echo t('redirect-seconds-hint', $lang); ?>" />

            <hr style="margin:16px 0;border-color:var(--color-border);" />

            <div id="access-code-section">
                <div id="ac-status-has" style="display:none;">
                    <p style="font-size:0.9em;margin-bottom:12px;"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 16c0-2.828 0-4.243.879-5.121C3.757 10 5.172 10 8 10h8c2.828 0 4.243 0 5.121.879C22 11.757 22 13.172 22 16c0 2.828 0 4.243-.879 5.121C20.243 22 18.828 22 16 22H8c-2.828 0-4.243 0-5.121-.879C2 20.243 2 18.828 2 16z" stroke="currentColor" stroke-width="1.5"/><path d="M6 10V8a6 6 0 1112 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg> <?php echo t('access-code-active', $lang); ?></p>
                    <label><?php echo t('current-access-code', $lang); ?></label>
                    <input type="password" id="current-access-code" class="input-link" placeholder="<?php echo t('current-access-code', $lang); ?>" />
                    <div id="ac-actions" style="display:flex;gap:8px;margin-top:8px;">
                        <button type="button" class="button-link" style="flex:1;" onclick="showAccessCodeAction('change')"><?php echo t('modify-access-code', $lang); ?></button>
                        <button type="button" class="btn btn-danger" style="flex:1;" onclick="removeAccessCode()"><?php echo t('remove-access-code', $lang); ?></button>
                    </div>
                    <div id="ac-change-fields" style="display:none;margin-top:12px;">
                        <label><?php echo t('new-access-code', $lang); ?></label>
                        <input type="password" id="new-access-code" class="input-link" placeholder="<?php echo t('new-access-code', $lang); ?>" />
                    </div>
                </div>
                <div id="ac-status-none" style="display:none;">
                    <p style="font-size:0.9em;color:var(--color-text-secondary);margin-bottom:12px;"><?php echo t('access-code-none', $lang); ?></p>
                    <label><?php echo t('set-access-code', $lang); ?></label>
                    <input type="password" id="set-access-code" class="input-link" placeholder="<?php echo t('new-access-code', $lang); ?>" />
                </div>
            </div>

            <input type="submit" class="button-link" style="margin-top:16px;" value="<?php echo t('save-link-settings', $lang); ?>" />
        </form>
    </div>
</div>
<?php endif; ?>

<div class="modal-overlay" id="language-modal" onclick="if(event.target===this)closeSettingsModal(this)">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?php echo t('preferred-language', $lang); ?></h3>
            <button class="modal-close" onclick="closeSettingsModal(document.getElementById('language-modal'))"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <div class="language-options">
            <?php
            $languages = ['en'=>'English','it'=>'Italiano','fr'=>'Français','de'=>'Deutsch','es'=>'Español'];
            foreach ($languages as $code => $name):
                $isActive = ($user['preferred_language'] === $code) ? 'active' : '';
            ?>
            <button class="language-option <?php echo $isActive; ?>" onclick="saveLanguage('<?php echo $code; ?>')" data-lang="<?php echo $code; ?>">
                <?php echo $name; ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="modal-overlay" id="password-modal" onclick="if(event.target===this)closeSettingsModal(this)">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?php echo t('change-password', $lang); ?></h3>
            <button class="modal-close" onclick="closeSettingsModal(document.getElementById('password-modal'))"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
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
                <input type="text" id="pw-otp-code" class="input-link otp-input" placeholder="······" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" required />
                <input type="submit" class="button-link" value="<?php echo t('confirm', $lang); ?>" />
            </form>
            <button type="button" class="otp-resend" onclick="resendOtp('password_change', 'pw')"><?php echo t('resend-code', $lang); ?></button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="twofa-modal" onclick="if(event.target===this)closeSettingsModal(this)">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?php echo t('two-factor-auth', $lang); ?></h3>
            <button class="modal-close" onclick="closeSettingsModal(document.getElementById('twofa-modal'))"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <p><?php echo t('status', $lang); ?>: <strong id="twofa-status"><?php echo $user['two_fa_enabled'] ? t('enabled', $lang) : t('disabled', $lang); ?></strong></p>
        <div id="twofa-toggle-btn">
        <?php if ($user['two_fa_enabled']): ?>
            <button class="btn btn-danger btn-block" onclick="toggle2FA(false)"><?php echo t('disable-2fa', $lang); ?></button>
        <?php else: ?>
            <button class="button-link" onclick="toggle2FA(true)"><?php echo t('enable-2fa', $lang); ?></button>
        <?php endif; ?>
        </div>
        <div id="twofa-verify" class="hidden" style="margin-top:16px;">
            <p><?php echo t('enter-code', $lang); ?></p>
            <form class="auth-form" onsubmit="verify2FAToggle(event)">
                <input type="text" id="twofa-code" class="input-link otp-input" placeholder="······" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required />
                <input type="submit" class="button-link" value="<?php echo t('confirm', $lang); ?>" />
            </form>
            <button type="button" class="otp-resend" onclick="resendOtp('login_2fa', 'twofa')"><?php echo t('resend-code', $lang); ?></button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="sessions-modal" onclick="if(event.target===this)closeSettingsModal(this)">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?php echo t('active-sessions', $lang); ?></h3>
            <button class="modal-close" onclick="closeSettingsModal(document.getElementById('sessions-modal'))"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <div id="sessions-list"><?php echo t('loading', $lang); ?></div>
    </div>
</div>

<div class="modal-overlay" id="delete-modal" onclick="if(event.target===this)closeSettingsModal(this)">
    <div class="modal-content">
        <div class="modal-header">
            <h3 style="color:var(--color-error);"><?php echo t('delete-account', $lang); ?></h3>
            <button class="modal-close" onclick="closeSettingsModal(document.getElementById('delete-modal'))"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <p style="font-size:0.9em;color:var(--color-text-secondary);margin-bottom:16px;"><?php echo t('delete-account-desc', $lang); ?></p>
        <div id="delete-step1">
            <form class="auth-form" onsubmit="requestDeleteAccount(event)">
                <input type="password" id="delete-password" class="input-link" placeholder="<?php echo t('password', $lang); ?>" required />
                <input type="submit" class="btn btn-danger btn-block" value="<?php echo t('confirm-delete-account', $lang); ?>" />
            </form>
        </div>
        <div id="delete-step2" class="hidden">
            <p><?php echo t('enter-code', $lang); ?></p>
            <form class="auth-form" onsubmit="confirmDeleteAccount(event)">
                <input type="text" id="del-otp-code" class="input-link otp-input" placeholder="······" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" required />
                <input type="submit" class="btn btn-danger btn-block" value="<?php echo t('confirm', $lang); ?>" />
            </form>
            <button type="button" class="otp-resend" onclick="resendOtp('account_deletion', 'del')"><?php echo t('resend-code', $lang); ?></button>
        </div>
    </div>
</div>

<script>
const currentSessionId = '<?php echo htmlspecialchars($current_session_id ?? ''); ?>';
let twofaTempToken = null;
let twofaEnabling = true;
const connErr = <?php echo json_encode(t('connection-error', $lang)); ?>;
const linkToEdit = <?php echo json_encode($link_to_edit); ?>;

function openSettingsModal(id) {
    document.getElementById(id).classList.add('active');
}

function closeSettingsModal(el) {
    el.classList.remove('active');
}

function resendOtp(purpose, prefix) {
    var tokenMap = {pw: pwTempToken, twofa: twofaTempToken, del: delTempToken};
    var token = tokenMap[prefix];
    if (!token) { showMsg(connErr, true); return; }

    var btn = event.target;
    btn.disabled = true;
    btn.textContent = '...';

    fetch('/api/v2/user/resend-otp/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({temp_token: token, purpose: purpose}),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.code === '200') {
            if (prefix === 'pw') pwTempToken = data.data.temp_token;
            else if (prefix === 'twofa') twofaTempToken = data.data.temp_token;
            else if (prefix === 'del') delTempToken = data.data.temp_token;
            showMsg(<?php echo json_encode(t('code-resent', $lang)); ?>, false);
        } else {
            showMsg(data.description, true);
        }
        btn.disabled = false;
        btn.textContent = <?php echo json_encode(t('resend-code', $lang)); ?>;
    })
    .catch(function() {
        showMsg(connErr, true);
        btn.disabled = false;
        btn.textContent = <?php echo json_encode(t('resend-code', $lang)); ?>;
    });
}

let linkHasAccessCode = false;

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
                linkHasAccessCode = true;
                document.getElementById('destination-hint').style.display = '';
                document.getElementById('link-destination').placeholder = <?php echo json_encode(t('destination-encrypted', $lang)); ?>;
                document.getElementById('ac-status-has').style.display = '';
                document.getElementById('ac-status-none').style.display = 'none';
            } else {
                document.getElementById('ac-status-has').style.display = 'none';
                document.getElementById('ac-status-none').style.display = '';
            }
            if (link.expiry_date) {
                document.getElementById('link-expiry').value = link.expiry_date;
            }
            if (link.openings_limit) {
                document.getElementById('link-openings').value = link.openings_limit;
            }
            if (link.redirect_seconds) {
                document.getElementById('link-redirect-seconds').value = link.redirect_seconds;
            }
        })
        .catch(() => {});
}

function showAccessCodeAction(action) {
    document.getElementById('ac-change-fields').style.display = action === 'change' ? '' : 'none';
}

function removeAccessCode() {
    const currentCode = document.getElementById('current-access-code').value;
    if (!currentCode) {
        showMsg(<?php echo json_encode(t('current-access-code', $lang)); ?>, true);
        return;
    }
    const name = document.getElementById('link-name').value;
    fetch('/api/v2/user/links/update/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({name, current_access_code: currentCode, remove_access_code: true}),
    })
    .then(r => r.json())
    .then(result => {
        if (result.code === '200') {
            showMsg(<?php echo json_encode(t('link-settings-updated', $lang)); ?>, false);
            setTimeout(() => window.location.reload(), 1000);
        } else showMsg(result.description, true);
    })
    .catch(() => showMsg(connErr, true));
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
            document.getElementById('twofa-toggle-btn').style.display = 'none';
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
    const payload = {name};
    const destination = document.getElementById('link-destination').value;
    const expiry = document.getElementById('link-expiry').value;
    const openings = document.getElementById('link-openings').value;
    const redirect_seconds = document.getElementById('link-redirect-seconds').value;
    if (destination) payload.destination_url = destination;
    if (expiry) payload.expiry_date = expiry;
    if (openings) payload.openings_limit = parseInt(openings);
    if (redirect_seconds) payload.redirect_seconds = parseInt(redirect_seconds);

    if (linkHasAccessCode) {
        const currentCode = document.getElementById('current-access-code').value;
        if (currentCode) payload.current_access_code = currentCode;
        const newCode = document.getElementById('new-access-code');
        if (newCode && newCode.value) payload.new_access_code = newCode.value;
    } else {
        const setCode = document.getElementById('set-access-code');
        if (setCode && setCode.value) payload.new_access_code = setCode.value;
    }

    fetch('/api/v2/user/links/update/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload),
    })
    .then(r => r.json())
    .then(result => {
        if (result.code === '200') {
            showMsg(<?php echo json_encode(t('link-settings-updated', $lang)); ?>, false);
            setTimeout(() => window.location.reload(), 1000);
        }
        else showMsg(result.description, true);
    })
    .catch(() => showMsg(connErr, true));
}

fetch('/api/v2/user/sessions/')
    .then(r => r.json())
    .then(data => {
        if (data.code !== '200') return;
        const sessions = data.data.sessions;
        document.getElementById('sessions-count').textContent = sessions.length + ' ' + (sessions.length === 1 ? 'session' : 'sessions');
        const container = document.getElementById('sessions-list');
        let html = '';
        sessions.forEach(s => {
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

if (linkToEdit) {
    openSettingsModal('link-settings-modal');
}
</script>
</body>
</html>
