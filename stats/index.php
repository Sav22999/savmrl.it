<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/include/meta.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/include/translations.php"); ?>

    <?php
    global $title_header, $seconds;
    $lang = detectLanguage();

    $name = substr($_SERVER['REQUEST_URI'], strlen('/stats/'));
    $name = strtok($name, '?');
    if (substr($name, 0, 6) === "?code=") {
        $name = substr($name, 6);
    }

    $has_name = ($name !== "" && $name !== false);
    $n_clicks = null;
    $redirect_link = null;
    $detailed = null;
    if ($has_name) {
        $n_clicks = getStatistics($name);
        $redirect_link = getUrlFromName($name);
        $detailed = getDetailedStatistics($name);
    }
    $title = "savmrl.it - " . t('statistics', $lang);
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/include/auth-header.php"); ?>
</header>
<main<?php if (!$has_name) echo ' class="main-centered"'; ?>>
    <div class="horizontal-center">
        <?php if (!$has_name): ?>
            <div class="auth-form-container" style="text-align:center;">
                <h2 class="title-section brilors"><?php echo t('statistics', $lang); ?></h2>
                <p style="color:var(--color-text-secondary);margin:8px 0 20px;"><?php echo t('stats-search-desc', $lang); ?></p>
                <form class="auth-form" onsubmit="goToStats(event)">
                    <input type="text" id="stats-name" class="input-link" placeholder="<?php echo t('stats-search-placeholder', $lang); ?>" required style="text-align:center;" />
                    <input type="submit" class="button-link" value="<?php echo t('see-stats', $lang); ?>" />
                </form>
            </div>
            <script>
            function goToStats(e) {
                e.preventDefault();
                var name = document.getElementById('stats-name').value.trim();
                if (name) window.location.href = '/stats/' + encodeURIComponent(name);
            }
            </script>
        <?php elseif ($n_clicks === "not_exists" && $redirect_link === "not_exists"): ?>
            <h2 class="title-section"><?php echo t('link-not-exists', $lang); ?></h2>
            <div class="big-space"></div>
            <p class="horizontal-center-p"><?php echo t('link-not-exists-desc', $lang); ?></p>
            <p class="text-align-center" style="margin-top:16px;">
                <a href="/stats/" class="btn btn-outline"><?php echo t('stats-search-another', $lang); ?></a>
            </p>
        <?php elseif ($n_clicks === "invalid" && $redirect_link === "invalid"): ?>
            <h2 class="title-section"><?php echo t('invalid-link', $lang); ?></h2>
            <div class="big-space"></div>
            <p class="horizontal-center-p"><?php echo t('invalid-link-desc', $lang); ?></p>
            <p class="text-align-center" style="margin-top:16px;">
                <a href="/stats/" class="btn btn-outline"><?php echo t('stats-search-another', $lang); ?></a>
            </p>
        <?php elseif ($n_clicks === "?" && $redirect_link === "?"): ?>
            <?php redirectTo(false, "/stats/", 0); ?>
        <?php elseif ($detailed !== null): ?>

            <h2 class="title-section brilors"><?php echo t('statistics', $lang); ?></h2>

            <div class="stats-detail-card">
                <div class="stats-link-header">
                    <div class="stats-short-url-row">
                        <a href="<?php echo "https://savmrl.it/r/" . htmlspecialchars($name); ?>" class="stats-short-url">savmrl.it/r/<?php echo htmlspecialchars($name); ?></a>
                        <button type="button" class="stats-copy-btn" onclick="copyStatsLink(this)" title="<?php echo t('copy-link', $lang); ?>">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="5.5" y="5.5" width="8" height="8" rx="1.5"/>
                                <path d="M10.5 5.5V3.5A1.5 1.5 0 009 2H3.5A1.5 1.5 0 002 3.5V9a1.5 1.5 0 001.5 1.5h2"/>
                            </svg>
                        </button>
                    </div>
                    <?php
                    $status = $detailed['status'];
                    $badge_class = 'badge-active';
                    $badge_text = t('badge-active', $lang);
                    if ($status === 'blocked') { $badge_class = 'badge-blocked'; $badge_text = t('badge-blocked', $lang); }
                    elseif ($status === 'reported') { $badge_class = 'badge-reported'; $badge_text = t('badge-reported', $lang); }
                    elseif ($status === 'expired') { $badge_class = 'badge-expired'; $badge_text = t('badge-expired', $lang); }
                    ?>
                    <span class="badge <?php echo $badge_class; ?>"><?php echo $badge_text; ?></span>
                </div>

                <div class="stats-metrics-grid">
                    <div class="stats-metric-card">
                        <div class="stats-metric-value"><?php echo number_format($detailed['total_clicks'], 0, ',', '.'); ?></div>
                        <div class="stats-metric-label"><?php echo t('stats-total-clicks', $lang); ?></div>
                    </div>
                    <div class="stats-metric-card">
                        <div class="stats-metric-value"><?php echo date('d M Y', strtotime($detailed['created_at'])); ?></div>
                        <div class="stats-metric-label"><?php echo t('stats-created', $lang); ?></div>
                    </div>
                </div>

                <?php
                $daily = $detailed['daily_clicks'];
                $max_daily = max(1, max($daily));
                $week_total = array_sum($daily);
                ?>
                <div class="stats-chart-section">
                    <div class="stats-chart-header">
                        <span class="stats-chart-title"><?php echo t('stats-last-7-days', $lang); ?></span>
                        <span class="stats-chart-total"><?php echo number_format($week_total, 0, ',', '.'); ?> <?php echo t('clicks', $lang); ?></span>
                    </div>
                    <div class="stats-chart">
                        <?php foreach ($daily as $day => $clicks): ?>
                            <?php $pct = round(($clicks / $max_daily) * 100); ?>
                            <div class="stats-chart-col">
                                <div class="stats-chart-count"><?php echo $clicks; ?></div>
                                <div class="stats-chart-bar-wrap">
                                    <div class="stats-chart-bar" style="height:<?php echo max($pct, 3); ?>%"></div>
                                </div>
                                <div class="stats-chart-day"><?php echo date('D', strtotime($day)); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ($detailed['has_expiry'] || $detailed['has_limit'] || $detailed['redirect_seconds']): ?>
                    <div class="stats-extra-info">
                        <?php if ($detailed['has_expiry']): ?>
                            <div class="stats-extra-item">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="8" r="6"/><path d="M8 5v3l2 2"/></svg>
                                <?php echo t('expires', $lang); ?> <?php echo date('d M Y', strtotime($detailed['expiry_date'])); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($detailed['has_limit']): ?>
                            <div class="stats-extra-item">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 8h12"/><path d="M8 2v12"/></svg>
                                <?php echo t('max-label', $lang); ?> <?php echo $detailed['limit_times']; ?> <?php echo t('openings', $lang); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($detailed['redirect_seconds']): ?>
                            <div class="stats-extra-item">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="8" r="6"/><path d="M8 5v3"/><circle cx="8" cy="11" r="0.5" fill="currentColor"/></svg>
                                <?php echo $detailed['redirect_seconds']; ?>s <?php echo t('redirect-delay-info', $lang); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <p class="text-align-center" style="margin-top:20px;">
                <a href="/stats/" class="btn btn-outline"><?php echo t('stats-search-another', $lang); ?></a>
            </p>

            <script>
            function copyStatsLink(btn) {
                var url = 'https://savmrl.it/r/<?php echo htmlspecialchars($name, ENT_QUOTES); ?>';
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(url).then(function() { showCopied(btn); });
                } else {
                    var t = document.createElement('textarea');
                    t.value = url;
                    document.body.appendChild(t);
                    t.select();
                    document.execCommand('copy');
                    document.body.removeChild(t);
                    showCopied(btn);
                }
            }
            function showCopied(btn) {
                btn.classList.add('copied');
                setTimeout(function() { btn.classList.remove('copied'); }, 1500);
            }
            </script>

        <?php else: ?>
            <h2 class="title-section brilors"><?php echo t('statistics', $lang); ?></h2>
            <p class="text-align-center" style="margin-top:8px;">
                <a href="<?php echo "https://savmrl.it/r/" . htmlspecialchars($name); ?>"><?php echo "savmrl.it/r/" . htmlspecialchars($name); ?></a>
            </p>
            <h1 style="font-size:3em;margin:24px 0 8px;color:var(--color-primary);"><?php echo number_format((int)$n_clicks, 0, ',', '.'); ?></h1>
            <p class="text-align-center" style="color:var(--color-text-secondary);"><?php echo t('stats-description', $lang); ?></p>
            <p class="text-align-center" style="margin-top:16px;">
                <a href="/stats/" class="btn btn-outline"><?php echo t('stats-search-another', $lang); ?></a>
            </p>
        <?php endif; ?>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php"); ?>
</footer>

</body>
</html>
