<?php
// API de historial de reservas (solo lectura, admin).

header('Content-Type: application/json');
header('Allow: GET');

require_once __DIR__ . '/config/session.php';       // Sesion PHP segura.
require_once __DIR__ . '/db/DbHistorialReservas.php'; // Acceso a datos de historial.

// Requiere autenticacion.
if (!isLogged()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// Solo admin puede ver historial.
if ((string) getUserRole() !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo admin puede ver historial']);
    exit;
}

// Solo GET.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo no permitido']);
    exit;
}

try {
    // Devuelve todo el historial.
    $db = new DbHistorialReservas();
    echo json_encode($db->getAll());
} catch (Throwable $e) {
    // Error generico (detalle en log).
    error_log('API historial error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno']);
}


