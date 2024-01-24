<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta.php"); ?>

    <?php
    global $title_header;
    $title = "savmrl.it - Page not exists";

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
        <h2 class="title-section">Link doesn't exist or has expired</h2>
        <div class="big-space"></div>
        <br>
        <p class="horizontal-center-p">
            The shortened link you're looking for doesn't exist, or it's expired.
            <br>
            Be sure the shortened link you used is correct, is still valid<sup>*</sup> or try again.
            <br><br>
            <sup>*</sup> Invalid links could be because the link is not correct at all, or the limit of opening number times has achieved, or because it was valid until a specific datetime.
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