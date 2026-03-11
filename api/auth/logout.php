<?php
// API logout: destruye la sesion actual.

header('Content-Type: application/json');

require_once __DIR__ . '/../config/session.php'; // Sesion PHP segura.

// Solo POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo no permitido']);
    exit;
}

// Cierra sesion y responde OK.
logoutUser();
echo json_encode(['ok' => true]);



