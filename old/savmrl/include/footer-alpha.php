<?php
if (!function_exists('t')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/translations.php");
}
$footerLang = isset($lang) ? $lang : detectLanguage();
?>
<nav class="footer-links">
    <a href="/alpha/privacy/"><?php echo t('privacy', $footerLang); ?></a>
    <a href="/alpha/terms/"><?php echo t('terms', $footerLang); ?></a>
    <a href="/alpha/addons/"><?php echo t('addons', $footerLang); ?></a>
    <a href="https://savmrl.it/r/github">GitHub</a>
    <a href="/alpha/admin/">Admin</a>
</nav>
<div class="footer-attribution">
    Developed with <span class="image-heart image-background-primary image-heart-inline"></span> by <a href="https://saveriomorelli.com" class="footer-author">Saverio Morelli</a>
</div>
