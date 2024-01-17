<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header.php"); ?>

    <?php
    global $title_header, $seconds;

    /*
    $current_url = $_SERVER['REQUEST_URI'];
    $current_url = trim($current_url, '/');
    $url_segments = explode('/', $current_url);
    $name = end($url_segments);*/

    $name = substr($_SERVER['REQUEST_URI'], 1);

    $redirect_url = getUrlFromName($name);
    $title = "Redirecting - " . $redirect_url;

    if ($redirect_url === "not_exists") redirectTo(false, "/page-not-exists/", 0);
    else if ($redirect_url === "invalid") redirectTo(false, "/invalid/", 0);
    else if ($redirect_url === "?") redirectTo(false, "/", 0);
    else redirectTo($name, $redirect_url, $seconds);
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
</header>
<main>
    <div class="horizontal-center">
        <h2 class="title-section">Redirecting…</h2>
        <h1 class="subtitle-section no-bold font-small"><?php echo $redirect_url; ?></h1>
        <div class="big-space"></div>
        <br>
        <br>
        <p class="horizontal-center-p">
            You will be redirected to the link in some seconds. In case the redirect doesn't work, please <a
                    href="<?php echo $redirect_url; ?>">click here</a>
        </p>
    </div>
    <div class="ads-section">
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

</body>
</html>