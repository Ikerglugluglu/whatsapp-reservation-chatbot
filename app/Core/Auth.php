<?php
// Core de autenticacion y sesion para el panel web.
declare(strict_types=1);

final class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Endurece cookies de sesion.
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');
            $isHttps = (
                (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
                || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
            );
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'secure' => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function login(array $user): void
    {
        // Regenera ID y guarda datos minimos en sesion.
        self::startSession();
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) ($user['id'] ?? 0);
        $_SESSION['user_name'] = (string) ($user['usuario'] ?? '');
        $_SESSION['user_rol'] = (string) ($user['rol'] ?? 'trabajador');

        // Compatibilidad con el resto del proyecto.
        $_SESSION['usuario'] = $_SESSION['user_name'];
        $_SESSION['rol'] = $_SESSION['user_rol'];
    }

    public static function logout(): void
    {
        // Cierra la sesion actual.
        self::startSession();
        $_SESSION = [];
        session_destroy();
    }

    public static function isLogged(): bool
    {
        // Sesion valida si existe user_id.
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    public static function userId(): ?int
    {
        // ID numerico del usuario autenticado.
        self::startSession();
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        return (int) $_SESSION['user_id'];
    }

    public static function role(): ?string
    {
        // Rol preferente (user_rol) con fallback.
        self::startSession();
        if (isset($_SESSION['user_rol'])) {
            return (string) $_SESSION['user_rol'];
        }
        if (isset($_SESSION['rol'])) {
            return (string) $_SESSION['rol'];
        }
        return null;
    }

    public static function requireLogin(string $redirectTo): void
    {
        // Redirige si no hay sesion.
        if (!self::isLogged()) {
            header('Location: ' . $redirectTo);
            exit;
        }
    }

    public static function csrfToken(): string
    {
        // Token CSRF por sesion.
        self::startSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return (string) $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(string $token): bool
    {
        // Valida token CSRF.
        self::startSession();
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals((string) $_SESSION['csrf_token'], $token);
    }
}


