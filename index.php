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
        if ($link_as_parameter !== "") {
            $shortener_code = insertNewRedirect($link_as_parameter);
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
                           onclick="copy_link()"/>
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
                       oninput="link_input()"
                       value="<?php echo $link_as_parameter; ?>"
                       required/>

                <input id="generate-link-button" class="button-link <?php echo $hidden_or_not_class; ?>" type="submit"
                       value="Generate shortener link"/>
            </form>
            <div class="big-space"></div>
            <div class="big-space"></div>
            <div class="big-space"></div>
            <div class="text-align-center" id="addons">
                Install the web browser add-on
                <br>
                <a href="https://savmrl.it/7hgSa"><img
                            src="/savmrl/images/badges/firefox.png" class="badge"/></a>
                <a href="https://savmrl.it/cQi6A"><img
                            src="/savmrl/images/badges/chrome.png" class="badge"/></a>
                <a href="https://microsoftedge.microsoft.com/addons/detail/jdgcfpdoiojfebhafmihkihficfnpahk"
                   class="hidden"><img src="/savmrl/images/badges/ms-edge.png" class="badge"/></a>
            </div>
            <?php
        }
        ?>
        <div class="big-space"></div>
    </div>

    <script>
        function link_input() {
            let linkInput = document.getElementById("link-input");
            let generateLinkButton = document.getElementById("generate-link-button");
            if (linkInput.value.replaceAll(" ", "") === "") generateLinkButton.style.display = "none";
            else generateLinkButton.style.display = "inline-block";
        }

        function copy_link() {
            var linkInput = document.getElementById('link-input-to-copy');
            linkInput.select();
            document.execCommand('copy');
            linkInput.setSelectionRange(0, 0);

            let newMessage = document.createElement("p");
            newMessage.classList.add("text-align-center", "info-message");
            newMessage.textContent = "Shortener link copied in the clipboard correctly!";
            let allMessages = document.getElementById("info-messages");
            allMessages.appendChild(newMessage);
            setTimeout(function () {
                allMessages.removeChild(newMessage);
            }, 3000);
        }

        if (document.getElementById("link-input") !== null && document.getElementById("generate-link-button") !== null) link_input();
    </script>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

</body>
</html>