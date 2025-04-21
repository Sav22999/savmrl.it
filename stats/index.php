<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta.php"); ?>

    <?php
    global $title_header, $seconds;

    $name = substr($_SERVER['REQUEST_URI'], 7); // /stats/ -> start from the 7th index (the 0, 1, 2, 3, 4, 5, 6 are removed "/stats/")
    //If the name start with "?code=" remove it
    if (substr($name, 0, 6) === "?code=") {
        $name = substr($name, 6);
    }
    if ($name === "") redirectTo(false, "/", 0);

    $n_clicks = getStatistics($name);
    $redirect_link = getUrlFromName($name);
    $title = "savmrl.it - Statistics";
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
</header>
<main>
    <div class="horizontal-center">
        <?php
        if ($n_clicks === "not_exists" && $redirect_link === "not_exists") {
            ?>
            <title>savmrl.it - Page not exists</title>
            <h2 class="title-section">Link doesn't exist or has expired</h2>
            <div class="big-space"></div>
            <br>
            <p class="horizontal-center-p">
                The shortened link you're looking for doesn't exist, or it's expired.<br>Be sure the shortened link you
                used is correct, is still valid<sup>*</sup> or try again.<br><br><sup>*</sup> Invalid links could be
                because the link is not correct at all, or the limit of opening number times has achieved, or because it
                was valid until a specific datetime.
            </p>
            <?php
            redirectTo(false, "/", 10);
        } else if ($n_clicks === "invalid" && $redirect_link === "invalid") {
            ?>
            <title>savmrl.it - Page not exists</title>
            <h2 class="title-section">Invalid link</h2>
            <div class="big-space"></div>
            <br>
            <p class="horizontal-center-p">
                The shortened link you provided is <b>not</b> valid.<br>Be sure the shortened link you used is correct
                or try again.
            </p>
            <?php
            redirectTo(false, "/", 10);
        } else if ($n_clicks === "?" && $redirect_link === "?") {
            redirectTo(false, "/", 0);
        } else {
            ?>
            <h2 class="title-section">Statistics<br>
                <a href="<?php echo "https://savmrl.it/r/" . $name; ?>"><?php echo "https://savmrl.it/r/" . $name; ?></a>
            </h2>
            <h1 class="subtitle-section no-bold font-small"><?php echo $n_clicks; ?></h1>
            <br class="big-space">
            <br>
            <br>
            <p class="text-align-center">This is the times number this link has been opened.</p>
            <?php
        }
        ?>
    </div>
    <div class="ads-section">
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

</body>
</html>