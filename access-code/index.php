<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta.php"); ?>

    <?php
    global $title_header, $seconds;

    $name = null;
    if (isset($_POST["name"]) && $_POST["name"] !== "") $name = $_POST["name"];

    $access_code = null;
    if (isset($_POST["access_code"]) && $_POST["access_code"] !== "") {
        $access_code = $_POST["access_code"];
    }

    $redirect_url = "invalid";
    if ($access_code !== null && $name !== null) {
        $redirect_url = getUrlFromName($name, $access_code, false);
    }

    if ($access_code === null || $name === null) {
        $redirect_url = "?";
    }
    $title = "Redirecting - " . $redirect_url;

    $errors = array("not_exists", "invalid", "access_code_required", "access_code_wrong", "reported", "?");
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
            <h2 class="title-section">Redirecting…</h2>
            <h1 class="subtitle-section no-bold font-small"><?php echo $redirect_url; ?></h1>
            <div class="big-space"></div>
            <br>
            <br>
            <p class="horizontal-center-p">
                You will be redirected to the link in some seconds. In case the redirect doesn't work, please <a
                        href="<?php echo $redirect_url; ?>">click here</a>
            </p>
            <?php
            redirectTo($name, $redirect_url, $seconds);
        } else {
            echo $redirect_url;

            if ($redirect_url !== "access_code_wrong" && $redirect_url !== "access_code_required") {
                redirectTo(false, "/", 0);
            }

            //Access code
            $title = "savmrl.it - Access code required";
            $title_section = "Access code required";
            $description = "To get the link, please insert the access code";

            ?>
            <title><?php echo $title; ?></title>
            <h2 class="title-section"><?php echo $title_section; ?></h2>
            <p class="horizontal-center-p">
                <?php echo $description; ?>
            </p>
            <div class="big-space"></div>
            <br>
            <?php
            if ($redirect_url === "access_code_wrong") {
                ?>
                <p class="text-align-center error-message" id="error-message-access-code">Access code wrong. Try
                    again.</p>
                <?php
            }
            ?>
            <form method="post" action="./" onsubmit="onsubmit_link(this);">
                <p class="text-align-center">
                    <input class="optional-param" id="access-code" type="password" name="access_code"
                           placeholder="Digit the access code" value="" required/>
                    <input id="go-to-link-button" class="button-link" type="submit" value="Go to the link"/>
                </p>
            </form>
            <?php
        }
        ?>

        <script>
            document.getElementById("access-code").onfocus = function () {
                document.getElementById("access-code").type = "text";
            }
            document.getElementById("access-code").onblur = function () {
                document.getElementById("access-code").type = "password";
            }

            function onsubmit_link(form) {
                let nameElement = document.createElement("input");
                nameElement.type = "text";
                nameElement.classList.add("hidden");
                nameElement.name = "name";
                nameElement.value = "<?php echo $name; ?>";
                form.appendChild(nameElement);
            }
        </script>
    </div>
    <div class="ads-section">
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

</body>
</html>