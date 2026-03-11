<?php
// Entry point de vista usuarios (alias /reservas).
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    $requestPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
    if (is_string($requestPath) && preg_match('#/usuarios\.php$#', $requestPath) === 1) {
        header('Location: reservas');
        exit;
    }
}

$route = 'usuarios';
require __DIR__ . '/panel_router.php';


