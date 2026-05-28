<?php
declare(strict_types=1);

/**
 * Reads environment variables with a fallback.
 */
function env_value(string $key, ?string $default = null): ?string
{
    load_env_file();
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }

    return $value;
}

/**
 * Loads key=value pairs from project .env into process env.
 */
function load_env_file(): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $loaded = true;

    $envPath = dirname(__DIR__) . '/.env';
    if (!is_file($envPath) || !is_readable($envPath)) {
        return;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        $parts = explode('=', $trimmed, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $name = trim($parts[0]);
        $value = trim($parts[1]);
        $value = trim($value, "\"'");

        if ($name === '') {
            continue;
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

/**
 * Applies hardened session cookie and security headers once per request.
 */
function security_bootstrap(): void
{
    static $bootstrapped = false;
    if ($bootstrapped) {
        return;
    }
    $bootstrapped = true;
    load_env_file();

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $https,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header('X-Robots-Tag: noindex, nofollow, noarchive');
    if ($https) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
}
