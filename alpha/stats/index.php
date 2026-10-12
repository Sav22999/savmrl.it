<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/meta.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/translations.php"); ?>

    <?php
    global $title_header, $seconds;
    $lang = detectLanguage();

    $name = substr($_SERVER['REQUEST_URI'], strlen('/alpha/stats/'));
    $name = strtok($name, '?');
    if (substr($name, 0, 6) === "?code=") {
        $name = substr($name, 6);
    }

    $has_name = ($name !== "" && $name !== false);
    $n_clicks = null;
    $redirect_link = null;
    if ($has_name) {
        $n_clicks = getStatistics($name);
        $redirect_link = getUrlFromName($name);
    }
    $title = "savmrl.it - " . t('statistics', $lang);
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/auth-header.php"); ?>
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
                if (name) window.location.href = '/alpha/stats/' + encodeURIComponent(name);
            }
            </script>
        <?php elseif ($n_clicks === "not_exists" && $redirect_link === "not_exists"): ?>
            <h2 class="title-section"><?php echo t('link-not-exists', $lang); ?></h2>
            <div class="big-space"></div>
            <p class="horizontal-center-p"><?php echo t('link-not-exists-desc', $lang); ?></p>
            <p class="text-align-center" style="margin-top:16px;">
                <a href="/alpha/stats/" class="btn btn-outline"><?php echo t('stats-search-another', $lang); ?></a>
            </p>
        <?php elseif ($n_clicks === "invalid" && $redirect_link === "invalid"): ?>
            <h2 class="title-section"><?php echo t('invalid-link', $lang); ?></h2>
            <div class="big-space"></div>
            <p class="horizontal-center-p"><?php echo t('invalid-link-desc', $lang); ?></p>
            <p class="text-align-center" style="margin-top:16px;">
                <a href="/alpha/stats/" class="btn btn-outline"><?php echo t('stats-search-another', $lang); ?></a>
            </p>
        <?php elseif ($n_clicks === "?" && $redirect_link === "?"): ?>
            <?php redirectTo(false, "/alpha/stats/", 0); ?>
        <?php else: ?>
            <h2 class="title-section brilors"><?php echo t('statistics', $lang); ?></h2>
            <p class="text-align-center" style="margin-top:8px;">
                <a href="<?php echo "https://savmrl.it/alpha/r/" . htmlspecialchars($name); ?>"><?php echo "savmrl.it/alpha/r/" . htmlspecialchars($name); ?></a>
            </p>
            <h1 style="font-size:3em;margin:24px 0 8px;color:var(--color-primary);"><?php echo number_format((int)$n_clicks, 0, ',', '.'); ?></h1>
            <p class="text-align-center" style="color:var(--color-text-secondary);"><?php echo t('stats-description', $lang); ?></p>
            <p class="text-align-center" style="margin-top:16px;">
                <a href="/alpha/stats/" class="btn btn-outline"><?php echo t('stats-search-another', $lang); ?></a>
            </p>
        <?php endif; ?>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/footer.php"); ?>
</footer>

</body>
</html>
