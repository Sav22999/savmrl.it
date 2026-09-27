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
    global $title_header;
    $lang = detectLanguage();
    ?>
    <title>Dashboard — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/auth-header-alpha.php"); ?>
</header>
<main>
    <div class="horizontal-center">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h2 class="title-section brilors"><?php echo t('your-links', $lang); ?></h2>
                <a href="/alpha/account/settings/" class="dashboard-settings-link"><?php echo t('settings', $lang); ?></a>
            </div>

            <?php if ($user['pro_features']): ?>
                <div class="pro-badge-banner"><?php echo t('pro-account', $lang); ?></div>
            <?php endif; ?>

            <div id="dashboard-message"></div>
            <div id="links-container">
                <p class="text-align-center"><?php echo t('loading', $lang); ?></p>
            </div>
            <div id="pagination-container" class="pagination"></div>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer-alpha.php"); ?>
</footer>

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
};
let currentPage = 1;

function showMsg(text, isError) {
    const el = document.getElementById('dashboard-message');
    el.className = isError ? 'error-message' : 'info-message';
    el.textContent = text;
    setTimeout(() => { el.textContent = ''; el.className = ''; }, 5000);
}

function loadLinks(page) {
    currentPage = page;
    fetch('/api/v2/user/links/?page=' + page + '&per_page=20')
        .then(r => r.json())
        .then(data => {
            if (data.code !== '200') { showMsg(data.description, true); return; }
            renderLinks(data.data.links, data.data.total, data.data.page, data.data.per_page);
        })
        .catch(() => showMsg(S.connErr, true));
}

function renderLinks(links, total, page, perPage) {
    const container = document.getElementById('links-container');
    if (links.length === 0) {
        container.innerHTML = '<p class="text-align-center">' + S.noLinks + ' <a href="/alpha/">' + S.createFirst + '</a></p>';
        return;
    }

    let html = '';
    links.forEach(link => {
        let status = '';
        if (link.is_admin_blocked) status = '<span class="badge badge-blocked">' + S.badgeBlocked + '</span>';
        else if (link.is_reported) status = '<span class="badge badge-reported">' + S.badgeReported + '</span>';
        else if (link.is_expired) status = '<span class="badge badge-expired">' + S.badgeExpired + '</span>';
        else status = '<span class="badge badge-active">' + S.badgeActive + '</span>';

        const disabled = link.is_admin_blocked ? 'disabled' : '';

        html += '<div class="link-card"><div class="link-card-header">'
            + '<a href="' + link.short_url + '" class="link-name" target="_blank">' + link.short_url + '</a>'
            + status + '</div>'
            + '<div class="link-card-dest">' + truncate(link.original_url, 60) + '</div>'
            + '<div class="link-card-meta">'
            + '<span>' + link.click_count + ' ' + S.clicks + '</span>'
            + '<span>' + formatDate(link.created_at) + '</span>'
            + (link.expiry_date ? '<span>' + S.expires + ' ' + link.expiry_date + '</span>' : '')
            + (link.openings_limit ? '<span>' + S.maxLabel + ' ' + link.openings_limit + ' ' + S.openings + '</span>' : '')
            + '</div><div class="link-card-actions">';

        if (isPro && !link.is_admin_blocked) {
            html += '<button onclick="renameLink(\'' + link.name + '\')" ' + disabled + '>' + S.rename + '</button>'
                + '<button onclick="expireLink(\'' + link.name + '\')" ' + disabled + '>' + S.expire + '</button>'
                + '<button onclick="editSettings(\'' + link.name + '\')">' + S.editSettings + '</button>';
        } else if (!link.is_admin_blocked) {
            html += '<button onclick="expireLink(\'' + link.name + '\')">' + S.expire + '</button>';
        }

        html += '<a href="/stats/' + link.name + '" target="_blank"><button>' + S.stats + '</button></a>'
            + '</div></div>';
    });
    container.innerHTML = html;

    const totalPages = Math.ceil(total / perPage);
    let pag = '';
    for (let i = 1; i <= totalPages; i++) {
        pag += '<button class="' + (i === page ? 'active' : '') + '" onclick="loadLinks(' + i + ')">' + i + '</button>';
    }
    document.getElementById('pagination-container').innerHTML = pag;
}

function truncate(str, len) { return str.length > len ? str.substring(0, len) + '...' : str; }
function formatDate(d) { return new Date(d).toLocaleDateString(); }

function renameLink(name) {
    const newName = prompt(S.enterNewName, name);
    if (!newName || newName === name) return;
    fetch('/api/v2/link/edit/', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({old_name: name, new_name: newName}) })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') { showMsg(S.linkRenamedTo + ' ' + data.data.new_name, false); loadLinks(currentPage); }
        else showMsg(data.description, true);
    }).catch(() => showMsg(S.connErr, true));
}

function expireLink(name) {
    if (!confirm(S.confirmExpire)) return;
    fetch('/api/v2/user/links/expire/', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({name}) })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') { showMsg(S.linkExpired, false); loadLinks(currentPage); }
        else showMsg(data.description, true);
    }).catch(() => showMsg(S.connErr, true));
}

function editSettings(name) { window.location.href = '/alpha/account/settings/?link=' + encodeURIComponent(name); }

loadLinks(1);
</script>
</body>
</html>
