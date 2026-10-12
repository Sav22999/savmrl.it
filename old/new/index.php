<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/meta.php"); ?>

    <?php
    global $title_header, $seconds;

    $link = substr($_SERVER['REQUEST_URI'], 5); // /new/ -> start from the 5th index (the 0, 1, 2, 3, 4 are removed "/new/")
    //If the name start with "?code=" remove it
    if ($link === "") redirectTo(false, "/", 0);
    else redirectTo(false, "/?link=" . $link, 0);

    $title = "savmrl.it - New shortener link";
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
</header>
<main>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/footer.php"); ?>
</footer>

</body>
</html>