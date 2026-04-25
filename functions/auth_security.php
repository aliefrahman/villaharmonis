<?php
// Mencegah akses langsung
if (!defined('CSRF_PROTECTION')) {
    define('CSRF_PROTECTION', true);
}

// Generate CSRF Token
function generate_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verifikasi CSRF Token
function verify_csrf_token($token)
{
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Cek Brute Force / Rate Limiting (Maks 5 percobaan per 5 menit)
function check_login_attempts($ip_address)
{
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = time();
    }

    // Reset percobaan jika sudah lewat 5 menit (300 detik)
    if (time() - $_SESSION['last_attempt_time'] > 300) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = time();
    }

    if ($_SESSION['login_attempts'] >= 5) {
        return false; // Diblokir sementara
    }
    return true; // Boleh mencoba login
}

function record_failed_login()
{
    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    $_SESSION['last_attempt_time'] = time();
}

function reset_login_attempts()
{
    unset($_SESSION['login_attempts']);
    unset($_SESSION['last_attempt_time']);
}
?>