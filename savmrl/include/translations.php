<?php
function isValidLanguage($language)
{
    $valid_languages = ['en', 'it', 'es', 'fr', 'de']; // Add more valid languages as needed
    return in_array($language, $valid_languages);
}

function getStringTranslated($key, $lang = 'en')
{
    if (!isValidLanguage($lang)) {
        $lang = 'en'; // Fallback to English if the language is not valid
    }

    include_once($_SERVER['DOCUMENT_ROOT'] . "/savmrl/include/translations/" . $lang . ".php");

    if (isset($strings) && is_array($strings) && array_key_exists($key, $strings)) {
        return $strings[$key] ?? $key;
    } else {
        return '?' . $key . '?';
    }
}

?>