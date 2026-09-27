<?php
global $current_user, $maintenance_bypass;
if (!function_exists('t')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/translations.php");
}
$authLang = isset($lang) ? $lang : detectLanguage();
$supported_langs_map = ['en' => 'EN', 'it' => 'IT', 'fr' => 'FR', 'de' => 'DE', 'es' => 'ES'];
?>
<div class="auth-nav">
    <?php if (isset($maintenance_bypass) && $maintenance_bypass): ?>
        <div class="maintenance-banner">Maintenance mode active</div>
    <?php endif; ?>

    <div class="header-lang-selector">
        <select onchange="switchLanguage(this.value)" aria-label="<?php echo t('language', $authLang); ?>">
            <?php foreach ($supported_langs_map as $code => $label): ?>
                <option value="<?php echo $code; ?>" <?php if ($authLang === $code) echo 'selected'; ?>><?php echo $label; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($current_user): ?>
        <a href="/alpha/account/" class="auth-nav-link"><?php echo htmlspecialchars($current_user['username']); ?></a>
        <form method="post" action="/alpha/account/logout/" class="auth-nav-logout">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">
            <button type="submit" class="auth-nav-link auth-logout-btn"><?php echo t('logout', $authLang); ?></button>
        </form>
    <?php else: ?>
        <a href="/alpha/account/login/" class="auth-nav-link"><?php echo t('login', $authLang); ?></a>
    <?php endif; ?>
</div>
<script>
function switchLanguage(lang) {
    document.cookie = 'savmrl_lang=' + lang + ';path=/;max-age=' + (365*86400) + ';SameSite=Lax';
    window.location.reload();
}
</script>
