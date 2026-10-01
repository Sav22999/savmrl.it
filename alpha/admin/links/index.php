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
    <title>Manage Links — Admin — savmrl.it</title>
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
            <h2 class="title-section brilors">Manage Links</h2>

            <div class="admin-toolbar">
                <div class="search-sort-bar">
                    <input type="text" id="search-input" class="input-link" placeholder="Search by name, URL or IP..." oninput="debounceSearch()" />
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
                <div class="admin-filters">
                    <button class="filter-btn active" data-filter="all" onclick="setFilter('all')">All</button>
                    <button class="filter-btn" data-filter="user_reported" onclick="setFilter('user_reported')">Reported by users</button>
                    <button class="filter-btn" data-filter="reported" onclick="setFilter('reported')">Reported</button>
                    <button class="filter-btn" data-filter="admin_blocked" onclick="setFilter('admin_blocked')">Blocked</button>
                    <button class="filter-btn" data-filter="active" onclick="setFilter('active')">Active</button>
                    <button class="filter-btn" data-filter="expired" onclick="setFilter('expired')">Expired</button>
                </div>
                <div class="admin-date-filters">
                    <label for="date-from"><?php echo t('date-from', $lang); ?></label>
                    <input type="date" id="date-from" class="input-link" onchange="reloadAll()" />
                    <label for="date-to"><?php echo t('date-to', $lang); ?></label>
                    <input type="date" id="date-to" class="input-link" onchange="reloadAll()" />
                    <button class="btn btn-outline btn-sm" id="clear-dates-btn" onclick="clearDates()" style="display:none;"><?php echo t('clear', $lang); ?></button>
                </div>
            </div>

            <div id="admin-message"></div>
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

<div class="modal-overlay" id="confirm-modal" onclick="if(event.target===this)closeModal()">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title"></h3>
            <button class="modal-close" onclick="closeModal()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <p id="modal-body"></p>
        <div style="display:flex;gap:8px;margin-top:16px;">
            <button class="btn btn-outline" onclick="closeModal()" style="flex:1;"><?php echo t('no-thanks', $lang); ?></button>
            <button class="btn btn-primary" id="modal-confirm-btn" style="flex:1;"><?php echo t('confirm', $lang); ?></button>
        </div>
    </div>
</div>

<script>
let currentPage = 0;
let currentFilter = 'all';
let currentSort = 'newest';
let searchTimeout = null;
let isLoading = false;
let hasMore = true;
const PER_PAGE = 20;
const connErr = <?php echo json_encode(t('connection-error', $lang)); ?>;
let modalCallback = null;
function fmtNum(n) { return String(Number(n)).replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }

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
    const btn = document.getElementById('modal-confirm-btn');
    btn.className = 'btn ' + btnClass;
    btn.style.flex = '1';
    modalCallback = onConfirm;
    btn.onclick = function() { var cb = modalCallback; closeModal(); if (cb) cb(); };
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

function setFilter(filter) {
    currentFilter = filter;
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('[data-filter="' + filter + '"]').classList.add('active');
    reloadAll();
}

function clearDates() {
    document.getElementById('date-from').value = '';
    document.getElementById('date-to').value = '';
    reloadAll();
}

function reloadAll() {
    document.getElementById('links-container').innerHTML = '';
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
    const dateFrom = document.getElementById('date-from').value;
    const dateTo = document.getElementById('date-to').value;
    let url = '/api/v2/admin/links/?page=' + currentPage + '&per_page=' + PER_PAGE;
    if (search) url += '&search=' + encodeURIComponent(search);
    if (currentFilter !== 'all') url += '&filter=' + currentFilter;
    if (currentSort && currentSort !== 'newest') url += '&sort=' + currentSort;
    if (dateFrom) url += '&date_from=' + dateFrom;
    if (dateTo) url += '&date_to=' + dateTo;
    document.getElementById('clear-dates-btn').style.display = (dateFrom || dateTo) ? '' : 'none';

    fetch(url)
        .then(r => r.json())
        .then(data => {
            isLoading = false;
            document.getElementById('loading-indicator').style.display = 'none';
            if (data.code !== '200') { showMsg(data.description, true); return; }
            appendLinks(data.data.links, data.data.total);
        })
        .catch(() => {
            isLoading = false;
            document.getElementById('loading-indicator').style.display = 'none';
            showMsg(connErr, true);
        });
}

function isEncrypted(url) {
    if (!url) return true;
    try { new URL(url); return false; } catch(e) { return true; }
}

function appendLinks(links, total) {
    const container = document.getElementById('links-container');
    if (currentPage === 1 && links.length === 0) {
        container.innerHTML = '<p class="text-align-center">No links found.</p>';
        hasMore = false;
        return;
    }

    const totalPages = Math.ceil(total / PER_PAGE);
    hasMore = currentPage < totalPages;

    let html = '';
    links.forEach(link => {
        let status = '';
        if (link.is_admin_blocked) status = '<span class="badge badge-blocked">' + <?php echo json_encode(t('badge-blocked', $lang)); ?> + '</span>';
        else if (link.is_reported) status = '<span class="badge badge-reported">' + <?php echo json_encode(t('badge-reported', $lang)); ?> + '</span>';
        else if (link.is_expired) status = '<span class="badge badge-expired">' + <?php echo json_encode(t('badge-expired', $lang)); ?> + '</span>';
        else status = '<span class="badge badge-active">' + <?php echo json_encode(t('badge-active', $lang)); ?> + '</span>';

        if (link.user_reports > 0) {
            var reasonTags = '';
            if (link.user_report_reason) {
                try {
                    var reasons = JSON.parse(link.user_report_reason);
                    for (var r in reasons) {
                        if (reasons.hasOwnProperty(r)) {
                            reasonTags += ' <span class="badge badge-reason">' + r + ' ×' + reasons[r] + '</span>';
                        }
                    }
                } catch(e) {}
            }
            status += ' <span class="badge badge-user-reported">' + fmtNum(link.user_reports) + '× reported</span>' + reasonTags;
        }

        let destHtml;
        if (isEncrypted(link.original_url)) {
            destHtml = '<span class="encrypted-label"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 16c0-2.828 0-4.243.879-5.121C3.757 10 5.172 10 8 10h8c2.828 0 4.243 0 5.121.879C22 11.757 22 13.172 22 16c0 2.828 0 4.243-.879 5.121C20.243 22 18.828 22 16 22H8c-2.828 0-4.243 0-5.121-.879C2 20.243 2 18.828 2 16z" stroke="currentColor" stroke-width="1.5"/><path d="M6 10V8a6 6 0 1112 0v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg> ' + <?php echo json_encode(t('link-encrypted', $lang)); ?> + '</span>';
        } else {
            const escaped = link.original_url.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            destHtml = '<a href="' + escaped + '" target="_blank" rel="noopener">' + truncate(link.original_url, 80) + '</a>';
        }

        html += '<div class="link-card">'
            + '<div class="link-card-header">'
            + '<a href="' + link.short_url + '" class="link-name" target="_blank">' + link.short_url + '</a>'
            + status
            + '</div>'
            + '<div class="link-card-dest">' + destHtml + '</div>'
            + '<div class="link-card-meta">'
            + '<span>IP: ' + (link.created_from_ip || 'N/A') + '</span>'
            + '<span>' + fmtNum(link.click_count || 0) + ' ' + <?php echo json_encode(t('clicks', $lang)); ?> + '</span>'
            + '<span>' + formatDate(link.created_at) + '</span>'
            + (link.user_email ? '<span>Owner: ' + link.user_email + '</span>' : '<span>Anonymous</span>')
            + '</div>'
            + '<div class="link-card-actions">';

        if (link.is_admin_blocked) {
            html += '<button class="btn-action-unblock" onclick="unblockLink(\'' + link.name + '\')"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/></svg> Unblock</button>';
        } else {
            html += '<button class="btn-action-block" onclick="blockLink(\'' + link.name + '\')"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M5.7 5.7l12.6 12.6" stroke="currentColor" stroke-width="1.5"/></svg> Block</button>';
        }

        if (link.is_reported) {
            html += '<button class="btn-action-unreport" onclick="unreportLink(\'' + link.name + '\')"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" stroke="currentColor" stroke-width="1.5"/><path d="M4 22v-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg> Unreport</button>';
        } else {
            html += '<button class="btn-action-report" onclick="reportLink(\'' + link.name + '\')"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" stroke="currentColor" stroke-width="1.5"/><path d="M4 22v-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg> Report</button>';
        }

        if (link.user_reports > 0) {
            html += '<button class="btn-action-dismiss" onclick="dismissUserReports(\'' + link.name + '\')"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg> Dismiss reports</button>';
        }

        html += '</div></div>';
    });
    container.insertAdjacentHTML('beforeend', html);
}

function truncate(str, len) {
    return str.length > len ? str.substring(0, len) + '...' : str;
}

function formatDate(d) {
    return new Date(d).toLocaleDateString();
}

function doAction(url, name, successMsg) {
    fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({name}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') { showMsg(successMsg, false); reloadAll(); }
        else showMsg(data.description, true);
    })
    .catch(() => showMsg(connErr, true));
}

function blockLink(name) {
    openModal('Block Link', 'Block "' + name + '"? Users will NOT be able to access this link.', 'btn-danger', function() {
        doAction('/api/v2/admin/links/block/', name, 'Link blocked');
    });
}

function unblockLink(name) {
    openModal('Unblock Link', 'Unblock "' + name + '"? The link will be accessible again.', 'btn-success', function() {
        doAction('/api/v2/admin/links/unblock/', name, 'Link unblocked');
    });
}

function reportLink(name) {
    openModal('Report Link', 'Mark "' + name + '" as reported?', 'btn-primary', function() {
        doAction('/api/v2/admin/links/report/', name, 'Link marked as reported');
    });
}

function unreportLink(name) {
    openModal('Clear Report', 'Remove the report flag from "' + name + '"?', 'btn-success', function() {
        doAction('/api/v2/admin/links/unreport/', name, 'Link report cleared');
    });
}

function dismissUserReports(name) {
    openModal('Dismiss User Reports', 'Dismiss all user reports for "' + name + '"? The report counter will be reset to 0.', 'btn-success', function() {
        doAction('/api/v2/admin/links/dismiss-reports/', name, 'User reports dismissed');
    });
}

const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) loadMore();
}, { rootMargin: '200px' });
observer.observe(document.getElementById('load-more-sentinel'));

loadMore();
</script>
</body>
</html>
