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
    <title>Manage Users — Admin — savmrl.it</title>
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
            <h2 class="title-section brilors">Manage Users</h2>

            <div class="admin-toolbar">
                <input type="text" id="search-input" class="input-link" placeholder="Search by email or name..." oninput="debounceSearch()" />
            </div>

            <div id="admin-message"></div>
            <div id="users-container">
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
    searchTimeout = setTimeout(() => loadUsers(1), 300);
}

function loadUsers(page) {
    currentPage = page;
    const search = document.getElementById('search-input').value;
    let url = '/api/v2/admin/users/?page=' + page + '&per_page=20';
    if (search) url += '&search=' + encodeURIComponent(search);

    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.code !== '200') { showMsg(data.description, true); return; }
            renderUsers(data.data.users, data.data.total, data.data.page, data.data.per_page);
        })
        .catch(() => showMsg(connErr, true));
}

function renderUsers(users, total, page, perPage) {
    const container = document.getElementById('users-container');
    if (users.length === 0) {
        container.innerHTML = '<p class="text-align-center">No users found.</p>';
        document.getElementById('pagination-container').innerHTML = '';
        return;
    }

    let html = '';
    users.forEach(user => {
        const isPro = user.pro_features == 1;
        const isBlocked = user.blocked == 1;

        html += '<div class="link-card user-card">'
            + '<div class="link-card-header">'
            + '<span class="link-name">' + user.username + '</span>'
            + (isBlocked ? '<span class="badge badge-blocked">' + <?php echo json_encode(t('badge-blocked', $lang)); ?> + '</span>' : '')
            + (isPro ? '<span class="badge badge-pro">Pro</span>' : '')
            + (user.email_verified ? '<span class="badge badge-active">Verified</span>' : '<span class="badge badge-expired">Unverified</span>')
            + (user.two_fa_enabled ? '<span class="badge badge-2fa">2FA</span>' : '')
            + '</div>'
            + '<div class="link-card-meta">'
            + '<span>' + user.email + '</span>'
            + '<span>' + user.link_count + ' links</span>'
            + '<span>Joined: ' + formatDate(user.created_at) + '</span>'
            + '</div>'
            + '<div class="link-card-actions">';

        if (isPro) {
            html += '<button class="btn-block" onclick="togglePro(' + user.id + ', false)">Remove Pro</button>';
        } else {
            html += '<button class="btn-unblock" onclick="togglePro(' + user.id + ', true)">Grant Pro</button>';
        }

        if (isBlocked) {
            html += '<button class="btn-unblock" onclick="toggleBlock(' + user.id + ', false)">Unblock</button>';
        } else {
            html += '<button class="btn-block" onclick="toggleBlock(' + user.id + ', true)">Block</button>';
        }

        html += '</div></div>';
    });
    container.innerHTML = html;

    const totalPages = Math.ceil(total / perPage);
    let pag = '';
    for (let i = 1; i <= totalPages; i++) {
        pag += '<button class="' + (i === page ? 'active' : '') + '" onclick="loadUsers(' + i + ')">' + i + '</button>';
    }
    document.getElementById('pagination-container').innerHTML = pag;
}

function formatDate(d) {
    return new Date(d).toLocaleDateString();
}

function togglePro(userId, enable) {
    const action = enable ? 'grant' : 'remove';
    if (!confirm(action.charAt(0).toUpperCase() + action.slice(1) + ' pro features for this user?')) return;

    fetch('/api/v2/admin/users/pro/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({user_id: userId, enable}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            showMsg('Pro features ' + (enable ? 'granted' : 'removed'), false);
            loadUsers(currentPage);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg(connErr, true));
}

function toggleBlock(userId, block) {
    const msg = block ? 'Block this user? They will be logged out and unable to sign in.' : 'Unblock this user?';
    if (!confirm(msg)) return;

    fetch('/api/v2/admin/users/block/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({user_id: userId, block}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            showMsg('User ' + (block ? 'blocked' : 'unblocked'), false);
            loadUsers(currentPage);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg(connErr, true));
}

loadUsers(1);
</script>
</body>
</html>
