
<?php


require_once __DIR__ . '/includes/auth.php';

$postedToken = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['csrf_token'] ?? '';

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !is_string($postedToken) ||
    !is_string($sessionToken) ||
    !hash_equals($sessionToken, $postedToken)
) {

    http_response_code(400);
    exit('Invalid logout request.');

}
$_SESSION = [];



if (ini_get('session.use_cookies')) {

    $cookieParams = session_get_cookie_params();

    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $cookieParams['path'],
        'domain' => $cookieParams['domain'],
        'secure' => $cookieParams['secure'],
        'httponly' => $cookieParams['httponly'],
        'samesite' => $cookieParams['samesite'],
    ]);

}

session_destroy();

header('Location: index.php');
exit;

?>
