<?php
/** Common bootstrap for every page. Supports XAMPP and PHP built-in server. */
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

define('APP_NAME', 'Shorehaven Villa');
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'shorehaven_booking');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

$scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
if (preg_match('~/(admin|api)$~', $scriptDirectory)) {
    $scriptDirectory = substr($scriptDirectory, 0, strrpos($scriptDirectory, '/'));
}
define('BASE_PATH', ($scriptDirectory === '/' || $scriptDirectory === '.') ? '' : rtrim($scriptDirectory, '/'));

require_once __DIR__ . '/functions.php';

function db(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) return $connection;
    try {
        $connection = new PDO(
            'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
             PDO::ATTR_EMULATE_PREPARES => false]
        );
    } catch (PDOException $e) {
        http_response_code(503);
        error_log('Database connection issue: ' . $e->getMessage());
        die('<!doctype html><html lang="en"><meta charset="utf-8"><title>Setup needed</title><body style="font:16px system-ui;background:#f4faf9;color:#153b3a;padding:5vw"><h1>Database connection needed</h1><p>Start Apache and MySQL in XAMPP, import <code>db_structure.sql</code>, then check your database settings in <code>includes/bootstrap.php</code> or environment variables.</p><p><a href="https://www.apachefriends.org/">Download XAMPP</a></p></body></html>');
    }
    return $connection;
}
