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
    ?>
    <title>Dashboard — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/auth-header.php"); ?>
</header>
<main>
    <div class="horizontal-center">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h2 class="title-section brilors">Your links</h2>
                <a href="/account/settings/" class="dashboard-settings-link">Settings</a>
            </div>

            <?php if ($user['pro_features']): ?>
                <div class="pro-badge-banner">Pro account</div>
            <?php endif; ?>

            <div id="dashboard-message"></div>
            <div id="links-container">
                <p class="text-align-center">Loading...</p>
            </div>
            <div id="pagination-container" class="pagination"></div>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

<script>
const isPro = <?php echo $user['pro_features'] ? 'true' : 'false'; ?>;
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
        .catch(() => showMsg('Connection error', true));
}

function renderLinks(links, total, page, perPage) {
    const container = document.getElementById('links-container');
    if (links.length === 0) {
        container.innerHTML = '<p class="text-align-center">No links yet. <a href="/">Create your first link</a></p>';
        return;
    }

    let html = '';
    links.forEach(link => {
        let status = '';
        if (link.is_admin_blocked) status = '<span class="badge badge-blocked">Blocked by admin</span>';
        else if (link.is_reported) status = '<span class="badge badge-reported">Reported</span>';
        else if (link.is_expired) status = '<span class="badge badge-expired">Expired</span>';
        else status = '<span class="badge badge-active">Active</span>';

        const disabled = link.is_admin_blocked ? 'disabled' : '';

        html += '<div class="link-card">'
            + '<div class="link-card-header">'
            + '<a href="' + link.short_url + '" class="link-name" target="_blank">' + link.short_url + '</a>'
            + status
            + '</div>'
            + '<div class="link-card-dest">' + truncate(link.original_url, 60) + '</div>'
            + '<div class="link-card-meta">'
            + '<span>' + link.click_count + ' clicks</span>'
            + '<span>' + formatDate(link.created_at) + '</span>'
            + (link.expiry_date ? '<span>Expires: ' + link.expiry_date + '</span>' : '')
            + (link.openings_limit ? '<span>Max: ' + link.openings_limit + ' openings</span>' : '')
            + '</div>'
            + '<div class="link-card-actions">';

        if (isPro && !link.is_admin_blocked) {
            html += '<button onclick="renameLink(\'' + link.name + '\')" ' + disabled + '>Rename</button>'
                + '<button onclick="expireLink(\'' + link.name + '\')" ' + disabled + '>Expire</button>'
                + '<button onclick="editSettings(\'' + link.name + '\')">Edit settings</button>';
        } else if (!link.is_admin_blocked) {
            html += '<button onclick="expireLink(\'' + link.name + '\')">Expire</button>';
        }

        html += '<a href="/stats/' + link.name + '" target="_blank"><button>Stats</button></a>'
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

function truncate(str, len) {
    return str.length > len ? str.substring(0, len) + '...' : str;
}

function formatDate(d) {
    return new Date(d).toLocaleDateString();
}

function renameLink(name) {
    const newName = prompt('Enter new name for the link:', name);
    if (!newName || newName === name) return;

    fetch('/api/v2/link/edit/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({old_name: name, new_name: newName}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            showMsg('Link renamed to ' + data.data.new_name, false);
            loadLinks(currentPage);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg('Connection error', true));
}

function expireLink(name) {
    if (!confirm('Are you sure you want to expire this link? This cannot be undone.')) return;

    fetch('/api/v2/user/links/expire/', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({name}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.code === '200') {
            showMsg('Link expired', false);
            loadLinks(currentPage);
        } else {
            showMsg(data.description, true);
        }
    })
    .catch(() => showMsg('Connection error', true));
}

function editSettings(name) {
    window.location.href = '/account/settings/?link=' + encodeURIComponent(name);
}

loadLinks(1);
</script>
</body>
</html>
