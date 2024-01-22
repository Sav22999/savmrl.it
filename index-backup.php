<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header.php"); ?>

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
        }

        if ($link_as_parameter === "" || $error) {
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
                <input id="link-input" type="url" class="input-link" placeholder="Insert your link (URL) here!"
                       name="link"
                       oninput="linkInput()"
                       value="<?php echo $link_as_parameter; ?>"
                       required/>
                <div id="div-after-link">
                    <div id="advanced-params">
                        The link expires after
                        <input class="optional-param" id="opening_expiry" type="text" min="1" name="openings"
                               value="<?php echo $expiry_openings; ?>" oninput="validateOpenings(this)"
                               onblur="setInfinityNumber(this)"
                               onfocus="checkInfinity(this);changeTypeTextToNumber(this)">
                        openings <b>or</b> on
                        <input class="optional-param" id="date_expiry" type="text" name="date"
                               value="<?php echo $expiry_date; ?>" oninput="validateDate(this)"
                               onblur="setInfinityDate(this)" onfocus="checkInfinity(this);changeTypeTextToDate(this)">
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

        function setInfinityNumber(input) {
            let inputValue = input.value;
            if (input.type === "number") input.type = "text";
            inputValue = inputValue.replace(/[^0-9∞]/g, '');
            if (inputValue === '' || isNaN(inputValue) || parseInt(inputValue) < 1) {
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

        function setInfinityDate(input) {
            let inputValue = input.value;
            if (input.type === "date") input.type = "text";
            if (inputValue === '' || inputValue !== '∞' && !isValidDate(inputValue)) {
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

        if (document.getElementById("link-input") !== null && document.getElementById("div-after-link") !== null) linkInput();
    </script>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

</body>
</html>