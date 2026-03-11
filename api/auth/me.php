<?php
// API me: devuelve info basica del usuario logueado.

header('Content-Type: application/json');

require_once __DIR__ . '/../config/session.php'; // Sesion PHP segura.

// Requiere autenticacion.
if (!isLogged()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// Devuelve datos de sesion.
echo json_encode([
    'id' => getUserId(),
    'usuario' => getUsername(),
    'rol' => getUserRole(),
]);



