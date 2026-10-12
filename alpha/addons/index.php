<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/header.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/translations.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/auth.php");

auth_start_session();
$current_user = auth_get_current_user();
global $title_header;
$lang = detectLanguage();
$title = "savmrl.it - " . t('addons-title', $lang);

function addonBrowserIcon($path)
{
    $file = $_SERVER['DOCUMENT_ROOT'] . $path;
    if (!is_readable($file)) {
        return '';
    }
    $svg = file_get_contents($file);
    $svg = preg_replace('/<\?xml[^>]*\?>|<!DOCTYPE[^>]*>|<!--.*?-->/is', '', $svg);
    // Le icone dei browser sono monocromatiche: il colore segue quello del testo della card.
    $svg = preg_replace('/fill="(?!none)[^"]*"/i', 'fill="currentColor"', $svg);
    $svg = preg_replace('/fill:\s*(?!none)[^;"]+/i', 'fill:currentColor', $svg);

    $viewBox = '0 0 24 24';
    $style = '';
    if (preg_match('/<svg\b[^>]*>/i', $svg, $root)) {
        if (preg_match('/viewBox="([^"]*)"/i', $root[0], $match)) {
            $viewBox = $match[1];
        }
        if (preg_match('/style="([^"]*)"/i', $root[0], $match)) {
            $style = ' style="' . $match[1] . '"';
        }
    }
    $inner = preg_replace('/^.*?<svg\b[^>]*>/is', '', $svg);
    $inner = preg_replace('/<\/svg>\s*$/i', '', $inner);

    return '<svg viewBox="' . $viewBox . '" fill="currentColor"' . $style
        . ' aria-hidden="true" focusable="false">' . trim($inner) . '</svg>';
}

$stores = array(
    array(
        'name' => 'Firefox',
        'store' => 'Firefox Browser Add-ons',
        'icon' => '/alpha/images/browsers/mozillafirefox.svg',
        'url' => 'https://addons.mozilla.org/firefox/addon/savmrl-it/',
    ),
    array(
        'name' => 'Chrome',
        'store' => 'Chrome Web Store',
        'icon' => '/alpha/images/browsers/googlechrome.svg',
        'url' => 'https://chromewebstore.google.com/detail/savmrlit/pkbjoeedhjjokllfhgcapbbihianeemn',
    ),
    array(
        'name' => 'Microsoft Edge',
        'store' => 'Microsoft Edge Add-ons',
        'icon' => '/alpha/images/browsers/microsoftedge.svg',
        'url' => 'https://microsoftedge.microsoft.com/addons/detail/savmrlit/jdgcfpdoiojfebhafmihkihficfnpahk',
    ),
);

$features = array(
    array(
        'title' => t('addons-feature-options-title', $lang),
        'desc' => t('addons-feature-options-desc', $lang),
        'icon' => '<path d="M4 7h5"/><path d="M13 7h7"/><circle cx="11" cy="7" r="2"/><path d="M4 17h9"/><path d="M17 17h3"/><circle cx="15" cy="17" r="2"/>',
    ),
    array(
        'title' => t('addons-feature-oneclick-title', $lang),
        'desc' => t('addons-feature-oneclick-desc', $lang),
        'icon' => '<path d="M9 11V5a2 2 0 0 1 4 0v6"/><path d="M13 11V9a2 2 0 0 1 4 0v2"/><path d="M17 11v-1a2 2 0 0 1 4 0v6a6 6 0 0 1-6 6h-2a7 7 0 0 1-7-7v-3a2 2 0 0 1 4 0"/>',
    ),
    array(
        'title' => t('addons-feature-handy-title', $lang),
        'desc' => t('addons-feature-handy-desc', $lang),
        'icon' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/><circle cx="6.5" cy="6.5" r="0.6" fill="currentColor" stroke="none"/><path d="M12 13v4"/><path d="M10 15h4"/>',
    ),
    array(
        'title' => t('addons-feature-free-title', $lang),
        'desc' => t('addons-feature-free-desc', $lang),
        'icon' => '<path d="M9 18l-6-6 6-6"/><path d="M15 6l6 6-6 6"/>',
    ),
);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/meta.php"); ?>
</head>
<body>

<header>
    <?php echo $title_header; ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/auth-header.php"); ?>
</header>
<main>
    <div class="horizontal-center">
        <a href="/alpha/" class="back-nav-link">&larr; <?php echo t('go-home', $lang); ?></a>
        <h2 class="title-section"><?php echo t('addons-title', $lang); ?></h2>
        <p class="horizontal-center-p addon-intro"><?php echo t('addons-intro', $lang); ?></p>

        <div class="big-space"></div>

        <h3 class="addon-section-title"><?php echo t('addons-install-title', $lang); ?></h3>
        <p class="addon-section-subtitle"><?php echo t('addons-install-subtitle', $lang); ?></p>

        <div class="addon-store-grid">
            <?php foreach ($stores as $store) { ?>
                <a class="addon-store-card" href="<?php echo $store['url']; ?>" target="_blank" rel="noopener"
                   aria-label="<?php echo t('addons-install-action', $lang) . ' — ' . $store['name']; ?>">
                    <span class="addon-store-logo">
                        <?php echo addonBrowserIcon($store['icon']); ?>
                    </span>
                    <span class="addon-store-name"><?php echo $store['name']; ?></span>
                    <span class="addon-store-meta"><?php echo $store['store']; ?></span>
                    <span class="addon-store-cta">
                        <?php echo t('addons-install-action', $lang); ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 4v12"/>
                            <path d="M6 13l6 6 6-6"/>
                            <path d="M4 21h16"/>
                        </svg>
                    </span>
                </a>
            <?php } ?>
        </div>

        <p class="addon-hint"><?php echo t('addons-other-browsers', $lang); ?></p>

        <div class="big-space"></div>
        <div class="big-space"></div>

        <h3 class="addon-section-title addon-section-title-spaced"><?php echo t('addons-features-title', $lang); ?></h3>

        <div class="addon-features">
            <?php foreach ($features as $feature) { ?>
                <div class="addon-feature">
                    <span class="addon-feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <?php echo $feature['icon']; ?>
                        </svg>
                    </span>
                    <h3><?php echo $feature['title']; ?></h3>
                    <p><?php echo $feature['desc']; ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
</main>
<footer>
    <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/footer.php"); ?>
</footer>

</body>
</html>
