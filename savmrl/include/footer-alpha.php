<?php
if (!function_exists('t')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/translations.php");
}
$footerLang = isset($lang) ? $lang : detectLanguage();
?>
Developed with
<div class="square-20px"></div>
<div class="image-heart image-background-primary image-square-20px"></div>
by <a href="https://saveriomorelli.com" class="bold footer-author">Saverio Morelli</a>
<br>
<span class="footer-small-text">
<a href="/savmrl/privacy/"><?php echo t('privacy', $footerLang); ?></a>  | <a href="/savmrl/terms/"><?php echo t('terms', $footerLang); ?></a> | <a
            href="/savmrl/addons/"><?php echo t('addons', $footerLang); ?></a> | <a href="/alpha/account/"><?php echo t('account', $footerLang); ?></a> | <a href="https://savmrl.it/r/github">GitHub</a>
</span>
