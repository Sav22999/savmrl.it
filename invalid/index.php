<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header.php"); ?>

    <?php
    global $title_header;
    $title = "savmrl.it - Invalid";

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
        <h2 class="title-section">Invalid link</h2>
        <div class="big-space"></div>
        <br>
        <p class="horizontal-center-p">
            The shortened link you provided is <b>not</b> valid.
            <br>
            Be sure the shortened link you used is correct or try again.
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