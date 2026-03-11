<?php
// API login: valida credenciales y crea sesion.

header('Content-Type: application/json');

require_once __DIR__ . '/../config/session.php'; // Sesion PHP segura.
require_once __DIR__ . '/../db/DbUsers.php';      // Acceso a usuarios.

// Solo POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo no permitido']);
    exit;
}

$contentType = (string) ($_SERVER['CONTENT_TYPE'] ?? '');
$payload = [];

// Permite JSON o form-data.
if (stripos($contentType, 'application/json') !== false) {
    $payload = json_decode((string) file_get_contents('php://input'), true) ?: [];
} else {
    $payload = $_POST;
}

$usuario = trim((string) ($payload['usuario'] ?? ''));
$password = (string) ($payload['password'] ?? '');

if ($usuario === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Usuario y password son obligatorios']);
    exit;
}

try {
    // Busca usuario y verifica password.
    $db = new DbUsers();
    $user = $db->getAuthByUsername($usuario);

    if ($user === null || !password_verify($password, (string) $user['password'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Credenciales invalidas']);
        exit;
    }

    // Inicia sesion.
    loginUser($user);
    echo json_encode([
        'ok' => true,
        'user' => [
            'id' => (int) $user['id'],
            'usuario' => (string) $user['usuario'],
            'rol' => (string) $user['rol'],
        ],
    ]);
} catch (Throwable $e) {
    // Error generico (detalle en log).
    error_log('API login error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno']);
}


