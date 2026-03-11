<?php
// API checkAdmin: valida si el usuario es admin.

header('Content-Type: application/json');

require_once __DIR__ . '/../config/session.php'; // Sesion PHP segura.

// Requiere autenticacion.
if (!isLogged()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autenticado']);
    exit;
}

// Verifica rol admin.
if (getUserRole() !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

// OK.
echo json_encode(['ok' => true]);



