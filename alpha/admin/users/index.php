<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/header.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/meta.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/translations.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/admin-auth.php");
    admin_auth_start_session();
    $admin = admin_require_login();
    global $title_header;
    $lang = detectLanguage();
    ?>
    <title>Manage Users — Admin — savmrl.it</title>
</head>
<body class="admin-mode">
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
                <div class="search-sort-bar">
                    <input type="text" id="search-input" class="input-link" placeholder="Search by email or name..." oninput="debounceSearch()" />
                    <div class="sort-dropdown" id="sort-dropdown">
                        <button type="button" class="sort-dropdown-btn" id="sort-dropdown-btn">
                            <span id="sort-label"><?php echo t('sort-newest', $lang); ?></span>
                            <svg class="sort-dropdown-arrow" width="10" height="10" viewBox="0 0 10 10"><path fill="currentColor" d="M5 7L1 3h8z"/></svg>
                        </button>
                        <div class="sort-dropdown-menu">
                            <button type="button" class="sort-dropdown-option active" data-sort="newest"><?php echo t('sort-newest', $lang); ?></button>
                            <button type="button" class="sort-dropdown-option" data-sort="oldest"><?php echo t('sort-oldest', $lang); ?></button>
                            <button type="button" class="sort-dropdown-option" data-sort="most-links"><?php echo t('sort-most-links', $lang); ?></button>
                            <button type="button" class="sort-dropdown-option" data-sort="least-links"><?php echo t('sort-least-links', $lang); ?></button>
                            <button type="button" class="sort-dropdown-option" data-sort="username-az"><?php echo t('sort-username-az', $lang); ?></button>
                            <button type="button" class="sort-dropdown-option" data-sort="username-za"><?php echo t('sort-username-za', $lang); ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="admin-message"></div>
            <div id="users-container"></div>
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

<div class="modal-overlay" id="confirm-modal" onclick="if(event.target===this)closeModal()">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title"></h3>
            <button class="modal-close" onclick="closeModal()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <p id="modal-body"></p>
        <div id="modal-actions-default" style="display:flex;gap:8px;margin-top:16px;">
            <button class="btn btn-outline" onclick="closeModal()" style="flex:1;"><?php echo t('no-thanks', $lang); ?></button>
            <button class="btn btn-primary" id="modal-confirm-btn" style="flex:1;"><?php echo t('confirm', $lang); ?></button>
        </div>
        <div id="modal-actions-block" style="display:none;flex-direction:column;gap:8px;margin-top:16px;">
            <button class="btn btn-danger" id="modal-block-only" style="width:100%;">Block user only</button>
            <button class="btn btn-danger" id="modal-block-all" style="width:100%;">Block user and all their links</button>
            <button class="btn btn-outline" onclick="closeModal()" style="width:100%;"><?php echo t('no-thanks', $lang); ?></button>
        </div>
    </div>
</div>

<script>
let currentPage = 0;
let currentSort = 'newest';
let searchTimeout = null;
let isLoading = false;
let hasMore = true;
function fmtNum(n) { return String(Number(n)).replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
const PER_PAGE = 20;
const connErr = <?php echo json_encode(t('connection-error', $lang)); ?>;
let modalCallback = null;

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
    const el = document.getElementById('admin-message');
    el.className = isError ? 'error-message' : 'info-message';
    el.textContent = text;
    setTimeout(() => { el.textContent = ''; el.className = ''; }, 5000);
}

function openModal(title, body, btnClass, onConfirm) {
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-body').textContent = body;
    document.getElementById('modal-actions-default').style.display = 'flex';
    document.getElementById('modal-actions-block').style.display = 'none';
    const btn = document.getElementById('modal-confirm-btn');
    btn.className = 'btn ' + btnClass;
    btn.style.flex = '1';
    modalCallback = onConfirm;
    btn.onclick = function() { var cb = modalCallback; closeModal(); if (cb) cb(); };
    document.getElementById('confirm-modal').classList.add('active');
}

function openBlockModal(title, body, onBlockOnly, onBlockAll) {
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-body').textContent = body;
    document.getElementById('modal-actions-default').style.display = 'none';
    document.getElementById('modal-actions-block').style.display = 'flex';
    document.getElementById('modal-block-only').onclick = function() { closeModal(); onBlockOnly(); };
    document.getElementById('modal-block-all').onclick = function() { closeModal(); onBlockAll(); };
    document.getElementById('confirm-modal').classList.add('active');
}

function closeModal() {
    document.getElementById('confirm-modal').classList.remove('active');
    modalCallback = null;
}

function debounceSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => reloadAll(), 300);
}

function reloadAll() {
    document.getElementById('users-container').innerHTML = '';
    currentPage = 0;
    hasMore = true;
    loadMore();
}

function loadMore() {
    if (isLoading || !hasMore) return;
    isLoading = true;
    currentPage++;
    document.getElementById('loading-indicator').style.display = '';

    const search = document.getElementById('search-input').value;
    let url = '/api/v2/admin/users/?page=' + currentPage + '&per_page=' + PER_PAGE;
    if (search) url += '&search=' + encodeURIComponent(search);
    if (currentSort && currentSort !== 'newest') url += '&sort=' + currentSort;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            isLoading = false;
            document.getElementById('loading-indicator').style.display = 'none';
            if (data.code !== '200') { showMsg(data.description, true); return; }
            appendUsers(data.data.users, data.data.total);
        })
        .catch(() => {
            isLoading = false;
            document.getElementById('loading-indicator').style.display = 'none';
            showMsg(connErr, true);
        });
}

function appendUsers(users, total) {
    const container = document.getElementById('users-container');
    if (currentPage === 1 && users.length === 0) {
        container.innerHTML = '<p class="text-align-center">No users found.</p>';
        hasMore = false;
        return;
    }

    const totalPages = Math.ceil(total / PER_PAGE);
    hasMore = currentPage < totalPages;

    let html = '';
    users.forEach(user => {
        const isPro = user.pro_features == 1;
        const isBlocked = user.blocked == 1;

        html += '<div class="link-card user-card">'
            + '<div class="link-card-header">'
            + '<span class="link-name">' + escHtml(user.username) + '</span>'
            + (isBlocked ? '<span class="badge badge-blocked">' + <?php echo json_encode(t('badge-blocked', $lang)); ?> + '</span>' : '')
            + (isPro ? '<span class="badge badge-pro">Pro</span>' : '')
            + (user.email_verified ? '<span class="badge badge-active">Verified</span>' : '<span class="badge badge-expired">Unverified</span>')
            + (user.two_fa_enabled ? '<span class="badge badge-2fa">2FA</span>' : '')
            + '</div>'
            + '<div class="link-card-meta">'
            + '<span>' + escHtml(user.email) + '</span>'
            + '<span>' + fmtNum(user.link_count) + ' links</span>'
            + '<span>Joined: ' + formatDate(user.created_at) + '</span>'
            + '</div>'
            + '<div class="link-card-actions">';

        if (isPro) {
            html += '<button class="btn-action-block" onclick="togglePro(' + user.id + ', false)"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg> Remove Pro</button>';
        } else {
            html += '<button class="btn-action-unblock" onclick="togglePro(' + user.id + ', true)"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg> Grant Pro</button>';
        }

        if (isBlocked) {
            html += '<button class="btn-action-unblock" onclick="toggleBlock(' + user.id + ', false)"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/></svg> Unblock</button>';
        } else {
            html += '<button class="btn-action-block" onclick="toggleBlock(' + user.id + ', true)"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M5.7 5.7l12.6 12.6" stroke="currentColor" stroke-width="1.5"/></svg> Block</button>';
        }

        html += '</div></div>';
    });
    container.insertAdjacentHTML('beforeend', html);
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

function formatDate(d) {
    return new Date(d).toLocaleDateString();
}

function togglePro(userId, enable) {
    const title = enable ? 'Grant Pro' : 'Remove Pro';
    const body = enable ? 'Grant pro features to this user?' : 'Remove pro features from this user?';
    const btnClass = enable ? 'btn-success' : 'btn-danger';
    openModal(title, body, btnClass, function() {
        fetch('/api/v2/admin/users/pro/', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({user_id: userId, enable}),
        })
        .then(r => r.json())
        .then(data => {
            if (data.code === '200') { showMsg('Pro features ' + (enable ? 'granted' : 'removed'), false); reloadAll(); }
            else showMsg(data.description, true);
        })
        .catch(() => showMsg(connErr, true));
    });
}

function doBlock(userId, blockLinks) {
    fetch('/api/v2/admin/users/block/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({user_id: userId, block: true, block_links: blockLinks}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            var msg = 'User blocked';
            if (blockLinks && data.data.links_blocked > 0) msg += ' (' + fmtNum(data.data.links_blocked) + ' links blocked)';
            showMsg(msg, false);
            reloadAll();
        } else showMsg(data.description, true);
    })
    .catch(() => showMsg(connErr, true));
}

function toggleBlock(userId, block) {
    if (block) {
        openBlockModal(
            'Block User',
            'Block this user? They will be logged out and unable to sign in.',
            function() { doBlock(userId, false); },
            function() { doBlock(userId, true); }
        );
    } else {
        openModal('Unblock User', 'Unblock this user? They will be able to sign in again.', 'btn-success', function() {
            fetch('/api/v2/admin/users/block/', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({user_id: userId, block: false}),
            })
            .then(r => r.json())
            .then(data => {
                if (data.code === '200') { showMsg('User unblocked', false); reloadAll(); }
                else showMsg(data.description, true);
            })
            .catch(() => showMsg(connErr, true));
        });
    }
}

const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) loadMore();
}, { rootMargin: '200px' });
observer.observe(document.getElementById('load-more-sentinel'));

loadMore();
</script>
</body>
</html>
