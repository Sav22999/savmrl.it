<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/meta.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/translations.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/auth.php"); ?>

    <?php
    auth_start_session();
    $current_user = auth_get_current_user();
    global $title_header;
    $title = "savmrl.it - Link shortener service";

    $lang = detectLanguage();

    $raw_link = "";
    $link_as_parameter = "";
    if (isset($_GET["link"]) && $_GET["link"] !== "") {
        $raw_link = $_GET["link"];
        $link_as_parameter = getGoodString($raw_link);
    }

    $expiry_date = "∞";
    if (isset($_GET["date"]) && $_GET["date"] !== "∞" && $_GET["date"] !== "") {
        $expiry_date = $_GET["date"];
    }
    $expiry_openings = "∞";
    if (isset($_GET["openings"]) && $_GET["openings"] !== "∞" && $_GET["openings"] !== "") {
        $expiry_openings = $_GET["openings"];
    }
    $access_code = "";
    if (isset($_POST["access_code"]) && $_POST["access_code"] !== "∞" && $_POST["access_code"] !== "") {
        $access_code = $_POST["access_code"];
    }
    $redirect_seconds = "";
    if (isset($_GET["delay"]) && $_GET["delay"] !== "" && is_numeric($_GET["delay"])) {
        $redirect_seconds = max(5, min(30, (int)$_GET["delay"]));
    }
    $show_qr = true;
    if (isset($_GET["qr"]) && $_GET["qr"] === "0") {
        $show_qr = false;
    }
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/auth-header.php"); ?>
</header>
<main id="drop-area" class="main-centered">
    <div id="drop-area-overlay">
        <div class="drop-area-overlay--content">
            <p>
                <img src="/alpha/images/release.svg" class="image-release" alt="Drop link"/>
            </p>
            <h1><?php echo t('drag-n-drop', $lang); ?></h1>
        </div>
    </div>

    <div class="horizontal-center">
        <div class="vertical-center-home">
            <div class="vertical-top">
                <h2 class="title-section brilors home-title"><?php echo t('payoff', $lang); ?></h2>
                <p class="home-subtitle"><?php echo t('payoff-desc', $lang); ?></p>
                <div id="info-messages"></div>
            </div>
            <?php
            if (!$current_user): ?>
                <div class="auth-required-container">
                    <div class="auth-required-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 3h2.996C18.2 3 19.3 3 20.15 3.436a3 3 0 011.414 1.414C22 5.7 22 6.8 22 9.004V14.996C22 17.2 22 18.3 21.564 19.15a3 3 0 01-1.414 1.414C19.3 21 18.2 21 15.996 21H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M14 12H2m0 0l3.5-3M2 12l3.5 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <h3><?php echo t('login-required', $lang); ?></h3>
                    <p class="auth-required-desc"><?php echo t('login-required-desc', $lang); ?></p>
                    <div class="auth-required-actions">
                        <a href="/alpha/account/login/" class="btn btn-primary"><?php echo t('login', $lang); ?></a>
                        <a href="/alpha/account/register/" class="btn btn-outline"><?php echo t('signup', $lang); ?></a>
                    </div>
                </div>
            <?php elseif (!$current_user['email_verified']): ?>
                <div class="auth-required-container">
                    <h3><?php echo t('verify-email-required', $lang); ?></h3>
                    <p class="auth-required-desc"><?php echo t('verify-email-required-desc', $lang); ?></p>
                    <div class="auth-required-actions">
                        <button class="btn btn-primary" onclick="resendVerification()"><?php echo t('resend-verification', $lang); ?></button>
                    </div>
                    <div id="resend-message"></div>
                </div>
                <script>
                function resendVerification() {
                    fetch('/api/v2/auth/resend-verification/', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({email: <?php echo json_encode($current_user['email']); ?>}),
                    })
                    .then(r => r.json())
                    .then(data => {
                        const el = document.getElementById('resend-message');
                        el.className = 'info-message';
                        el.textContent = <?php echo json_encode(t('verification-resent', $lang)); ?>;
                    })
                    .catch(() => {
                        const el = document.getElementById('resend-message');
                        el.className = 'error-message';
                        el.textContent = <?php echo json_encode(t('connection-error', $lang)); ?>;
                    });
                }
                </script>
            <?php else:
            $error = false;
            if ($expiry_date !== "∞" && isValidDate($expiry_date) === null) $error = true;
            if ($expiry_openings !== "∞" && isValidNumber($expiry_openings) === null) $error = true;

            if ($link_as_parameter !== "" && !$error) {
                $user_id = (int)$current_user['id'];
                $redirect_sec_val = ($redirect_seconds !== "" && $redirect_seconds > 0) ? $redirect_seconds : null;
                $shortener_code = insertNewRedirect($raw_link, $expiry_openings, $expiry_date, $access_code, $user_id, $redirect_sec_val);
                if ($shortener_code !== "error") {
                    if ($shortener_code === "invalid_url") {
                        $shortener_code = "";
                        $shortened_url = $link_as_parameter;
                    } else $shortened_url = "https://savmrl.it/alpha/r/" . $shortener_code;
                    ?>
                    <p class="text-align-center hidden" id="message"></p>
                    <div class="result-card" id="copy-link-container">
                        <div class="result-success-badge">
                            <svg class="icon-inline" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.5 17l6 6 13-13"/></svg>
                            <?php echo t('link-created', $lang); ?>
                        </div>
                        <div class="input-link-container result-input-container">
                            <input id="link-input-to-copy" class="input-link" type="url"
                                   value="<?php echo $shortened_url; ?>" onkeydown="onkeydown_enter(event)"
                                   oninput="validateName(this)" readonly/>
                            <?php if ($shortener_code !== "") { ?>
                                <input type="button" id="edit-link" onclick="editOrSaveLink(this)">
                            <?php } ?>
                        </div>

                        <div class="result-original-url">
                            <svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 12H20M20 12L14 6M20 12L14 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <a href="<?php echo $link_as_parameter; ?>"><?php echo strlen($link_as_parameter) > 60 ? substr($link_as_parameter, 0, 60) . '...' : $link_as_parameter; ?></a>
                        </div>

                        <?php if ($show_qr): ?>
                        <div class="result-qr-section" id="qr-code-section">
                            <img id="qrcode" alt="QR Code"
                                 src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?php echo urlencode($shortened_url); ?>"/>
                        </div>
                        <?php endif; ?>

                        <div class="result-actions">
                            <input id="copy-link-button" class="button-link" type="button"
                                   value="<?php echo t('copy-link', $lang); ?>"
                                   onclick="copyLink()"/>
                            <div class="result-actions-row">
                                <input id="another-link-button" class="button-link result-btn-secondary" type="button"
                                       value="<?php echo t('generate-another', $lang); ?>"
                                       onclick="location.href='/alpha/'"/>
                                <a href="/alpha/stats/<?php echo $shortener_code; ?>" style="flex:1;">
                                    <input id="see-stats-button" class="button-link result-btn-secondary" type="button"
                                           value="<?php echo t('see-stats', $lang); ?>"/>
                                </a>
                            </div>
                        </div>
                        <div id="additional_params"></div>
                    </div>


                    <script>
                        let global_old_name = "<?php echo $shortener_code; ?>";

                        document.getElementById("link-input-to-copy").oninput = function () {
                            this.value = getValidatedNewName(this.value);
                        }

                        function onkeydown_enter(event) {
                            if (event.key === "Enter") {
                                editOrSaveLink(document.getElementById("edit-link"));
                            }
                        }

                        function onsubmit_newName(old_name, new_name) {
                            let data = {
                                new_name: getValidatedNewName(new_name),
                                old_name: getValidatedNewName(old_name)
                            };

                            fetch('/api/v2/link/edit/', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/json'},
                                body: JSON.stringify(data),
                            })
                                .then(response => response.json())
                                .then(result => {
                                    let messageEl = document.getElementById("message");
                                    messageEl.classList.remove("hidden", "error-message", "info-message");

                                    if (result.code === "200") {
                                        global_old_name = getValidatedNewName(new_name);
                                        messageEl.classList.add("info-message");
                                        messageEl.innerHTML = '<?php echo t('link-renamed', $lang); ?>'.replace('{{source-link}}', result.data.old_name).replace('{{new-link}}', result.data.new_name);
                                        var statsBtn = document.getElementById("see-stats-button");
                                        if (statsBtn && statsBtn.parentElement) {
                                            statsBtn.parentElement.href = "/alpha/stats/" + global_old_name;
                                        }
                                        document.getElementById("link-input-to-copy").value = result.data.short_url;
                                        var qrImg = document.getElementById("qrcode");
                                        if (qrImg) {
                                            qrImg.src = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" + encodeURIComponent(result.data.short_url);
                                        }
                                    } else {
                                        messageEl.classList.add("error-message");
                                        messageEl.innerHTML = result.description;
                                        document.getElementById("link-input-to-copy").value = "https://savmrl.it/alpha/r/" + global_old_name;
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    document.getElementById("link-input-to-copy").value = "https://savmrl.it/alpha/r/" + global_old_name;
                                });
                        }

                        function editOrSaveLink(button) {
                            let editImage = "/alpha/images/edit.svg";
                            let saveImage = "/alpha/images/save.svg";
                            let inputLink = document.getElementById("link-input-to-copy");

                            if (inputLink.readOnly) {
                                button.style.backgroundImage = 'url("' + saveImage + '")';
                                inputLink.value = inputLink.value.substring("https://savmrl.it/alpha/r/".length);
                                inputLink.readOnly = false;
                                inputLink.focus();
                            } else {
                                button.style.backgroundImage = 'url("' + editImage + '")';
                                let new_name = getValidatedNewName(inputLink.value);
                                inputLink.value = inputLink.value.replace("https://savmrl.it/alpha/r/", "");
                                inputLink.value = "https://savmrl.it/alpha/r/" + inputLink.value;
                                inputLink.readOnly = true;
                                inputLink.blur();

                                if (global_old_name !== new_name) {
                                    onsubmit_newName(global_old_name, new_name);
                                }
                            }
                        }

                        function getValidatedNewName(new_name) {
                            let validatedName = new_name.toString().replace(/[^0-9a-zA-Z\-]/g, '');
                            return validatedName.substring(0, 100);
                        }
                    </script>
                    <?php
                } else {
                    $error = true;
                }
            }

            if ($link_as_parameter === "" || $error) {
                $hidden_or_not_class = "hidden-btn";
                if ($link_as_parameter !== "" && filter_var($link_as_parameter, FILTER_VALIDATE_URL)) $hidden_or_not_class = "";
            if ($error) {
                ?>
                <p class="text-align-center error-message">
                    <?php echo t('error-generic', $lang); ?>
                </p>
            <?php
            }
            ?>
                <form class="text-align-center" id="generate-link-form" onsubmit="onsubmit_link(this)">
                    <?php
                    $is_advanced = false;
                    if ($expiry_openings !== "∞" || $expiry_date !== "∞" || $access_code !== "" || $redirect_seconds !== "" || (isset($_GET["advanced"]))) {
                        $is_advanced = true;
                    }
                    ?>
                    <div id="basic-advanced-switcher">
                        <div class="square-20px border-radius-bottom-right-20px"></div>
                        <div class="switcher">
                            <div id="basic-switcher-option" class="option-switcher <?php if (!$is_advanced) echo "selected-option"; ?>"
                                 onclick="changeBasicAdvanced('basic')"><?php echo t('basic', $lang); ?></div>
                            <div id="advanced-switcher-option" class="option-switcher <?php if ($is_advanced) echo "selected-option"; ?>"
                                 onclick="changeBasicAdvanced('advanced')"><?php echo t('advanced', $lang); ?></div>
                        </div>
                        <div class="square-20px border-radius-bottom-left-20px"></div>
                    </div>
                    <div class="input-link-container">
                        <input id="link-input" type="url" class="input-link" placeholder="<?php echo t('insert-link', $lang); ?>"
                               name="link" oninput="linkInput()" value="<?php echo $link_as_parameter; ?>" required autofocus/>
                        <input id="generate-link-button" class="button-link <?php echo $hidden_or_not_class; ?>"
                               type="submit" value=""/>
                    </div>
                    <script>
                        const dropArea = document.getElementById('drop-area');
                        let dragCounter = 0;

                        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                            dropArea.addEventListener(eventName, preventDefaults, false);
                        });

                        function preventDefaults(e) {
                            e.preventDefault();
                            e.stopPropagation();
                        }

                        dropArea.addEventListener('dragenter', () => {
                            dragCounter++;
                            dropArea.classList.add('highlight');
                        }, false);

                        dropArea.addEventListener('dragleave', () => {
                            dragCounter--;
                            if (dragCounter === 0) dropArea.classList.remove('highlight');
                        }, false);

                        dropArea.addEventListener('drop', (e) => {
                            dragCounter = 0;
                            dropArea.classList.remove('highlight');
                            handleDrop(e);
                        }, false);

                        function handleDrop(e) {
                            const dt = e.dataTransfer;
                            let url = null;
                            if (dt.getData('text/uri-list')) url = dt.getData('text/uri-list');
                            else if (dt.getData('text/plain')) {
                                const text = dt.getData('text/plain');
                                if (text.startsWith('http://') || text.startsWith('https://')) url = text;
                            }
                            if (url) displayLink(url);
                        }

                        function displayLink(url) {
                            document.getElementById('link-input').value = url;
                            linkInput();
                        }
                    </script>
                    <div id="div-after-link">
                        <div id="advanced-params" class="advanced-params-container <?php if (!$is_advanced) echo "hidden"; ?>">
                            <div class="advanced-row">
                                <label class="advanced-label" for="opening_expiry"><?php echo t('max-openings', $lang); ?></label>
                                <input class="advanced-input" id="opening_expiry" type="text" min="1" name="openings"
                                       value="<?php echo $expiry_openings; ?>" oninput="validateOpenings(this)"
                                       onblur="setInfinityNumber(this)" onfocus="checkInfinity(this);changeTypeTextToNumber(this)"
                                       placeholder="∞"/>
                            </div>
                            <div class="advanced-row">
                                <label class="advanced-label" for="date_expiry"><?php echo t('expiry-date', $lang); ?></label>
                                <input class="advanced-input" id="date_expiry" type="text" name="date"
                                       value="<?php echo $expiry_date; ?>" oninput="validateDate(this)"
                                       onblur="setInfinityDate(this)" onfocus="checkInfinity(this);changeTypeTextToDate(this)"
                                       placeholder="∞"/>
                            </div>
                            <div class="advanced-row">
                                <label class="advanced-label" for="access_code"><?php echo t('access-code', $lang); ?></label>
                                <input class="advanced-input" id="access_code" type="password" name="access_code"
                                       value="<?php echo $access_code; ?>" placeholder="<?php echo t('optional', $lang); ?>"/>
                            </div>
                            <div class="advanced-row">
                                <label class="advanced-label" for="redirect_delay"><?php echo t('redirect-delay', $lang); ?></label>
                                <input class="advanced-input" id="redirect_delay" type="number" name="delay"
                                       min="5" max="30" value="<?php echo $redirect_seconds; ?>"
                                       placeholder="<?php echo t('optional', $lang); ?>"/>
                            </div>
                            <div class="advanced-row">
                                <label class="advanced-label" for="qr_toggle"><?php echo t('generate-qr', $lang); ?></label>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="qr_toggle" checked />
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        <div class="terms-checkbox-row">
                            <input type="checkbox" id="accept_terms" required />
                            <label for="accept_terms"><?php echo t('accept-terms', $lang); ?></label>
                        </div>
                    </div>
                </form>

                <script>
                    var acInput = document.getElementById("access_code");
                    if (acInput) {
                        acInput.onfocus = function () { this.type = "text"; }
                        acInput.onblur = function () { this.type = "password"; }
                    }

                    function onsubmit_link(form) {
                        var termsCheckbox = document.getElementById('accept_terms');
                        if (!termsCheckbox.checked) {
                            alert('<?php echo addslashes(t('must-accept-terms', $lang)); ?>');
                            return false;
                        }

                        const baseUrl = "/alpha/";
                        const openingExpiry = document.getElementById('opening_expiry');
                        const dateExpiry = document.getElementById('date_expiry');
                        const link = document.getElementById('link-input');
                        const qrToggle = document.getElementById('qr_toggle');
                        const redirectDelay = document.getElementById('redirect_delay');
                        const qrParam = (qrToggle && !qrToggle.checked) ? '&qr=0' : '';
                        const delayParam = (redirectDelay && redirectDelay.value && parseInt(redirectDelay.value) >= 5) ? '&delay=' + redirectDelay.value : '';

                        form.action = baseUrl + '?openings=' + encodeURIComponent(openingExpiry.value) + '&date=' + encodeURIComponent(dateExpiry.value) + '&link=' + encodeURIComponent(link.value) + qrParam + delayParam;
                        openingExpiry.name = "";
                        dateExpiry.name = "";
                        link.name = "";
                        redirectDelay.name = "";
                        form.method = "post";
                    }
                </script>
                <?php
            }
            endif; ?>
            <div class="vertical-bottom"></div>
        </div>
    </div>

    <script>
        function validLink(link) {
            try {
                const url = new URL(link);
                return url.protocol === 'http:' || url.protocol === 'https:';
            } catch { return false; }
        }

        function linkInput() {
            let linkInput = document.getElementById("link-input");
            let generateButton = document.getElementById("generate-link-button");
            if (linkInput && linkInput.value.trim() !== "" && validLink(linkInput.value)) {
                if (generateButton) generateButton.classList.remove("hidden-btn");
            } else {
                if (generateButton) generateButton.classList.add("hidden-btn");
            }
        }

        function copyLink() {
            let linkInput = document.getElementById('link-input-to-copy');
            let copyButton = document.getElementById('copy-link-button');
            let textToCopy = linkInput.value;

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(textToCopy).then(function () {
                    showCopyFeedback(copyButton);
                }).catch(function () { fallbackCopy(linkInput, copyButton); });
            } else { fallbackCopy(linkInput, copyButton); }
        }

        function fallbackCopy(inputEl, buttonEl) {
            inputEl.select();
            document.execCommand('copy');
            inputEl.setSelectionRange(0, 0);
            showCopyFeedback(buttonEl);
        }

        function showCopyFeedback(button) {
            let previousText = button.value;
            button.value = "<?php echo addslashes(t('link-copied', $lang)); ?>";
            setTimeout(function () { button.value = previousText; }, 3000);
        }

        function validateOpenings(input) {
            input.value = input.value.replace(/[^0-9∞]/g, '');
        }

        function setInfinityNumber(input, force) {
            let inputValue = input.value;
            if (input.type === "number") input.type = "text";
            inputValue = inputValue.replace(/[^0-9∞]/g, '');
            if (inputValue === '' || isNaN(inputValue) || parseInt(inputValue) < 1 || force) input.value = '∞';
            inputValue = input.value;
            if (!isNaN(inputValue) && parseInt(inputValue) >= 1) input.value = parseInt(inputValue);
        }

        function changeTypeTextToNumber(input) {
            if (input.type === "text") input.type = "number";
            input.min = 1;
        }

        function validateDate(input) {}

        function setInfinityDate(input, force) {
            let inputValue = input.value;
            if (input.type === "date") input.type = "text";
            if (inputValue === '' || (inputValue !== '∞' && !isValidDateJS(inputValue)) || force) input.value = '∞';
        }

        function isValidDateJS(value) { return !isNaN(Date.parse(value)); }

        function checkInfinity(input) {
            if (input.value === "∞") input.value = "";
        }

        function changeTypeTextToDate(input) {
            if (input.type === "text") input.type = "date";
            input.min = new Date().toISOString().split('T')[0];
        }

        function changeBasicAdvanced(status) {
            let basic = document.getElementById("basic-switcher-option");
            let advanced = document.getElementById("advanced-switcher-option");
            basic.classList.remove("selected-option");
            advanced.classList.remove("selected-option");
            if (status === "basic") {
                basic.classList.add("selected-option");
                showHideAdvanced("hide");
            } else {
                advanced.classList.add("selected-option");
                showHideAdvanced("show");
            }
        }

        function showHideAdvanced(status) {
            let advancedContainer = document.getElementById("advanced-params");
            if (status === "show") {
                if (advancedContainer) advancedContainer.classList.remove("hidden");
            } else {
                setInfinityNumber(document.getElementById("opening_expiry"), true);
                setInfinityDate(document.getElementById("date_expiry"), true);
                if (advancedContainer) advancedContainer.classList.add("hidden");
            }
        }

        linkInput();
    </script>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/footer.php"); ?>
</footer>

</body>
</html>
