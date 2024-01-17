<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header.php"); ?>

    <?php
    global $title_header;
    $title = "savmrl.it - Link expired";

    redirectTo(false, "/", 10);
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
</header>
<main>
    <div class="horizontal-center">
        <h2 class="title-section">Link expired</h2>
        <div class="big-space"></div>
        <br>
        <p class="horizontal-center-p">
            This shortened link has expired.
            <br>
            This can be because the limit of opening number times has achieved or because it was valid until a specific datetime.
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