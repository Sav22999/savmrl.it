<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header-alpha.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta-alpha.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/translations.php"); ?>

    <?php
    global $title_header, $seconds;
    $lang = detectLanguage();

    $name = substr($_SERVER['REQUEST_URI'], strlen('/alpha/r/'));
    $name = strtok($name, '?');

    $linkData = null;
    $redirect_url = getUrlFromName($name, false, false, $linkData);
    $title = t('redirecting', $lang) . " - " . $redirect_url;

    $errors = array("not_exists", "invalid", "access_code_required", "access_code_wrong", "reported", "admin_blocked", "?");

    $redirect_delay = 0;
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

<main>
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
                    <script>
                    (function() {
                        var remaining = <?php echo $redirect_delay; ?>;
                        var url = <?php echo json_encode($redirect_url); ?>;
                        var el = document.getElementById('countdown');
                        var interval = setInterval(function() {
                            remaining--;
                            el.textContent = remaining;
                            if (remaining <= 0) {
                                clearInterval(interval);
                                window.location.href = url;
                            }
                        }, 1000);
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

                switch ($redirect_url) {
                    case "not_exists":
                        $title = "savmrl.it - Page not exists";
                        $title_section = "Link doesn't exist or has expired";
                        $description = "The shortened link you're looking for doesn't exist, or it's expired.<br>Be sure the shortened link you used is correct, is still valid<sup>*</sup> or try again.";
                        $go_to = "/alpha/";
                        $seconds = 10;
                        break;

                    case "invalid":
                        $title = "savmrl.it - Invalid";
                        $title_section = "Invalid link";
                        $description = "The shortened link you provided is <b>not</b> valid.<br>Be sure the shortened link you used is correct or try again.";
                        $go_to = "/alpha/";
                        $seconds = 10;
                        break;

                    default:
                        $go_to = "/alpha/";
                        $seconds = 0;
                }
                ?>
                <title><?php echo $title; ?></title>
                <h2 class="title-section"><?php echo $title_section; ?></h2>
                <div class="big-space"></div>
                <br>
                <p class="horizontal-center-p">
                    <?php echo $description; ?>
                </p>
                <?php
                redirectTo(false, $go_to, $seconds);

            } else if (in_array($redirect_url, $errors_type_2)) {
                $title = "savmrl.it - Access code required";
                $title_section = "Access code required";
                $description = "To get the link, please insert the access code";
                ?>
                <title><?php echo $title; ?></title>
                <h2 class="title-section"><?php echo $title_section; ?></h2>
                <p class="horizontal-center-p"><?php echo $description; ?></p>
                <div class="big-space"></div>
                <br>
                <form method="post" action="/access-code/" onsubmit="onsubmit_link(this);">
                    <p class="text-align-center">
                        <input class="advanced-input" id="access-code" type="password" name="access_code"
                               placeholder="Digit the access code" required style="width:200px;display:inline-block"/>
                        <input id="go-to-link-button" class="button-link" type="submit" value="Go to the link" style="width:auto;display:inline-block;margin-left:8px"/>
                    </p>
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
                <?php
            } else if (in_array($redirect_url, $errors_type_3)) {
                if ($redirect_url === "admin_blocked") {
                    $title = "savmrl.it - Link blocked";
                    $title_section = "Link blocked by an administrator";
                    $description = t('error-admin-blocked', $lang);
                } else {
                    $title = "savmrl.it - Link blocked";
                    $title_section = "Link blocked because reported as unsafe";
                    $description = "This link has been reported as unsafe, and now it's blocked!<br>If you desire, you can contact me to get more information.";
                }
                $go_to = "/alpha/";
                $seconds = 10;
                ?>
                <title><?php echo $title; ?></title>
                <h2 class="title-section"><?php echo $title_section; ?></h2>
                <div class="big-space"></div>
                <br>
                <p class="horizontal-center-p"><?php echo $description; ?></p>
                <?php
                redirectTo(false, $go_to, $seconds);
            }
        }
        ?>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer-alpha.php"); ?>
</footer>

</body>
</html>
