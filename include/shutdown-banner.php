<?php
if (!function_exists('t')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . "/include/translations.php");
}
$_sb_lang = isset($lang) ? $lang : detectLanguage();

if (savmrl_is_shutdown()) {
    $domain_end = date('j M Y', strtotime(SAVMRL_DOMAIN_END_DATE));
    $export_end = date('j M Y', strtotime(SAVMRL_EXPORT_END_DATE));
    echo '<div class="shutdown-banner shutdown-banner--active">';
    echo '<strong>' . t('shutdown-active-title', $_sb_lang) . '</strong> ';
    echo str_replace(['{domain_end}', '{export_end}'], [$domain_end, $export_end], t('shutdown-active-desc', $_sb_lang));
    if (savmrl_is_export_open()) {
        echo ' <a href="/account/">' . t('shutdown-export-link', $_sb_lang) . '</a>';
    }
    echo '</div>';
} else {
    $shutdown_date = date('j M Y', strtotime(SAVMRL_SHUTDOWN_DATE));
    echo '<div class="shutdown-banner shutdown-banner--warning">';
    echo '<strong>' . t('shutdown-warning-title', $_sb_lang) . '</strong> ';
    echo str_replace('{shutdown_date}', $shutdown_date, t('shutdown-warning-desc', $_sb_lang));
    echo '</div>';
}
?>
