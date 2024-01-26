<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header-alpha.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta.php"); ?>

    <?php
    global $title_header, $seconds;

    /*
    $current_url = $_SERVER['REQUEST_URI'];
    $current_url = trim($current_url, '/');
    $url_segments = explode('/', $current_url);
    $name = end($url_segments);*/

    $name = substr($_SERVER['REQUEST_URI'], 1);

    $name = substr($_SERVER['REQUEST_URI'], 19);//TODO: remove this

    $redirect_url = getUrlFromName($name, false, false);
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
        <?php redirectTo($name, $redirect_url, $seconds);
        } else {
        //It's an error
        $errors_type_1 = array("not_exists", "invalid", "?"); //error message
        $errors_type_2 = array("access_code_required", "access_code_wrong"); //require password or wrong password
        $errors_type_3 = array("reported"); //reported message
        $errors_type_4 = array(); //redirect only to a page

        if (in_array($redirect_url, $errors_type_1)) {
        $title = "";
        $title_section = "";
        $description = "";
        $go_to = "/";
        $seconds = 0;

        switch ($redirect_url) {
            case "not_exists":
                $title = "savmrl.it - Page not exists";
                $title_section = "Link doesn't exist or has expired";
                $description = "The shortened link you're looking for doesn't exist, or it's expired.<br>Be sure the shortened link you used is correct, is still valid<sup>*</sup> or try again.<br><br><sup>*</sup> Invalid links could be because the link is not correct at all, or the limit of opening number times has achieved, or because it was valid until a specific datetime.";

                $go_to = "/";
                $seconds = 10;
                break;

            case "invalid":
                $title = "savmrl.it - Invalid";
                $title_section = "Invalid link";
                $description = "The shortened link you provided is <b>not</b> valid.<br>Be sure the shortened link you used is correct or try again.";

                $go_to = "/";
                $seconds = 10;
                break;

            default:
                $go_to = "/";
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
        //Access code
        $title = "savmrl.it - Access code required";
        $title_section = "Access code required";
        $description = "To get the link, please insert the access code";

        if ($redirect_url === "access_code_wrong") {
            //error: wrong access code
        }

        ?>
            <title><?php echo $title; ?></title>
            <h2 class="title-section"><?php echo $title_section; ?></h2>
            <p class="horizontal-center-p">
                <?php echo $description; ?>
            </p>
            <div class="big-space"></div>
        <br>
        <?php //TODO: request as POST and not as GET. The name have to be 'access_code' as POST parameter ?>
            <form method="post" action="/access-code/" onsubmit="onsubmit_link(this);">
                <p class="text-align-center">
                    <input class="optional-param" id="access-code" type="password" name="access_code"
                           placeholder="Digit the access code" required/>
                    <input id="go-to-link-button" class="button-link" type="submit" value="Go to the link"/>
                </p>
            </form>

            <script>
                document.getElementById("access-code").onfocus = function () {
                    document.getElementById("access-code").type = "text";
                }
                document.getElementById("access-code").onblur = function () {
                    document.getElementById("access-code").type = "password";
                }

                function onsubmit_link(form) {
                    let nameElement = document.createElement("input");
                    nameElement.type="text";
                    nameElement.classList.add("hidden");
                    nameElement.name ="name";
                    nameElement.value="<?php echo $name; ?>";
                    form.appendChild(nameElement);
                }
            </script>
        <?php
        } else if (in_array($redirect_url, $errors_type_3)) {
        //Links reported
        $title = "savmrl.it - Link blocked";
        $title_section = "Link blocked because reported as unsafe";
        $description = "This link has been reported as unsafe, and now it's blocked!<br>If you desire, you can contact me to get more information.";
        $go_to = "/";
        $seconds = 10;
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
        } else if (in_array($redirect_url, $errors_type_4)) {
            //Just redirecting
            //
        }
            ?>

        <?php } ?>
    </div>
    <div class="ads-section">
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

</body>
</html>