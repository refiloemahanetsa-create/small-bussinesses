
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $language = $_POST['language'] ?? 'English';

    if (in_array($language, ['English', 'Sesotho'], true)) {
        $_SESSION['language'] = $language;
    }
}

$redirect = $_POST['return_to'] ?? 'index.php';

// Allow redirects only to local page paths.
if (
    !is_string($redirect) ||
    $redirect === '' ||
    str_contains($redirect, '://') ||
    str_starts_with($redirect, '//') ||
    str_contains($redirect, '\\')
) {
    $redirect = 'index.php';
}

header('Location: ' . $redirect);
exit;
?>
