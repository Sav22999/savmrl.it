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
    <title>Admin Dashboard — savmrl.it</title>
</head>
<body>
<header>
    <?php echo $title_header; ?>
    <div class="auth-nav">
        <span class="auth-nav-link admin-badge">Admin: <?php echo htmlspecialchars($admin['username']); ?></span>
        <a href="/alpha/admin/logout/" class="auth-nav-link auth-logout-btn"><?php echo t('logout', $lang); ?></a>
    </div>
</header>
<main>
    <div class="horizontal-center">
        <div class="dashboard-container admin-dashboard">
            <h2 class="title-section brilors">Admin Dashboard</h2>

            <div class="admin-stats-grid" id="stats-grid">
                <div class="admin-stat-card">
                    <div class="admin-stat-value" id="stat-total-links">-</div>
                    <div class="admin-stat-label">Total Links</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-value" id="stat-total-clicks">-</div>
                    <div class="admin-stat-label">Total Clicks</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-value" id="stat-total-users">-</div>
                    <div class="admin-stat-label">Users</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-value" id="stat-reported">-</div>
                    <div class="admin-stat-label">Reported</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-value" id="stat-blocked">-</div>
                    <div class="admin-stat-label">Blocked</div>
                </div>
            </div>

            <div class="admin-nav-grid">
                <a href="/alpha/admin/links/" class="admin-nav-card">
                    <h3>Manage Links</h3>
                    <p>Search, block, and unblock links</p>
                </a>
                <a href="/alpha/admin/users/" class="admin-nav-card">
                    <h3>Manage Users</h3>
                    <p>View users, toggle pro features, block accounts</p>
                </a>
            </div>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer-alpha.php"); ?>
</footer>

<script>
fetch('/api/v2/admin/stats/')
    .then(r => r.json())
    .then(data => {
        if (data.code !== '200') return;
        const s = data.data;
        document.getElementById('stat-total-links').textContent = s.total_links;
        document.getElementById('stat-total-clicks').textContent = s.total_clicks;
        document.getElementById('stat-total-users').textContent = s.total_users;
        document.getElementById('stat-reported').textContent = s.reported_links;
        document.getElementById('stat-blocked').textContent = s.blocked_links;
    })
    .catch(() => {});
</script>
</body>
</html>
