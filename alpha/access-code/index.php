<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/meta.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/translations.php"); ?>

    <?php
    global $title_header, $seconds;
    $lang = detectLanguage();

    $name = null;
    if (isset($_POST["name"]) && $_POST["name"] !== "") $name = $_POST["name"];

    $access_code = null;
    if (isset($_POST["access_code"]) && $_POST["access_code"] !== "") {
        $access_code = $_POST["access_code"];
    }

    $redirect_url = "invalid";
    $linkData = null;
    if ($access_code !== null && $name !== null) {
        $redirect_url = getUrlFromName($name, $access_code, false, $linkData);
    }

    if ($access_code === null || $name === null) {
        $redirect_url = "?";
    }

    $errors = array("not_exists", "invalid", "access_code_required", "access_code_wrong", "reported", "?");
    $title = !in_array($redirect_url, $errors) ? (t('redirecting', $lang) . " - " . $redirect_url) : "savmrl.it";

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
            </div>
            <?php
        } else {
            if ($redirect_url !== "access_code_wrong" && $redirect_url !== "access_code_required") {
                redirectTo(false, "/alpha/", 0);
            }
            ?>
            <h2 class="title-section"><?php echo t('access-code-required', $lang); ?></h2>
            <p class="horizontal-center-p"><?php echo t('access-code-desc', $lang); ?></p>
            <div class="big-space"></div>
            <br>
            <?php if ($redirect_url === "access_code_wrong") { ?>
                <p class="text-align-center error-message"><?php echo t('access-code-wrong', $lang); ?></p>
            <?php } ?>
            <form method="post" action="/alpha/access-code/" onsubmit="onsubmit_link(this);">
                <p class="text-align-center">
                    <input class="advanced-input" id="access-code" type="password" name="access_code"
                           placeholder="<?php echo t('access-code-placeholder', $lang); ?>" required style="width:200px;display:inline-block"/>
                    <input id="go-to-link-button" class="button-link" type="submit" value="<?php echo t('go-to-link', $lang); ?>" style="width:auto;display:inline-block;margin-left:8px"/>
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
                    nameElement.value = "<?php echo htmlspecialchars($name); ?>";
                    form.appendChild(nameElement);
                }
            </script>
        <?php } ?>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/footer.php"); ?>
</footer>

</body>
</html>
