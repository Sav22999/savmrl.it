<?php
global $current_user, $maintenance_bypass;
if (!function_exists('t')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . "/alpha/include/translations.php");
}
$authLang = isset($lang) ? $lang : detectLanguage();
$supported_langs_map = ['en' => 'English', 'it' => 'Italiano', 'fr' => 'Français', 'de' => 'Deutsch', 'es' => 'Español'];
?>
<div class="auth-nav">
    <?php if (isset($maintenance_bypass) && $maintenance_bypass): ?>
        <div class="maintenance-banner">Maintenance mode active</div>
    <?php endif; ?>

    <div class="lang-toggle" id="lang-toggle">
        <button type="button" class="lang-toggle-btn" id="lang-toggle-btn" aria-label="<?php echo t('language', $authLang); ?>">
            <?php echo strtoupper($authLang); ?>
            <svg class="lang-toggle-arrow" width="10" height="10" viewBox="0 0 10 10"><path fill="currentColor" d="M5 7L1 3h8z"/></svg>
        </button>
        <div class="lang-toggle-menu" id="lang-toggle-menu">
            <?php foreach ($supported_langs_map as $code => $label): ?>
                <button type="button" class="lang-toggle-option<?php if ($authLang === $code) echo ' active'; ?>" onclick="switchLanguage('<?php echo $code; ?>')"><span class="lang-code"><?php echo strtoupper($code); ?></span> <?php echo $label; ?></button>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($current_user): ?>
        <a href="/alpha/account/" class="auth-nav-link"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.5"/><path d="M5 20c0-3 3.5-5 7-5s7 2 7 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg> <?php echo htmlspecialchars($current_user['username']); ?></a>
        <form method="post" action="/alpha/account/logout/" class="auth-nav-logout">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generate_csrf_token()); ?>">
            <button type="submit" class="auth-nav-link auth-logout-btn"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 3h2.996C18.2 3 19.3 3 20.15 3.436a3 3 0 011.414 1.414C22 5.7 22 6.8 22 9.004V14.996C22 17.2 22 18.3 21.564 19.15a3 3 0 01-1.414 1.414C19.3 21 18.2 21 15.996 21H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M12 12H2m0 0l3.5-3M2 12l3.5 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg> <?php echo t('logout', $authLang); ?></button>
        </form>
    <?php else: ?>
        <a href="/alpha/account/login/" class="auth-nav-link"><svg class="icon-inline" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 3h2.996C18.2 3 19.3 3 20.15 3.436a3 3 0 011.414 1.414C22 5.7 22 6.8 22 9.004V14.996C22 17.2 22 18.3 21.564 19.15a3 3 0 01-1.414 1.414C19.3 21 18.2 21 15.996 21H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M14 12H2m0 0l3.5-3M2 12l3.5 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg> <?php echo t('login', $authLang); ?></a>
    <?php endif; ?>
</div>
<script>
function switchLanguage(lang) {
    document.cookie = 'savmrl_lang=' + lang + ';path=/;max-age=' + (365*86400) + ';SameSite=Lax';
    window.location.reload();
}
(function() {
    var btn = document.getElementById('lang-toggle-btn');
    var menu = document.getElementById('lang-toggle-menu');
    var toggle = document.getElementById('lang-toggle');
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        toggle.classList.toggle('open');
    });
    document.addEventListener('click', function() {
        toggle.classList.remove('open');
    });
    menu.addEventListener('click', function(e) { e.stopPropagation(); });
})();
</script>
