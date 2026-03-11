<?php
// API registro: crear usuarios (solo admin).

header('Content-Type: application/json');

require_once __DIR__ . '/../config/session.php'; // Sesion PHP segura.
require_once __DIR__ . '/../db/DbUsers.php';      // Acceso a usuarios.

// Solo POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Metodo no permitido']);
    exit;
}

// Solo admin.
if (!isLogged() || getUserRole() !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Solo admin puede crear usuarios']);
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
$rol = trim((string) ($payload['rol'] ?? 'trabajador'));

if ($usuario === '' || strlen($usuario) < 3 || strlen($usuario) > 50) {
    http_response_code(400);
    echo json_encode(['error' => 'Usuario invalido']);
    exit;
}

if ($password === '' || strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Password invalido']);
    exit;
}

if ($rol === '') {
    $rol = 'trabajador';
}
if ($rol !== 'admin' && $rol !== 'trabajador') {
    http_response_code(400);
    echo json_encode(['error' => 'Rol invalido. Solo admin o trabajador']);
    exit;
}

try {
    // Inserta usuario.
    $db = new DbUsers();
    $user = $db->insert($usuario, $password, $rol);
    echo json_encode($user);
} catch (Throwable $e) {
    // Error generico (detalle en log).
    error_log('API register error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno']);
}


