<?php
$supported_languages = ['en', 'it', 'fr', 'de', 'es'];

function isValidLanguage($language)
{
    global $supported_languages;
    return in_array($language, $supported_languages);
}

function detectLanguage()
{
    global $supported_languages;

    if (isset($_GET['lang']) && isValidLanguage($_GET['lang'])) {
        return $_GET['lang'];
    }

    if (isset($_COOKIE['savmrl_lang']) && isValidLanguage($_COOKIE['savmrl_lang'])) {
        return $_COOKIE['savmrl_lang'];
    }

    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if (isValidLanguage($browser_lang)) {
            return $browser_lang;
        }
    }

    return 'en';
}

function getStringTranslated($key, $lang = null)
{
    if ($lang === null) {
        $lang = detectLanguage();
    }
    if (!isValidLanguage($lang)) {
        $lang = 'en';
    }

    static $loaded = [];

    if (!isset($loaded[$lang])) {
        $file = $_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/translations/" . $lang . ".php";
        if (file_exists($file)) {
            include $file;
            $loaded[$lang] = isset($strings) ? $strings : [];
        } else {
            $loaded[$lang] = [];
        }
    }

    if (isset($loaded[$lang][$key])) {
        return $loaded[$lang][$key];
    }

    if ($lang !== 'en' && !isset($loaded['en'])) {
        include $_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/translations/en.php";
        $loaded['en'] = isset($strings) ? $strings : [];
    }

    if ($lang !== 'en' && isset($loaded['en'][$key])) {
        return $loaded['en'][$key];
    }

    return '?' . $key . '?';
}

function t($key, $lang = null) {
    return getStringTranslated($key, $lang);
}
?>
