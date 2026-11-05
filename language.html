
<?php

// Start the session if it has not already started.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Change language when a valid language is selected.
if (isset($_GET['lang'])) {
    $selectedLanguage = $_GET['lang'];

    if (in_array($selectedLanguage, ['en', 'st'], true)) {
        $_SESSION['language'] = $selectedLanguage;
    }
}

// Use English as the default language.
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en';
}

// Load the selected language file.
$languageFile = __DIR__ . '/languages/'
              . $_SESSION['language'] . '.php';

// Fall back to English if the selected file is missing.
if (!file_exists($languageFile)) {
    $languageFile = __DIR__ . '/languages/en.php';
    $_SESSION['language'] = 'en';
}

$lang = require $languageFile;

// Translation function.
if (!function_exists('t')) {
    function t($key)
    {
        global $lang;

        return $lang[$key] ?? $key;
    }
}
?>
