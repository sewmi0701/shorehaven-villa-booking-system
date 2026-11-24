<?php
require __DIR__ . '/includes/bootstrap.php';
if (!is_post()) { http_response_code(405); exit('POST required'); }
verify_csrf();
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $parameters = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $parameters['path'], $parameters['domain'], $parameters['secure'], $parameters['httponly']);
}
session_destroy();
redirect('index.php');
