<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta.php"); ?>

    <?php
    global $title_header, $seconds;

    $name = "";
    if (isset($_GET["code"])) $name = getGoodString($_GET["code"]);
    if ($name === "") redirectTo(false, "/", 0);

    $n_clicks = getStatistics($name);
    $redirect_link = getUrlFromName($name);
    $title = "savmrl.it - Statistics";


    if ($n_clicks === "not_exists" || $redirect_link === "not_exists") redirectTo(false, "/page-not-exists/", 0);
    else if ($n_clicks === "invalid" || $redirect_link === "invalid") redirectTo(false, "/invalid/", 0);
    else if ($n_clicks === "?" || $redirect_link === "?") redirectTo(false, "/", 0);
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
</header>
<main>
    <div class="horizontal-center">
        <h2 class="title-section">Statistics<br>
            <a href="<?php echo "https://savmrl.it/" . $name; ?>"><?php echo "https://savmrl.it/" . $name; ?></a>
        </h2>
        <h1 class="subtitle-section no-bold font-small"><?php echo $n_clicks; ?></h1>
        <br class="big-space">
        <br>
        <br>
        <p class="text-align-center">This is the times number this link has been opened.</p>
    </div>
    <div class="ads-section">
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

</body>
</html>