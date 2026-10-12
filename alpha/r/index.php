<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/meta.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/translations.php"); ?>

    <?php
    global $title_header, $seconds;
    $lang = detectLanguage();

    $name = substr($_SERVER['REQUEST_URI'], strlen('/alpha/r/'));
    $name = strtok($name, '?');

    $linkData = null;
    $redirect_url = getUrlFromName($name, false, false, $linkData);
    $title = t('redirecting', $lang) . " - " . $redirect_url;

    $errors = array("not_exists", "invalid", "access_code_required", "access_code_wrong", "reported", "admin_blocked", "?");

    $redirect_delay = 5;
    if ($linkData && isset($linkData['redirect_seconds']) && $linkData['redirect_seconds'] > 0) {
        $redirect_delay = (int)$linkData['redirect_seconds'];
    }
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
</header>

<main class="main-centered">
    <div class="horizontal-center">
        <?php if (!in_array($redirect_url, $errors)) { ?>
            <div class="redirect-container">
                <h2 class="title-section brilors"><?php echo t('redirecting', $lang); ?></h2>
                <p class="redirect-url"><?php echo htmlspecialchars($redirect_url); ?></p>

                <?php if ($redirect_delay > 0): ?>
                    <div class="redirect-countdown" id="countdown"><?php echo $redirect_delay; ?></div>
                    <p style="font-size:0.85em;color:var(--color-text-secondary)">
                        <?php echo $redirect_delay . ' ' . t('redirect-delay-info', $lang); ?>
                    </p>
                    <p id="redirect-paused-msg" style="display:none;font-size:0.85em;color:var(--color-text-secondary);margin-top:4px;">
                        <?php echo t('redirect-paused', $lang); ?>
                    </p>
                    <script>
                    (function() {
                        var remaining = <?php echo $redirect_delay; ?>;
                        var url = <?php echo json_encode($redirect_url); ?>;
                        var el = document.getElementById('countdown');
                        var pausedMsg = document.getElementById('redirect-paused-msg');
                        var delayInfo = el.nextElementSibling;
                        var paused = false;
                        var pauseSvg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="6" y="4" width="4" height="16" rx="1" fill="currentColor"/><rect x="14" y="4" width="4" height="16" rx="1" fill="currentColor"/></svg>';
                        var interval = setInterval(function() {
                            if (paused) return;
                            remaining--;
                            el.textContent = remaining;
                            if (remaining <= 0) {
                                clearInterval(interval);
                                window.location.href = url;
                            }
                        }, 1000);
                        document.addEventListener('DOMContentLoaded', function() {
                            var details = document.querySelector('.report-section details');
                            if (details) {
                                details.addEventListener('toggle', function() {
                                    if (this.open) {
                                        paused = true;
                                        el.innerHTML = pauseSvg;
                                        if (delayInfo) delayInfo.style.display = 'none';
                                        pausedMsg.style.display = '';
                                    } else {
                                        paused = false;
                                        el.textContent = remaining;
                                        if (delayInfo) delayInfo.style.display = '';
                                        pausedMsg.style.display = 'none';
                                    }
                                });
                            }
                        });
                    })();
                    </script>
                <?php else: ?>
                    <?php redirectTo($name, $redirect_url, $seconds); ?>
                <?php endif; ?>

                <div class="redirect-actions">
                    <a href="<?php echo htmlspecialchars($redirect_url); ?>">
                        <?php echo t('redirect-manual', $lang); ?>
                    </a>
                </div>

                <div class="report-section">
                    <details>
                        <summary><?php echo t('report-link', $lang); ?></summary>
                        <p style="font-size:0.85em;color:var(--color-text-secondary);margin-top:8px;"><?php echo t('report-reason', $lang); ?></p>
                        <div class="report-options">
                            <button class="report-btn" onclick="reportLink('phishing')"><?php echo t('report-phishing', $lang); ?></button>
                            <button class="report-btn" onclick="reportLink('spam')"><?php echo t('report-spam', $lang); ?></button>
                            <button class="report-btn" onclick="reportLink('illegal')"><?php echo t('report-illegal', $lang); ?></button>
                            <button class="report-btn" onclick="reportLink('other')"><?php echo t('report-other', $lang); ?></button>
                        </div>
                        <div id="report-message" style="margin-top:8px;"></div>
                    </details>
                </div>
            </div>

            <script>
            function reportLink(reason) {
                var btns = document.querySelectorAll('.report-btn');
                btns.forEach(function(b) { b.disabled = true; });

                fetch('/api/v2/link/report/', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({name: <?php echo json_encode($name); ?>, reason: reason}),
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var msgEl = document.getElementById('report-message');
                    if (data.code === '200') {
                        msgEl.className = 'info-message';
                        msgEl.textContent = <?php echo json_encode(t('report-success', $lang)); ?>;
                    } else {
                        msgEl.className = 'error-message';
                        msgEl.textContent = data.description || <?php echo json_encode(t('report-error', $lang)); ?>;
                        btns.forEach(function(b) { b.disabled = false; });
                    }
                })
                .catch(function() {
                    var msgEl = document.getElementById('report-message');
                    msgEl.className = 'error-message';
                    msgEl.textContent = <?php echo json_encode(t('report-error', $lang)); ?>;
                    btns.forEach(function(b) { b.disabled = false; });
                });
            }
            </script>
        <?php
        } else {
            $errors_type_1 = array("not_exists", "invalid", "?");
            $errors_type_2 = array("access_code_required", "access_code_wrong");
            $errors_type_3 = array("reported", "admin_blocked");

            if (in_array($redirect_url, $errors_type_1)) {
                $title = "";
                $title_section = "";
                $description = "";
                $go_to = "/alpha/";
                $seconds = 0;
                $illustration = "not-found";

                switch ($redirect_url) {
                    case "not_exists":
                        $title = "savmrl.it - Page not exists";
                        $title_section = t('error-not-exists-title', $lang);
                        $description = t('error-not-exists-desc', $lang);
                        $illustration = "expired";
                        $go_to = "/alpha/";
                        $seconds = 10;
                        break;

                    case "invalid":
                        $title = "savmrl.it - Invalid";
                        $title_section = t('error-invalid-title', $lang);
                        $description = t('error-invalid-desc', $lang);
                        $illustration = "not-found";
                        $go_to = "/alpha/";
                        $seconds = 10;
                        break;

                    default:
                        $go_to = "/alpha/";
                        $seconds = 0;
                }
                ?>
                <title><?php echo $title; ?></title>
                <div class="error-page">
                    <?php include($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/error-illustration.php"); ?>
                    <h2 class="title-section"><?php echo $title_section; ?></h2>
                    <p class="error-page-desc"><?php echo $description; ?></p>
                    <a href="/alpha/" class="button-link" style="margin-top:16px;width:auto;display:inline-block;padding:10px 24px;"><?php echo t('go-home', $lang); ?></a>
                </div>
                <?php

            } else if (in_array($redirect_url, $errors_type_2)) {
                $title = "savmrl.it - Access code required";
                $title_section = t('error-access-code-title', $lang);
                $description = t('error-access-code-desc', $lang);
                $illustration = "locked";
                ?>
                <title><?php echo $title; ?></title>
                <div class="error-page">
                    <?php include($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/error-illustration.php"); ?>
                    <h2 class="title-section"><?php echo $title_section; ?></h2>
                    <p class="error-page-desc"><?php echo $description; ?></p>
                    <form method="post" action="/alpha/access-code/" onsubmit="onsubmit_link(this);" style="margin-top:16px;">
                        <div style="display:flex;gap:8px;justify-content:center;align-items:center;flex-wrap:wrap;">
                            <input class="input-link" id="access-code" type="password" name="access_code"
                                   placeholder="<?php echo t('access-code-placeholder', $lang); ?>" required style="width:200px;flex-shrink:0;"/>
                            <input id="go-to-link-button" class="button-link" type="submit" value="<?php echo t('go-to-link', $lang); ?>" style="width:auto;margin:0;padding:10px 20px;"/>
                        </div>
                    </form>
                    <script>
                        document.getElementById("access-code").onfocus = function() { this.type = "text"; }
                        document.getElementById("access-code").onblur = function() { this.type = "password"; }
                        function onsubmit_link(form) {
                            var nameElement = document.createElement("input");
                            nameElement.type = "text";
                            nameElement.classList.add("hidden");
                            nameElement.name = "name";
                            nameElement.value = "<?php echo $name; ?>";
                            form.appendChild(nameElement);
                        }
                    </script>
                </div>
                <?php
            } else if (in_array($redirect_url, $errors_type_3)) {
                if ($redirect_url === "admin_blocked") {
                    $title = "savmrl.it - Link blocked";
                    $title_section = t('error-blocked-title', $lang);
                    $description = t('error-admin-blocked', $lang);
                    $illustration = "blocked";
                } else {
                    $title = "savmrl.it - Link blocked";
                    $title_section = t('error-reported-title', $lang);
                    $description = t('error-reported-desc', $lang);
                    $illustration = "warning";
                }
                ?>
                <title><?php echo $title; ?></title>
                <div class="error-page">
                    <?php include($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/error-illustration.php"); ?>
                    <h2 class="title-section"><?php echo $title_section; ?></h2>
                    <p class="error-page-desc"><?php echo $description; ?></p>
                    <a href="/alpha/" class="button-link" style="margin-top:16px;width:auto;display:inline-block;padding:10px 24px;"><?php echo t('go-home', $lang); ?></a>
                </div>
                <?php
            }
        }
        ?>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/footer.php"); ?>
</footer>

</body>
</html>
