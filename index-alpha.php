<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header-alpha.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta-alpha.php"); ?>

    <?php
    global $title_header;
    $title = "savmrl.it - Link shortener service";

    $link_as_parameter = "";
    if (isset($_GET["link"]) && $_GET["link"] !== "") {
        $link_as_parameter = getGoodString($_GET["link"]);
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
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
</header>
<main id="drop-area">
    <div id="drop-area-overlay">
        <div class="drop-area-overlay--content">
            <p>
                <img src="/savmrl/images/release.svg" class="image-release"/>
            </p>
            <h1>Release the link to paste it in the input field</h1>
        </div>
    </div>

    <div class="horizontal-center">
        <div class="vertical-center-home">
            <div class="vertical-top">
                <h2 class="title-section brilors">The best anonymous and free link shortener</h2>
                <div id="info-messages"></div>
            </div>
            <?php
            $error = false;
            if ($expiry_date !== "∞" && isValidDate($expiry_date) === null) $error = true;
            if ($expiry_openings !== "∞" && isValidNumber($expiry_openings) === null) $error = true;

            if ($link_as_parameter !== "" && !$error) {
                $shortener_code = insertNewRedirect($link_as_parameter, $expiry_openings, $expiry_date, $access_code);
                if ($shortener_code !== "error") {
                    if ($shortener_code === "invalid_url") {
                        $shortener_code = "";
                        $shortened_url = $link_as_parameter;
                    } else $shortened_url = "https://savmrl.it/r/" . $shortener_code;
                    ?>
                    <p class="text-align-center hidden" id="message">
                    </p>
                    <div class="text-align-center" id="copy-link-container">
                        <div class="input-link-container">
                            <input id="link-input-to-copy" class="input-link" type="url"
                                   placeholder="Copy this shortener link!"
                                   value="<?php echo $shortened_url; ?>" onkeydown="onkeydown_enter(event)"
                                   oninput="validateName(this)" readonly/>
                            <?php if ($shortener_code !== "") { ?>
                                <input type="button" id="edit-link" onclick="editOrSaveLink(this)">
                            <?php } ?>
                        </div>

                        <div class="text-align-center">
                            Redirect link: <a
                                    href="<?php echo $link_as_parameter; ?>"><?php echo $link_as_parameter; ?></a>
                        </div>

                        <div class="margin-top-30px flex-row">
                            <input id="copy-link-button" class="button-link" type="button"
                                   value="Copy the shortened link"
                                   onclick="copyLink()"/>
                            <input id="another-link-button" class="button-link" type="button"
                                   value="Generate another link"
                                   onclick="location.href='/'"/>
                            <a href="/stats/<?php echo $shortener_code; ?>">
                                <input id="see-stats-button" class="button-link" type="button"
                                       value="See click statistics"/>
                            </a>
                        </div>
                        <div id="additional_params" class=""></div>
                    </div>
                    <div class="text-align-center margin-top-30px qr-code-section">
                        <img id="qrcode"
                             src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?php echo $shortened_url; ?>"/>
                    </div>

                    <script>
                        let global_old_name = "<?php echo $shortener_code; ?>";

                        let inputCopy = document.getElementById("link-input-to-copy");
                        document.getElementById("link-input-to-copy").oninput = function () {
                            /*if (inputCopy.classList.contains("frankruhllibre")) inputCopy.classList.remove("frankruhllibre");
                            if (inputCopy.classList.contains("domine")) inputCopy.classList.remove("domine");
                            if (inputCopy.value === "") {
                                inputCopy.classList.add("frankruhllibre");
                            } else {
                                inputCopy.classList.add("domine");
                            }*/
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

                            fetch('https://www.savmrl.it/api/v1/link/edit/index-alpha.php', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/json',},
                                body: JSON.stringify(data),
                            })
                                .then(response => response.json())
                                .then(result => {
                                    console.log(result.code);
                                    if (document.getElementById("message").classList.contains("hidden")) {
                                        document.getElementById("message").classList.remove("hidden");
                                    }
                                    if (document.getElementById("message").classList.contains("error-message")) {
                                        document.getElementById("message").classList.remove("error-message");
                                    }
                                    if (document.getElementById("message").classList.contains("info-message")) {
                                        document.getElementById("message").classList.remove("info-message");
                                    }
                                    if (result.code === "200") {
                                        global_old_name = getValidatedNewName(new_name);
                                        document.getElementById("message").classList.add("info-message");
                                        document.getElementById("message").innerHTML = `Link renamed correctly from <b>${result.data.old_name}</b> to <b>${result.data.new_name}</b>`;
                                    } else {
                                        document.getElementById("message").classList.add("error-message");
                                        document.getElementById("message").innerHTML = `<i>[${result.timestamp}]</i> Error (<b>${result.code}</b>): <i>${result.description}</i>`;
                                        document.getElementById("link-input-to-copy").value = "https://savmrl.it/" + global_old_name;
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);

                                    document.getElementById("link-input-to-copy").value = "https://savmrl.it/" + global_old_name;
                                });
                        }

                        function editOrSaveLink(button) {
                            let editImage = "https://www.savmrl.it/savmrl/images/edit.svg";
                            let saveImage = "https://www.savmrl.it/savmrl/images/save.svg";

                            let inputLink = document.getElementById("link-input-to-copy");

                            let computedStyle = window.getComputedStyle(button);
                            let backgroundImage = computedStyle.getPropertyValue('background-image');
                            let imageUrl = backgroundImage.match(/url\(['"]?(.*?)['"]?\)/)[1];

                            if (imageUrl === editImage || inputLink.readOnly) {
                                //edit link
                                button.style.backgroundImage = `url("${saveImage}")`;
                                inputLink.value = inputLink.value.substring(("https://savmrl.it/r/").length);
                                inputLink.readOnly = false;
                                inputLink.focus();
                            } else {
                                //save link
                                //TODO
                                button.style.backgroundImage = `url("${editImage}")`;
                                let new_name = getValidatedNewName(inputLink.value);
                                inputLink.value = inputLink.value.replace("https://savmrl.it/r/", "");
                                inputLink.value = "https://savmrl.it/r/" + inputLink.value;
                                inputLink.readOnly = true;
                                inputLink.blur();

                                if (global_old_name !== new_name) {
                                    onsubmit_newName(global_old_name, new_name);
                                }
                            }
                        }

                        function getValidatedNewName(new_name) {
                            // Replace any characters that are not 0-9, a-z, A-Z, or "-" with an empty string
                            let validatedName = new_name.toString().replace(/[^0-9a-zA-Z\-]/g, '');

                            // Limit the length to a maximum of 100 characters
                            validatedName = validatedName.substring(0, 100);

                            return validatedName;
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
                    Error. Try again later, check the URL is valid or contact a maintainer.
                </p>
            <?php
            }
            ?>
                <form class="text-align-center" id="generate-link-form" onsubmit="onsubmit_link(this)">
                    <?php
                    $is_advanced = false;
                    if ($expiry_openings !== "∞" || $expiry_date !== "∞" || $access_code !== "" || (isset($_GET["advanced"]))) {
                        $is_advanced = true;
                    }
                    ?>
                    <div id="basic-advanced-switcher">
                        <div class="square-20px border-radius-bottom-right-20px"></div>
                        <div class="switcher">
                            <div id="basic-switcher-option" class="option-switcher <?php if (!$is_advanced) {
                                echo "selected-option";
                            } ?>"
                                 onclick="changeBasicAdvanced('basic')">Basic
                            </div>
                            <div id="advanced-switcher-option" class="option-switcher <?php if ($is_advanced) {
                                echo "selected-option";
                            } ?>"
                                 onclick="changeBasicAdvanced('advanced')">Advanced
                            </div>
                        </div>
                        <div class="square-20px border-radius-bottom-left-20px"></div>
                    </div>
                    <div class="input-link-container">
                        <input id="link-input" type="url" class="input-link" placeholder="Insert your link (URL) here!"
                               name="link" oninput="linkInput()" value="<?php echo $link_as_parameter; ?>" required/>
                        <input id="generate-link-button" class="button-link <?php echo $hidden_or_not_class; ?>"
                               type="submit" value=""/>
                    </div>
                    <script>
                        const dropArea = document.getElementById('drop-area');

                        // Prevent default drag behaviors
                        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                            dropArea.addEventListener(eventName, preventDefaults, false);
                        });

                        function preventDefaults(e) {
                            e.preventDefault();
                            e.stopPropagation();
                        }

                        // Highlight on drag hover
                        ['dragenter', 'dragover'].forEach(eventName => {
                            dropArea.addEventListener(eventName, () => dropArea.classList.add('highlight'), false);
                        });

                        ['dragleave', 'drop'].forEach(eventName => {
                            dropArea.addEventListener(eventName, () => dropArea.classList.remove('highlight'), false);
                        });

                        // Handle dropped link
                        dropArea.addEventListener('drop', handleDrop, false);

                        function handleDrop(e) {
                            const dt = e.dataTransfer;
                            let url = null;

                            // Try to get URL from text/uri-list
                            if (dt.getData('text/uri-list')) {
                                url = dt.getData('text/uri-list');
                            }
                            // Fallback: plain text might contain a URL
                            else if (dt.getData('text/plain')) {
                                const text = dt.getData('text/plain');
                                // Simple check if it looks like a URL
                                if (text.startsWith('http://') || text.startsWith('https://')) {
                                    url = text;
                                }
                            }

                            if (url) {
                                displayLink(url);
                            } else {
                                //TODO: no valid URL found
                            }
                        }

                        function displayLink(url) {
                            document.getElementById('link-input').value = url;
                            linkInput();
                        }
                    </script>
                    <div id="div-after-link" class="frankruhllibre">
                        <div id="advanced-params" class="<?php if (!$is_advanced) {
                            echo "hidden";
                        } ?>">
                        <span>
                        The link expires after
                        <input class="optional-param" id="opening_expiry" type="text" min="1" name="openings"
                               value="<?php echo $expiry_openings; ?>" oninput="validateOpenings(this)"
                               onblur="setInfinityNumber(this)"
                               onfocus="checkInfinity(this);changeTypeTextToNumber(this)"/>
                        openings <b>or</b> on
                        <input class="optional-param" id="date_expiry" type="text" name="date"
                               value="<?php echo $expiry_date; ?>" oninput="validateDate(this)"
                               onblur="setInfinityDate(this)" onfocus="checkInfinity(this);changeTypeTextToDate(this)"/>
                        <br>
                            You can set an access code
                        <input class="optional-param" id="access_code" type="password" min="1" name="access_code"
                               value="<?php echo $access_code; ?>"/>
                        </span>
                        </div>
                        <!--<input id="generate-link-button" class="button-link <?php /*echo $hidden_or_not_class; */ ?>"
                           type="submit" value="Generate shortener link"/>-->
                    </div>
                </form>
                <!--<div class="text-align-center" id="addons">
                    <div class="margin-bottom-30px">
                        Install the web browser add-on
                    </div>
                    <a href="https://savmrl.it/r/firefox"><img src="/savmrl/images/badges/firefox.png" class="badge"/></a>
                    <a href="https://savmrl.it/r/chrome"><img src="/savmrl/images/badges/chrome.png" class="badge"/></a>
                    <a href="https://savmrl.it/r/edge"><img src="/savmrl/images/badges/ms-edge.png" class="badge"/></a>
                </div>-->

                <script>
                    document.getElementById("access_code").onfocus = function () {
                        document.getElementById("access_code").type = "text";
                    }
                    document.getElementById("access_code").onblur = function () {
                        document.getElementById("access_code").type = "password";
                    }

                    function onsubmit_link(form) {
                        const baseUrl = "./index-alpha.php";
                        const openingExpiry = document.getElementById('opening_expiry');
                        const dateExpiry = document.getElementById('date_expiry');
                        const link = document.getElementById('link-input');

                        form.action = `${baseUrl}?openings=${encodeURIComponent(openingExpiry.value)}&date=${encodeURIComponent(dateExpiry.value)}&link=${encodeURIComponent(link.value)}`;
                        openingExpiry.name = "";
                        dateExpiry.name = "";
                        link.name = "";
                        form.method = "post";
                    }
                </script>
                <?php
            }
            ?>
            <div class="vertical-bottom"></div>
        </div>
    </div>

    <script>
        function validLink(link) {
            const urlPattern = new RegExp('^(https?:\\/\\/)?' +
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' +
                '((\\d{1,3}\\.){3}\\d{1,3}))' +
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+\\#]*)*' + // added \\# to path
                '(\\?[;&a-z\\d%_.~+=-]*)?' +
                '(\\#[-a-z\\d_]*)?$', 'i');
            return !!urlPattern.test(link);
        }

        function linkInput() {
            let linkInput = document.getElementById("link-input");
            let divAfterLink = document.getElementById("div-after-link");
            let generateButton = document.getElementById("generate-link-button");
            if (divAfterLink !== null) {
                if (linkInput.value.replaceAll(" ", "") !== "" && validLink(linkInput.value)) {
                    divAfterLink.style.display = "block";
                    if (generateButton.classList.contains("hidden")) generateButton.classList.remove("hidden");
                } else {
                    divAfterLink.style.display = "none";
                    generateButton.classList.add("hidden");
                }
            }

            /*if (linkInput.classList.contains("frankruhllibre")) linkInput.classList.remove("frankruhllibre");
            if (linkInput.classList.contains("domine")) linkInput.classList.remove("domine");
            if (linkInput.value === "") {
                linkInput.classList.add("frankruhllibre");
            } else {
                linkInput.classList.add("domine");
            }*/
        }

        function copyLink() {
            var linkInput = document.getElementById('link-input-to-copy');
            var copyButton = document.getElementById('copy-link-button');
            linkInput.select();
            document.execCommand('copy');
            linkInput.setSelectionRange(0, 0);

            let previousText = copyButton.value;
            copyButton.value = "✓ Link copied!";
            setTimeout(function () {
                copyButton.value = previousText;
            }, 3000);
        }

        function validateOpenings(input) {
            let inputValue = input.value;
            inputValue = inputValue.replace(/[^0-9∞]/g, '');
            if (inputValue === '' || (inputValue !== '∞' && (isNaN(inputValue) || parseInt(inputValue) < 1))) {
                //error
            }
            input.value = inputValue;
        }

        function setInfinityNumber(input, force = false) {
            let inputValue = input.value;
            if (input.type === "number") input.type = "text";
            inputValue = inputValue.replace(/[^0-9∞]/g, '');
            if (inputValue === '' || isNaN(inputValue) || parseInt(inputValue) < 1 || force) {
                input.value = '∞';
            }
            inputValue = input.value;

            if (!isNaN(inputValue) && parseInt(inputValue) >= 1) {
                input.value = parseInt(inputValue);
                //console.log(`Correct number ${parseInt(inputValue)}`)
            } else if (inputValue === "∞") {
                //console.log("Not set openings limit");
            } else {
                //console.log(`Error: ${inputValue}`);
            }
        }

        function changeTypeTextToNumber(input) {
            if (input.type === "text") input.type = "number";
            input.min = 1;
        }

        function validateDate(input) {
            let inputValue = input.value;
            if (inputValue === '' || inputValue !== '∞' && !isValidDate(inputValue)) {
                //error
            }
            input.value = inputValue;
        }

        function setInfinityDate(input, force = false) {
            let inputValue = input.value;
            if (input.type === "date") input.type = "text";
            if (inputValue === '' || inputValue !== '∞' && !isValidDate(inputValue) || force) {
                input.value = '∞';
            }
        }

        function isValidDate(value) {
            return !isNaN(Date.parse(value));
        }

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

            if (basic.classList.contains("selected-option")) basic.classList.remove("selected-option");
            if (advanced.classList.contains("selected-option")) advanced.classList.remove("selected-option");

            /*let selector = document.getElementById("selected-switcher-option");

            if (selector.classList.contains("selected-option-basic")) selector.classList.remove("selected-option-basic");
            if (selector.classList.contains("selected-option-advanced")) selector.classList.remove("selected-option-advanced");*/

            if (status === "basic") {
                basic.classList.add("selected-option");
                //selector.classList.add("selected-option-basic");
                showHideAdvanced("hide");
            } else {
                advanced.classList.add("selected-option");
                //selector.classList.add("selected-option-advanced");
                showHideAdvanced("show");
            }
        }

        function showHideAdvanced(status) {
            let advancedContainer = document.getElementById("advanced-params");
            if (status === "show") {
                //show elements

                if (advancedContainer.classList.contains("hidden")) advancedContainer.classList.remove("hidden");
            } else {
                //hide (if exists!)
                setInfinityNumber(document.getElementById("opening_expiry"), true);
                setInfinityDate(document.getElementById("date_expiry"), true);
                advancedContainer.classList.add("hidden");
            }
        }

        if (document.getElementById("link-input") !== null && document.getElementById("div-after-link") !== null) linkInput();
    </script>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

</body>
</html>