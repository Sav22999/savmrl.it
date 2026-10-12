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
    global $title_header;
    $lang = detectLanguage();
    ?>
    <title>Dashboard — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/auth-header.php"); ?>
</header>
<main>
    <div class="horizontal-center">
        <div class="dashboard-container">
            <h2 class="title-section brilors"><?php echo htmlspecialchars($user['username']); ?></h2>

            <?php if ($user['pro_features']): ?>
                <div class="pro-badge-banner"><?php echo t('pro-account', $lang); ?></div>
            <?php endif; ?>

            <div class="user-stats-grid" id="user-stats">
                <div class="user-stat-card">
                    <div class="user-stat-value" id="stat-links">-</div>
                    <div class="user-stat-label">Links</div>
                </div>
                <div class="user-stat-card">
                    <div class="user-stat-value" id="stat-clicks">-</div>
                    <div class="user-stat-label"><?php echo t('clicks', $lang); ?></div>
                </div>
                <div class="user-stat-card">
                    <div class="user-stat-value" id="stat-active">-</div>
                    <div class="user-stat-label"><?php echo t('badge-active', $lang); ?></div>
                </div>
            </div>

            <div class="admin-nav-grid" style="margin-bottom:20px;">
                <a href="/alpha/" class="admin-nav-card">
                    <svg class="nav-card-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    <h3><?php echo t('generate-another', $lang); ?></h3>
                </a>
                <a href="/alpha/account/settings/" class="admin-nav-card">
                    <svg class="nav-card-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5"/><path d="M13.765 2.152C13.398 2 12.932 2 12 2c-.932 0-1.398 0-1.765.152a2 2 0 00-1.083 1.083c-.092.223-.129.484-.143.863a1.617 1.617 0 01-.952 1.424 1.617 1.617 0 01-1.71-.142c-.31-.24-.555-.414-.834-.473a2 2 0 00-1.489.382c-.3.227-.504.592-.912 1.32-.408.73-.612 1.094-.66 1.456a2 2 0 00.382 1.49c.155.204.38.378.694.588a1.617 1.617 0 010 2.814c-.314.21-.54.384-.694.588a2 2 0 00-.382 1.49c.048.362.252.726.66 1.456.408.728.612 1.093.912 1.32a2 2 0 001.49.382c.278-.059.523-.234.833-.473a1.617 1.617 0 011.71-.142c.58.272.94.78.953 1.424.014.379.05.64.143.863a2 2 0 001.083 1.083C10.602 22 11.068 22 12 22c.932 0 1.398 0 1.765-.152a2 2 0 001.083-1.083c.092-.223.129-.484.143-.863.013-.644.373-1.152.952-1.424a1.616 1.616 0 011.71.142c.311.24.556.414.834.473a2 2 0 001.49-.382c.3-.227.504-.592.912-1.32.408-.73.612-1.094.66-1.456a2 2 0 00-.382-1.49c-.155-.204-.38-.378-.694-.588a1.617 1.617 0 010-2.814c.314-.21.54-.384.694-.588a2 2 0 00.382-1.49c-.048-.362-.252-.726-.66-1.456-.408-.728-.612-1.093-.912-1.32a2 2 0 00-1.49-.382c-.278.06-.523.234-.833.473a1.616 1.616 0 01-1.71.142 1.617 1.617 0 01-.953-1.424c-.014-.379-.05-.64-.143-.863a2 2 0 00-1.083-1.083z" stroke="currentColor" stroke-width="1.5"/></svg>
                    <h3><?php echo t('settings', $lang); ?></h3>
                </a>
            </div>

            <h3 class="brilors"><?php echo t('your-links', $lang); ?></h3>

            <div class="search-sort-bar">
                <input type="text" id="search-input" class="input-link" placeholder="<?php echo t('search-links', $lang); ?>" oninput="debounceSearch()" />
                <div class="sort-dropdown" id="sort-dropdown">
                    <button type="button" class="sort-dropdown-btn" id="sort-dropdown-btn">
                        <span id="sort-label"><?php echo t('sort-newest', $lang); ?></span>
                        <svg class="sort-dropdown-arrow" width="10" height="10" viewBox="0 0 10 10"><path fill="currentColor" d="M5 7L1 3h8z"/></svg>
                    </button>
                    <div class="sort-dropdown-menu">
                        <button type="button" class="sort-dropdown-option active" data-sort="newest"><?php echo t('sort-newest', $lang); ?></button>
                        <button type="button" class="sort-dropdown-option" data-sort="oldest"><?php echo t('sort-oldest', $lang); ?></button>
                        <button type="button" class="sort-dropdown-option" data-sort="most-clicks"><?php echo t('sort-most-clicks', $lang); ?></button>
                        <button type="button" class="sort-dropdown-option" data-sort="least-clicks"><?php echo t('sort-least-clicks', $lang); ?></button>
                        <button type="button" class="sort-dropdown-option" data-sort="name-az"><?php echo t('sort-name-az', $lang); ?></button>
                        <button type="button" class="sort-dropdown-option" data-sort="name-za"><?php echo t('sort-name-za', $lang); ?></button>
                    </div>
                </div>
            </div>

            <div id="dashboard-message"></div>
            <div id="links-container"></div>
            <div id="load-more-sentinel"></div>
            <div id="loading-indicator" class="text-align-center" style="padding:16px 0;">
                <p><?php echo t('loading', $lang); ?></p>
            </div>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/footer.php"); ?>
</footer>

<?php if ($user['pro_features']): ?>
<div class="modal-overlay" id="link-settings-modal" onclick="if(event.target===this)closeLinkModal()">
    <div class="modal-content">
        <div class="modal-header">
            <h3><?php echo t('edit-link', $lang); ?> <span id="modal-link-name"></span></h3>
            <button class="modal-close" onclick="closeLinkModal()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <div id="link-settings-message"></div>
        <form id="link-settings-form" class="auth-form" onsubmit="saveLinkSettings(event)">
            <input type="hidden" id="link-name" value="" />
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
                    <p style="font-size:0.9em;margin-bottom:12px;"><?php echo t('access-code-active', $lang); ?></p>
                    <label><?php echo t('current-access-code', $lang); ?></label>
                    <input type="password" id="current-access-code" class="input-link" placeholder="<?php echo t('current-access-code', $lang); ?>" />
                    <div style="display:flex;gap:8px;margin-top:8px;">
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

<script>
const isPro = <?php echo $user['pro_features'] ? 'true' : 'false'; ?>;
const S = {
    noLinks: <?php echo json_encode(t('no-links', $lang)); ?>,
    createFirst: <?php echo json_encode(t('create-first', $lang)); ?>,
    clicks: <?php echo json_encode(t('clicks', $lang)); ?>,
    expires: <?php echo json_encode(t('expires', $lang)); ?>,
    maxLabel: <?php echo json_encode(t('max-label', $lang)); ?>,
    openings: <?php echo json_encode(t('openings', $lang)); ?>,
    rename: <?php echo json_encode(t('rename', $lang)); ?>,
    expire: <?php echo json_encode(t('expire', $lang)); ?>,
    editSettings: <?php echo json_encode(t('edit-settings', $lang)); ?>,
    stats: <?php echo json_encode(t('stats', $lang)); ?>,
    confirmExpire: <?php echo json_encode(t('confirm-expire', $lang)); ?>,
    linkExpired: <?php echo json_encode(t('link-expired', $lang)); ?>,
    linkRenamedTo: <?php echo json_encode(t('link-renamed-to', $lang)); ?>,
    enterNewName: <?php echo json_encode(t('enter-new-name', $lang)); ?>,
    connErr: <?php echo json_encode(t('connection-error', $lang)); ?>,
    badgeActive: <?php echo json_encode(t('badge-active', $lang)); ?>,
    badgeExpired: <?php echo json_encode(t('badge-expired', $lang)); ?>,
    badgeReported: <?php echo json_encode(t('badge-reported', $lang)); ?>,
    badgeBlocked: <?php echo json_encode(t('badge-blocked', $lang)); ?>,
    copy: <?php echo json_encode(t('link-copied', $lang)); ?>,
    linkEncrypted: <?php echo json_encode(t('link-encrypted', $lang)); ?>,
};
let currentPage = 0;
let isLoading = false;
let hasMore = true;
const PER_PAGE = 20;
let totalLinks = 0;
let totalClicks = 0;
function fmtNum(n) { return String(Number(n)).replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
let totalActive = 0;
let searchTimeout = null;
let currentSort = 'newest';

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => reloadAll(), 300);
}

(function() {
    var dd = document.getElementById('sort-dropdown');
    var btn = document.getElementById('sort-dropdown-btn');
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        dd.classList.toggle('open');
    });
    document.addEventListener('click', function() { dd.classList.remove('open'); });
    dd.querySelector('.sort-dropdown-menu').addEventListener('click', function(e) {
        var opt = e.target.closest('.sort-dropdown-option');
        if (!opt) return;
        currentSort = opt.getAttribute('data-sort');
        document.getElementById('sort-label').textContent = opt.textContent;
        dd.querySelectorAll('.sort-dropdown-option').forEach(function(o) { o.classList.remove('active'); });
        opt.classList.add('active');
        dd.classList.remove('open');
        reloadAll();
    });
})();

function showMsg(text, isError) {
    const el = document.getElementById('dashboard-message');
    el.className = isError ? 'error-message' : 'info-message';
    el.textContent = text;
    setTimeout(() => { el.textContent = ''; el.className = ''; }, 5000);
}

function loadMore() {
    if (isLoading || !hasMore) return;
    isLoading = true;
    currentPage++;
    document.getElementById('loading-indicator').style.display = '';

    let url = '/api/v2/user/links/?page=' + currentPage + '&per_page=' + PER_PAGE;
    const searchVal = document.getElementById('search-input').value;
    if (searchVal) url += '&search=' + encodeURIComponent(searchVal);
    if (currentSort && currentSort !== 'newest') url += '&sort=' + currentSort;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            isLoading = false;
            document.getElementById('loading-indicator').style.display = 'none';
            if (data.code !== '200') { showMsg(data.description, true); return; }
            if (currentPage === 1) {
                totalLinks = data.data.total;
                totalClicks = 0;
                totalActive = 0;
            }
            data.data.links.forEach(l => {
                totalClicks += l.click_count || 0;
                if (!l.is_expired && !l.is_admin_blocked) totalActive++;
            });
            document.getElementById('stat-links').textContent = fmtNum(totalLinks);
            document.getElementById('stat-clicks').textContent = fmtNum(totalClicks);
            document.getElementById('stat-active').textContent = fmtNum(totalActive);
            appendLinks(data.data.links, data.data.total);
        })
        .catch(() => {
            isLoading = false;
            document.getElementById('loading-indicator').style.display = 'none';
            showMsg(S.connErr, true);
        });
}

function appendLinks(links, total) {
    const container = document.getElementById('links-container');
    if (currentPage === 1 && links.length === 0) {
        container.innerHTML = '<p class="text-align-center">' + S.noLinks + ' <a href="/alpha/">' + S.createFirst + '</a></p>';
        hasMore = false;
        return;
    }

    const totalPages = Math.ceil(total / PER_PAGE);
    hasMore = currentPage < totalPages;

    let html = '';
    links.forEach(link => {
        let status = '';
        if (link.is_admin_blocked) status = '<span class="badge badge-blocked">' + S.badgeBlocked + '</span>';
        else if (link.is_reported) status = '<span class="badge badge-reported">' + S.badgeReported + '</span>';
        else if (link.is_expired) status = '<span class="badge badge-expired">' + S.badgeExpired + '</span>';
        else status = '<span class="badge badge-active">' + S.badgeActive + '</span>';

        const canEdit = !link.is_admin_blocked && !link.is_expired;

        let destHtml;
        if (link.has_access_code || isEncrypted(link.original_url)) {
            destHtml = '<span class="encrypted-label"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 16c0-2.828 0-4.243.879-5.121C3.757 10 5.172 10 8 10h8c2.828 0 4.243 0 5.121.879C22 11.757 22 13.172 22 16c0 2.828 0 4.243-.879 5.121C20.243 22 18.828 22 16 22H8c-2.828 0-4.243 0-5.121-.879C2 20.243 2 18.828 2 16z" stroke="currentColor" stroke-width="1.5"/><path d="M6 10V8a6 6 0 1112 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg> ' + S.linkEncrypted + '</span>';
        } else {
            destHtml = truncate(link.original_url, 60);
        }

        html += '<div class="link-card"><div class="link-card-header">'
            + '<a href="' + link.short_url + '" class="link-name" target="_blank">' + link.short_url + '</a>'
            + status + '</div>'
            + '<div class="link-card-dest">' + destHtml + '</div>'
            + '<div class="link-card-meta">'
            + '<span>' + fmtNum(link.click_count) + ' ' + S.clicks + '</span>'
            + '<span>' + formatDate(link.created_at) + '</span>'
            + (link.expiry_date ? '<span>' + S.expires + ' ' + link.expiry_date + '</span>' : '')
            + (link.openings_limit ? '<span>' + S.maxLabel + ' ' + fmtNum(link.openings_limit) + ' ' + S.openings + '</span>' : '')
            + '</div><div class="link-card-actions">';

        if (canEdit && isPro) {
            html += '<button onclick="renameLink(\'' + link.name + '\')"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg> ' + S.rename + '</button>'
                + '<button onclick="expireLink(\'' + link.name + '\')"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M12 7v5l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg> ' + S.expire + '</button>'
                + '<button onclick="editSettings(\'' + link.name + '\')"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 15a3 3 0 100-6 3 3 0 000 6z" stroke="currentColor" stroke-width="1.5"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="1.5"/></svg> ' + S.editSettings + '</button>';
        } else if (canEdit) {
            html += '<button onclick="expireLink(\'' + link.name + '\')"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M12 7v5l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg> ' + S.expire + '</button>';
        }

        html += '<button onclick="copyLink(\'' + link.short_url + '\')"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 11c0-2.828 0-4.243.879-5.121C7.757 5 9.172 5 12 5h3c2.828 0 4.243 0 5.121.879C21 6.757 21 8.172 21 11v5c0 2.828 0 4.243-.879 5.121C19.243 22 17.828 22 15 22h-3c-2.828 0-4.243 0-5.121-.879C6 20.243 6 18.828 6 16v-5z" stroke="currentColor" stroke-width="1.5"/><path d="M6 19c-1.657 0-3-1.343-3-3v-6c0-3.771 0-5.657 1.172-6.828C5.343 2 7.229 2 11 2h4c1.657 0 3 1.343 3 5" stroke="currentColor" stroke-width="1.5"/></svg> Copy</button>'
            + '<button onclick="showQR(\'' + link.short_url + '\')"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="8" height="8" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="13" y="3" width="8" height="8" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="3" y="13" width="8" height="8" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="14" y="14" width="2" height="2" fill="currentColor"/><rect x="18" y="14" width="2" height="2" fill="currentColor"/><rect x="14" y="18" width="2" height="2" fill="currentColor"/><rect x="18" y="18" width="2" height="2" fill="currentColor"/><rect x="5" y="5" width="4" height="4" rx="0.5" fill="currentColor"/><rect x="15" y="5" width="4" height="4" rx="0.5" fill="currentColor"/><rect x="5" y="15" width="4" height="4" rx="0.5" fill="currentColor"/></svg> QR</button>'
            + '<a href="/alpha/stats/' + link.name + '" target="_blank"><button>' + S.stats + '</button></a>'
            + '</div></div>';
    });
    container.insertAdjacentHTML('beforeend', html);
}

function isEncrypted(url) {
    if (!url) return true;
    try { new URL(url); return false; } catch(e) { return true; }
}
function truncate(str, len) { return str.length > len ? str.substring(0, len) + '...' : str; }
function formatDate(d) { return new Date(d).toLocaleDateString(); }

function reloadAll() {
    document.getElementById('links-container').innerHTML = '';
    currentPage = 0;
    hasMore = true;
    totalClicks = 0;
    totalActive = 0;
    loadMore();
}

function renameLink(name) {
    const newName = prompt(S.enterNewName, name);
    if (!newName || newName === name) return;
    fetch('/api/v2/link/edit/', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({old_name: name, new_name: newName}) })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') { showMsg(S.linkRenamedTo + ' ' + data.data.new_name, false); reloadAll(); }
        else showMsg(data.description, true);
    }).catch(() => showMsg(S.connErr, true));
}

function expireLink(name) {
    if (!confirm(S.confirmExpire)) return;
    fetch('/api/v2/user/links/expire/', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({name}) })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') { showMsg(S.linkExpired, false); reloadAll(); }
        else showMsg(data.description, true);
    }).catch(() => showMsg(S.connErr, true));
}

function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        showMsg(S.copy, false);
    }).catch(() => {
        showMsg(S.connErr, true);
    });
}

let linkHasAccessCode = false;

function editSettings(name) {
    document.getElementById('link-name').value = name;
    document.getElementById('modal-link-name').textContent = name;
    document.getElementById('link-destination').value = '';
    document.getElementById('link-expiry').value = '';
    document.getElementById('link-openings').value = '';
    document.getElementById('link-redirect-seconds').value = '';
    document.getElementById('destination-hint').style.display = 'none';
    document.getElementById('link-destination').placeholder = 'https://example.com';
    document.getElementById('ac-status-has').style.display = 'none';
    document.getElementById('ac-status-none').style.display = 'none';
    if (document.getElementById('ac-change-fields')) document.getElementById('ac-change-fields').style.display = 'none';
    linkHasAccessCode = false;

    fetch('/api/v2/user/links/?per_page=50')
        .then(r => r.json())
        .then(data => {
            if (data.code !== '200') return;
            const link = data.data.links.find(l => l.name === name);
            if (!link) return;
            if (!link.has_access_code && link.original_url) {
                document.getElementById('link-destination').value = link.original_url;
            }
            if (link.has_access_code) {
                linkHasAccessCode = true;
                document.getElementById('destination-hint').style.display = '';
                document.getElementById('link-destination').placeholder = <?php echo json_encode(t('destination-encrypted', $lang)); ?>;
                document.getElementById('ac-status-has').style.display = '';
            } else {
                document.getElementById('ac-status-none').style.display = '';
            }
            if (link.expiry_date) document.getElementById('link-expiry').value = link.expiry_date;
            if (link.openings_limit) document.getElementById('link-openings').value = link.openings_limit;
            if (link.redirect_seconds) document.getElementById('link-redirect-seconds').value = link.redirect_seconds;
        })
        .catch(() => {});

    document.getElementById('link-settings-modal').classList.add('active');
}

function closeLinkModal() {
    document.getElementById('link-settings-modal').classList.remove('active');
}

function showAccessCodeAction(action) {
    document.getElementById('ac-change-fields').style.display = action === 'change' ? '' : 'none';
}

function removeAccessCode() {
    const currentCode = document.getElementById('current-access-code').value;
    if (!currentCode) { showMsg(<?php echo json_encode(t('current-access-code', $lang)); ?>, true); return; }
    const name = document.getElementById('link-name').value;
    fetch('/api/v2/user/links/update/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({name, current_access_code: currentCode, remove_access_code: true}),
    })
    .then(r => r.json())
    .then(result => {
        if (result.code === '200') { showMsg(<?php echo json_encode(t('link-settings-updated', $lang)); ?>, false); closeLinkModal(); reloadAll(); }
        else showMsg(result.description, true);
    })
    .catch(() => showMsg(S.connErr, true));
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
        if (result.code === '200') { showMsg(<?php echo json_encode(t('link-settings-updated', $lang)); ?>, false); closeLinkModal(); reloadAll(); }
        else showMsg(result.description, true);
    })
    .catch(() => showMsg(S.connErr, true));
}

const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) loadMore();
}, { rootMargin: '200px' });
observer.observe(document.getElementById('load-more-sentinel'));

loadMore();

function showQR(url) {
    document.getElementById('qr-modal-link').textContent = url;
    document.getElementById('qr-modal-img').src = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent(url);
    document.getElementById('qr-modal').classList.add('active');
}
function closeQRModal() {
    document.getElementById('qr-modal').classList.remove('active');
}
</script>

<div class="modal-overlay" id="qr-modal" onclick="if(event.target===this)closeQRModal()">
    <div class="modal-content" style="text-align:center;">
        <div class="modal-header">
            <h3>QR Code</h3>
            <button class="modal-close" onclick="closeQRModal()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <p id="qr-modal-link" style="font-size:0.9em;color:var(--color-text-secondary);word-break:break-all;margin-bottom:16px;"></p>
        <img id="qr-modal-img" alt="QR Code" style="max-width:250px;width:100%;" />
        <div style="margin-top:16px;">
            <a id="qr-modal-download" download="qrcode.png" class="button-link" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;width:auto;padding:8px 20px;font-size:0.9em;text-decoration:none;"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> Download</a>
        </div>
    </div>
</div>
<script>
document.getElementById('qr-modal-img').addEventListener('load', function() {
    document.getElementById('qr-modal-download').href = this.src;
});
</script>
</body>
</html>
