<?php
/**
 * Database Connection & Shared Procedural Helpers
 * Connects Core PHP to MySQL using MySQLi.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host     = 'localhost';
$username = 'root';
$password = '';
$database = 'library_management';

// Enable MySQLi exceptions for clean error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($host, $username, $password, $database);
    mysqli_set_charset($conn, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('Database connection failed. Please make sure MySQL is running in XAMPP and the "library_management" database exists. Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

/**
 * Safely escape output for HTML rendering to prevent XSS.
 */
function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Store a one-time flash message in session and redirect.
 */
function redirect_with_flash(string $url, string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
    header('Location: ' . $url);
    exit;
}

/**
 * Retrieve and clear the current flash message, if any.
 */
function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}
?>
