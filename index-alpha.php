<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header-alpha.php"); ?>

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
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
</header>
<main>
    <div class="horizontal-center">
        <h2 class="title-section">The best anonymous and free link shortener</h2>
        <div class="big-space"></div>
        <div id="info-messages"></div>
        <?php
        $error = false;
        if ($expiry_date !== "∞" && isValidDate($expiry_date) === null) $error = true;
        if ($expiry_openings !== "∞" && isValidNumber($expiry_openings) === null) $error = true;

        if ($link_as_parameter !== "" && !$error) {
            $shortener_code = insertNewRedirect($link_as_parameter, $expiry_openings, $expiry_date);
            if ($shortener_code !== "error" && $shortener_code !== "invalid_url") {
                $shortened_url = "https://savmrl.it/" . $shortener_code;
                ?>
                <div class="text-align-center">
                    <input id="link-input-to-copy" class="input-link" type="url" placeholder="Copy this shortener link!"
                           value="<?php echo $shortened_url; ?>" readonly/>
                    <button id="edit-link" onclick="editOrSaveLink(this)"></button>

                    <div class="text-align-center">
                        Redirect link: <a href="<?php echo $link_as_parameter; ?>"><?php echo $link_as_parameter; ?></a>
                    </div>

                    <input id="copy-link-button" class="button-link" type="button" value="Copy the shortened link"
                           onclick="copyLink()"/>
                    <input id="another-link-button" class="button-link" type="button" value="Generate another link"
                           onclick="location.href='/'"/>
                    <a href="/stats/?code=<?php echo $shortener_code; ?>">
                        <input id="see-stats-button" class="button-link" type="button" value="See click statistics"/>
                    </a>
                </div>
                <div class="text-align-center margin-top-10px">
                    <img id="qrcode"
                         src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?php echo $shortened_url; ?>"/>
                </div>
                <?php
            } else {
                $error = true;
            }
        } else if ($link_as_parameter === "" || $error) {
            $hidden_or_not_class = "hidden-btn";
            if ($link_as_parameter !== "") $hidden_or_not_class = "";
            if ($error) {
                ?>
                <p class="text-align-center error-message">
                    Error. Try again later, check the URL is valid or contact a maintainer.
                </p>
                <?php
            }
            ?>
            <form class="text-align-center" id="generate-link-form">
                <span id="basic-advanced-switcher">
                    <span id="selected-switcher-option"></span>
                    <span id="basic-switcher-option" class="option-switcher selected-option"
                          onclick="changeBasicAdvanced('basic')">Basic</span>
                    <span id="advanced-switcher-option" class="option-switcher"
                          onclick="changeBasicAdvanced('advanced')">Advanced</span>
                </span>
                <br>
                <input id="link-input" type="url" class="input-link" placeholder="Insert your link (URL) here!"
                       name="link"
                       oninput="linkInput()"
                       value="<?php echo $link_as_parameter; ?>"
                       required/>
                <div id="div-after-link">
                    <div id="advanced-params" class="hidden">
                        <span>
                        The link expires after
                        <input class="optional-param" id="opening_expiry" type="text" min="1" name="openings"
                               value="<?php echo $expiry_openings; ?>" oninput="validateOpenings(this)"
                               onblur="setInfinityNumber(this)"
                               onfocus="checkInfinity(this);changeTypeTextToNumber(this)">
                        openings <b>or</b> on
                        <input class="optional-param" id="date_expiry" type="text" name="date"
                               value="<?php echo $expiry_date; ?>" oninput="validateDate(this)"
                               onblur="setInfinityDate(this)" onfocus="checkInfinity(this);changeTypeTextToDate(this)">
                        </span>
                    </div>
                    <input id="generate-link-button" class="button-link <?php echo $hidden_or_not_class; ?>"
                           type="submit" value="Generate shortener link"/>
                </div>
            </form>
            <div class="big-space"></div>
            <div class="big-space"></div>
            <div class="big-space"></div>
            <div class="text-align-center" id="addons">
                Install the web browser add-on
                <br>
                <a href="https://savmrl.it/7hgSa"><img src="/savmrl/images/badges/firefox.png" class="badge"/></a>
                <a href="https://savmrl.it/cQi6A"><img src="/savmrl/images/badges/chrome.png" class="badge"/></a>
                <a href="https://savmrl.it/90utg"><img src="/savmrl/images/badges/ms-edge.png" class="badge"/></a>
            </div>
            <?php
        }
        ?>
        <div class="big-space"></div>
    </div>

    <script>
        function linkInput() {
            let linkInput = document.getElementById("link-input");
            let divAfterLink = document.getElementById("div-after-link");
            if (divAfterLink !== null) {
                if (linkInput.value.replaceAll(" ", "") === "") divAfterLink.style.display = "none";
                else divAfterLink.style.display = "block";
            }
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
                inputLink.value = inputLink.value.replace("https://savmrl.it/", "");
                inputLink.readOnly = false;
                inputLink.focus();
            } else {
                //save link
                //TODO
                button.style.backgroundImage = `url("${editImage}")`;
                inputLink.value = "https://savmrl.it/" + inputLink.value;
                inputLink.readOnly = true;
                inputLink.blur();
            }
        }

        function changeBasicAdvanced(status) {
            let basic = document.getElementById("basic-switcher-option");
            let advanced = document.getElementById("advanced-switcher-option");

            if (basic.classList.contains("selected-option")) basic.classList.remove("selected-option");
            if (advanced.classList.contains("selected-option")) advanced.classList.remove("selected-option");

            let selector = document.getElementById("selected-switcher-option");

            if (status === "basic") {
                basic.classList.add("selected-option");
                selector.style.left = "2px";
                selector.style.right = "80px";
                showHideAdvanced("hide");
            } else {
                advanced.classList.add("selected-option");
                selector.style.left = "50px";
                selector.style.right = "2px";
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