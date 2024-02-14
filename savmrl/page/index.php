<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta.php"); ?>

    <?php
    global $title_header, $seconds;

    /*
    $current_url = $_SERVER['REQUEST_URI'];
    $current_url = trim($current_url, '/');
    $url_segments = explode('/', $current_url);
    $name = end($url_segments);*/

    $name = substr($_SERVER['REQUEST_URI'], 1);

    $title = "Redirecting";
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
</header>

<main>
    <div class="horizontal-center">
        <div class="error-message text-align-center font-size-30">
            Outdated link! Please, use the new link: <a
                    href="https://savmrl.it/r/<?php echo $name; ?>">savmrl.it/r/<?php echo $name; ?></a>
        </div>
        <?php redirectTo(false, "https://savmrl.it/r/" . $name, 5); ?>
    </div>
    <div class="ads-section">
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

</body>
</html>