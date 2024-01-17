<html>
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/header.php"); ?>

    <?php
    global $title_header;
    $title = "savmrl.it - Web browser add-on";
    ?>
    <title><?php echo $title; ?></title>
</head>
<body>

<header>
    <?php echo $title_header; ?>
</header>
<main>
    <div class="horizontal-center">
        <h2 class="title-section">Web browser add-on</h2>
        <div class="big-space"></div>
        <br>
        <p class="horizontal-center-p basic text-align-justify">
            It's available also the web browser add-on, which permits you to create a shortened link just pressing the add-on icon on the toolbar.
            <br>
            It's available on the main web browser add-ons stores!
        </p>
        <div class="big-space"></div>
        <div class="big-space"></div>
        <p class="basic text-center">
            <br>
            Install the web browser add-on
            <br>
            <a href="https://addons.mozilla.org/en-GB/firefox/addon/savmrl-it/"><img src="/savmrl/images/badges/firefox.png" class="badge"/></a>
            <a href="https://chromewebstore.google.com/detail/pkbjoeedhjjokllfhgcapbbihianeemn"><img src="/savmrl/images/badges/chrome.png" class="badge"/></a>
            <a href="https://microsoftedge.microsoft.com/addons/detail/jdgcfpdoiojfebhafmihkihficfnpahk" class="hidden"><img src="/savmrl/images/badges/ms-edge.png" class="badge"/></a>
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