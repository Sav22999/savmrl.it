<?php
global $current_user, $maintenance_bypass;
if (!function_exists('t')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/translations.php");
}
$authLang = isset($lang) ? $lang : detectLanguage();
?>
<div class="auth-nav">
    <?php if (isset($maintenance_bypass) && $maintenance_bypass): ?>
        <div class="maintenance-banner">Maintenance mode active</div>
    <?php endif; ?>
    <?php if ($current_user): ?>
        <a href="/account/" class="auth-nav-link"><?php echo htmlspecialchars($current_user['username']); ?></a>
        <form method="post" action="/account/logout/" class="auth-nav-logout">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">
            <button type="submit" class="auth-nav-link auth-logout-btn"><?php echo t('logout', $authLang); ?></button>
        </form>
    <?php else: ?>
        <a href="/account/login/" class="auth-nav-link"><?php echo t('login', $authLang); ?></a>
    <?php endif; ?>
</div>
