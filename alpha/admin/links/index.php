<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header-alpha.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta-alpha.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/translations.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/admin-auth.php");
    admin_auth_start_session();
    $admin = admin_require_login();
    global $title_header;
    $lang = detectLanguage();
    ?>
    <title>Manage Links — Admin — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
    <div class="auth-nav">
        <a href="/alpha/admin/" class="auth-nav-link">Dashboard</a>
        <a href="/alpha/admin/logout/" class="auth-nav-link auth-logout-btn"><?php echo t('logout', $lang); ?></a>
    </div>
</header>
<main>
    <div class="horizontal-center">
        <div class="dashboard-container admin-dashboard">
            <h2 class="title-section brilors">Manage Links</h2>

            <div class="admin-toolbar">
                <input type="text" id="search-input" class="input-link" placeholder="Search by name, URL or IP..." oninput="debounceSearch()" />
                <div class="admin-filters">
                    <button class="filter-btn active" data-filter="all" onclick="setFilter('all')">All</button>
                    <button class="filter-btn" data-filter="reported" onclick="setFilter('reported')">Reported</button>
                    <button class="filter-btn" data-filter="admin_blocked" onclick="setFilter('admin_blocked')">Blocked</button>
                    <button class="filter-btn" data-filter="active" onclick="setFilter('active')">Active</button>
                    <button class="filter-btn" data-filter="expired" onclick="setFilter('expired')">Expired</button>
                </div>
            </div>

            <div id="admin-message"></div>
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
let currentPage = 1;
let currentFilter = 'all';
let searchTimeout = null;
const connErr = <?php echo json_encode(t('connection-error', $lang)); ?>;

function showMsg(text, isError) {
    const el = document.getElementById('admin-message');
    el.className = isError ? 'error-message' : 'info-message';
    el.textContent = text;
    setTimeout(() => { el.textContent = ''; el.className = ''; }, 5000);
}

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => loadLinks(1), 300);
}

function setFilter(filter) {
    currentFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('[data-filter="' + filter + '"]').classList.add('active');
    loadLinks(1);
}

function loadLinks(page) {
    currentPage = page;
    const search = document.getElementById('search-input').value;
    let url = '/api/v2/admin/links/?page=' + page + '&per_page=20';
    if (search) url += '&search=' + encodeURIComponent(search);
    if (currentFilter !== 'all') url += '&filter=' + currentFilter;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.code !== '200') { showMsg(data.description, true); return; }
            renderLinks(data.data.links, data.data.total, data.data.page, data.data.per_page);
        })
        .catch(() => showMsg(connErr, true));
}

function renderLinks(links, total, page, perPage) {
    const container = document.getElementById('links-container');
    if (links.length === 0) {
        container.innerHTML = '<p class="text-align-center">No links found.</p>';
        document.getElementById('pagination-container').innerHTML = '';
        return;
    }

    let html = '';
    links.forEach(link => {
        let status = '';
        if (link.is_admin_blocked) status = '<span class="badge badge-blocked">' + <?php echo json_encode(t('badge-blocked', $lang)); ?> + '</span>';
        else if (link.is_reported) status = '<span class="badge badge-reported">' + <?php echo json_encode(t('badge-reported', $lang)); ?> + '</span>';
        else if (link.is_expired) status = '<span class="badge badge-expired">' + <?php echo json_encode(t('badge-expired', $lang)); ?> + '</span>';
        else status = '<span class="badge badge-active">' + <?php echo json_encode(t('badge-active', $lang)); ?> + '</span>';

        html += '<div class="link-card">'
            + '<div class="link-card-header">'
            + '<span class="link-name">' + link.short_url + '</span>'
            + status
            + '</div>'
            + '<div class="link-card-dest">' + truncate(link.original_url || '(encrypted)', 80) + '</div>'
            + '<div class="link-card-meta">'
            + '<span>IP: ' + (link.created_from_ip || 'N/A') + '</span>'
            + '<span>' + (link.click_count || 0) + ' ' + <?php echo json_encode(t('clicks', $lang)); ?> + '</span>'
            + '<span>' + formatDate(link.created_at) + '</span>'
            + (link.user_email ? '<span>Owner: ' + link.user_email + '</span>' : '<span>Anonymous</span>')
            + '</div>'
            + '<div class="link-card-actions">';

        if (link.is_admin_blocked) {
            html += '<button class="btn-unblock" onclick="unblockLink(\'' + link.name + '\')">Unblock</button>';
        } else {
            html += '<button class="btn-block" onclick="blockLink(\'' + link.name + '\')">Block</button>';
        }

        html += '</div></div>';
    });
    container.innerHTML = html;

    const totalPages = Math.ceil(total / perPage);
    let pag = '';
    for (let i = 1; i <= totalPages; i++) {
        pag += '<button class="' + (i === page ? 'active' : '') + '" onclick="loadLinks(' + i + ')">' + i + '</button>';
    }
    document.getElementById('pagination-container').innerHTML = pag;
}

function truncate(str, len) {
    return str.length > len ? str.substring(0, len) + '...' : str;
}

function formatDate(d) {
    return new Date(d).toLocaleDateString();
}

function blockLink(name) {
    if (!confirm('Block link "' + name + '"? Users will NOT be able to re-enable it.')) return;

    fetch('/api/v2/admin/links/block/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({name}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            showMsg('Link blocked', false);
            loadLinks(currentPage);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg(connErr, true));
}

function unblockLink(name) {
    if (!confirm('Unblock link "' + name + '"?')) return;

    fetch('/api/v2/admin/links/unblock/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({name}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            showMsg('Link unblocked', false);
            loadLinks(currentPage);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg(connErr, true));
}

loadLinks(1);
</script>
</body>
</html>
