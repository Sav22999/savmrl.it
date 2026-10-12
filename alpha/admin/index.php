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
    <title>Admin Dashboard — savmrl.it</title>
</head>
<body class="admin-mode">
<header>
    <?php echo $title_header; ?>
    <div class="auth-nav">
        <span class="auth-nav-link admin-badge"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 15c-4 0-7 2-7 5h14c0-3-3-5-7-5z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.5"/><path d="M17 3l1 2 2 .5-1.5 1.5.5 2-2-1-2 1 .5-2L14 5.5 16 5l1-2z" fill="currentColor" stroke="currentColor" stroke-width="0.5"/></svg> <?php echo htmlspecialchars($admin['username']); ?></span>
        <a href="/alpha/admin/logout/" class="auth-nav-link auth-logout-btn"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 3H6.004C5.8 3 4.7 3 3.85 3.436a3 3 0 00-1.414 1.414C2 5.7 2 6.8 2 9.004V14.996C2 17.2 2 18.3 2.436 19.15a3 3 0 001.414 1.414C4.7 21 5.8 21 8.004 21H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M12 12h10m0 0l-3.5-3M22 12l-3.5 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg> <?php echo t('logout', $lang); ?></a>
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
                <div class="admin-stat-card">
                    <div class="admin-stat-value" id="stat-user-reported">-</div>
                    <div class="admin-stat-label">User Reports</div>
                </div>
            </div>

            <div class="admin-nav-grid">
                <a href="/alpha/admin/links/" class="admin-nav-card">
                    <svg class="nav-card-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    <h3>Manage Links</h3>
                    <p>Search, block, and unblock links</p>
                </a>
                <a href="/alpha/admin/users/" class="admin-nav-card">
                    <svg class="nav-card-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/><path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    <h3>Manage Users</h3>
                    <p>View users, toggle pro features, block accounts</p>
                </a>
            </div>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/footer.php"); ?>
</footer>

<script>
fetch('/api/v2/admin/stats/')
    .then(r => r.json())
    .then(data => {
        if (data.code !== '200') return;
        const s = data.data;
        function fmtNum(n) { return String(Number(n)).replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
        document.getElementById('stat-total-links').textContent = fmtNum(s.total_links);
        document.getElementById('stat-total-clicks').textContent = fmtNum(s.total_clicks);
        document.getElementById('stat-total-users').textContent = fmtNum(s.total_users);
        document.getElementById('stat-reported').textContent = fmtNum(s.reported_links);
        document.getElementById('stat-blocked').textContent = fmtNum(s.blocked_links);
        document.getElementById('stat-user-reported').textContent = fmtNum(s.user_reported_links || 0);
    })
    .catch(() => {});
</script>
</body>
</html>
