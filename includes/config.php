<?php
declare(strict_types=1);

/**
 * Loads configuration from environment variables, with sane defaults for local dev.
 * Copy .env.example to .env and fill in real values before going to production.
 */

if (!function_exists('env')) {
function env(string $key, ?string $default = null): ?string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $envPath = dirname(__DIR__) . '/.env';
        if (is_readable($envPath)) {
            foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                    continue;
                }
                [$k, $v] = explode('=', $line, 2);
                $cache[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
            }
        }
    }
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }
    $val = getenv($key);
    return $val === false ? $default : $val;
}
}

return [
    'app' => [
        'name'  => env('APP_NAME', 'imeicheck'),
        'url'   => rtrim((string) env('APP_URL', 'http://localhost:8080'), '/'),
        'debug' => filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN),
    ],
    'db' => [
        'host' => env('DB_HOST', 'localhost'),
        'name' => env('DB_NAME', 'imei_checker'),
        'user' => env('DB_USER', 'root'),
        'pass' => env('DB_PASS', ''),
    ],
    'api' => [
        'provider'        => env('IMEI_API_PROVIDER', 'sickw'),
        'key'             => env('IMEI_API_KEY', ''),
        'url'             => env('IMEI_API_URL', 'https://sickw.com/api.php'),
        'default_service' => env('IMEI_API_DEFAULT_SERVICE', '0'),
    ],
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID', ''),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
    ],
    'stripe' => [
        'publishable_key' => env('STRIPE_PUBLISHABLE_KEY', ''),
        'secret_key'      => env('STRIPE_SECRET_KEY', ''),
        'webhook_secret'  => env('STRIPE_WEBHOOK_SECRET', ''),
    ],
    'session' => [
        'lifetime_days' => (int) env('SESSION_LIFETIME_DAYS', '30'),
        'cookie_name'   => env('SESSION_COOKIE_NAME', 'imeicheck_sid'),
    ],
];
