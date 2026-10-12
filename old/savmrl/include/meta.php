<?php
$url_opengraph = "https://www.savmrl.it/savmrl/images/banner.png";
?>
    <link rel="stylesheet" href="/savmrl/css/style.css"/>
    <link rel="icon" href="/savmrl/images/icon.svg"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-16">
    <meta name="viewport" content="width=device-width, initial-scale=0.8"/>

    <!-- Primary Meta Tags -->
    <title>savmrl.it – Anonymous & Free URL Shortener with Link Tracking</title>
    <meta name="description" content="Shorten and share links instantly with savmrl.it. No registration required. Track clicks, generate QR codes, and access browser extensions for Chrome, Firefox, and Edge.">
    <meta name="keywords" content="URL shortener, free link shortener, anonymous URL shortener, link redirect service, QR code generator, link tracking, savmrl.it, Saverio Morelli">
    <meta name="author" content="Saverio Morelli">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="charset=UTF-16">
    <link rel="canonical" href="https://savmrl.it/">

    <!-- Open Graph -->
    <meta property="og:locale" content="en"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="savmrl.it"/>
    <meta property="og:description"
          content="The best anonymous and free link shortener"/>
    <meta property="og:url" content="https://www.savmrl.it"/>
    <meta property="og:site_name" content="savmrl.it"/>
    <meta property="og:image" content="<?php echo $url_opengraph; ?>"/>
    <meta property="og:image:secure_url" content="<?php echo $url_opengraph; ?>"/>
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:description"
          content="The best anonymous and free link shortener"/>
    <meta name="twitter:title" content="savmrl.it"/>
    <meta name="twitter:site" content="@Sav22999"/>
    <meta name="twitter:image" content="<?php echo $url_opengraph; ?>"/>
    <meta name="twitter:creator" content="@Sav22999"/>

    <meta name="viewport" content="width=device-width, initial-scale=1"/>
<?php
global $title;
if (isset($title)) {
    echo "<title>" . $title . "</title>";
} else {
    $title = "?";
    echo "<title>savmrl.it</title>";
}
?>