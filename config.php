<?php
// Carga configuracion de entorno y helpers de URL.
require_once __DIR__ . '/vendor/autoload.php';

// Exponer variables tambien para getenv().
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->safeLoad();

if (!function_exists('detectPublicBaseUrl')) {
    function detectPublicBaseUrl(): string
    {
        $fromEnv = trim((string) (getenv('PUBLIC_BASE_URL') ?: getenv('APP_URL') ?: ''));
        if ($fromEnv !== '') {
            return rtrim($fromEnv, '/');
        }

        // Si hay ngrok local, intenta detectar el public_url.
        $ngrokApiUrl = trim((string) (getenv('NGROK_API_URL') ?: 'http://127.0.0.1:4040/api/tunnels'));
        if ($ngrokApiUrl !== '') {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 1.5,
                    'ignore_errors' => true,
                ],
            ]);

            $raw = @file_get_contents($ngrokApiUrl, false, $context);
            if (is_string($raw) && $raw !== '') {
                $data = json_decode($raw, true);
                if (is_array($data) && isset($data['tunnels']) && is_array($data['tunnels'])) {
                    $httpUrl = '';
                    foreach ($data['tunnels'] as $tunnel) {
                        if (!is_array($tunnel)) {
                            continue;
                        }

                        $publicUrl = trim((string) ($tunnel['public_url'] ?? ''));
                        if ($publicUrl === '') {
                            continue;
                        }

                        if (str_starts_with($publicUrl, 'https://')) {
                            return rtrim($publicUrl, '/');
                        }

                        if ($httpUrl === '' && str_starts_with($publicUrl, 'http://')) {
                            $httpUrl = $publicUrl;
                        }
                    }

                    if ($httpUrl !== '') {
                        return rtrim($httpUrl, '/');
                    }
                }
            }
        }

        // Fallback: esquema + host actuales.
        $host = trim((string) ($_SERVER['HTTP_HOST'] ?? ''));
        if ($host !== '') {
            $isHttps = (
                (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
                || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
            );
            $scheme = $isHttps ? 'https' : 'http';
            return $scheme . '://' . $host;
        }

        return '';
    }
}

if (!function_exists('getWebhookUrl')) {
    function getWebhookUrl(): string
    {
        // Construye la URL publica del webhook.
        $base = detectPublicBaseUrl();
        $pathFromEnv = trim((string) (getenv('WEBHOOK_PATH') ?: ''));
        if ($pathFromEnv !== '') {
            $path = '/' . ltrim($pathFromEnv, '/');
        } else {
            $scriptName = trim((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
            $path = $scriptName !== '' ? $scriptName : '/index.php';
        }

        if ($base === '') {
            return $path;
        }

        return rtrim($base, '/') . $path;
    }
}


