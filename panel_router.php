<?php
// Router simple del panel.
declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

$route = $route ?? 'login';

try {
    // Enruta segun $route.
    switch ($route) {
        case 'login':
            (new AuthController())->login();
            break;
        case 'logout':
            (new AuthController())->logout();
            break;
        case 'register':
            (new AuthController())->registerRedirect();
            break;
        case 'admin':
            (new DashboardController())->admin();
            break;
        case 'usuarios':
            (new DashboardController())->usuarios();
            break;
        default:
            http_response_code(404);
            echo 'Ruta no encontrada';
            break;
    }
} catch (Throwable $e) {
    // Error generico con log.
    error_log('Panel router error: ' . $e->getMessage());
    http_response_code(500);
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><title>Error</title></head><body class="bg-light"><div class="container py-5"><div class="alert alert-danger"><h1 class="h5">Error interno</h1><p class="mb-0">Ha ocurrido un error inesperado. Revisa logs del servidor.</p></div></div></body></html>';
}


