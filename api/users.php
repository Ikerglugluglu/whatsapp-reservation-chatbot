<?php
// API de usuarios: CRUD con control de rol y sesion.

header('Content-Type: application/json');
header('Allow: GET, POST, PUT, DELETE');

require_once __DIR__ . '/config/session.php'; // Sesion PHP segura.
require_once __DIR__ . '/db/DbUsers.php';     // Acceso a datos de usuarios.

function getRequestData(): array
{
    // Permite JSON, querystring en PUT/DELETE o POST normal.
    $contentType = (string) ($_SERVER['CONTENT_TYPE'] ?? '');
    if (stripos($contentType, 'application/json') !== false) {
        return json_decode((string) file_get_contents('php://input'), true) ?: [];
    }
    if ($_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
        parse_str((string) file_get_contents('php://input'), $data);
        return is_array($data) ? $data : [];
    }
    return $_POST;
}

// Requiere autenticacion para acceder a usuarios.
if (!isLogged()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

try {
    // Inicializa DB y valida rol.
    $db = new DbUsers();
    $role = (string) getUserRole();
    if ($role !== 'admin' && $role !== 'trabajador') {
        throw new Exception('Rol no autorizado');
    }
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            // Admin ve todos, trabajador solo su usuario.
            if ($role === 'admin') {
                echo json_encode($db->getAll());
                break;
            }

            $userId = getUserId();
            if ($userId === null) {
                throw new Exception('Sesion invalida');
            }
            echo json_encode($db->getById($userId));
            break;

        case 'POST':
            // Crear usuario solo admin.
            if ($role !== 'admin') {
                throw new Exception('Solo admin puede crear usuarios');
            }
            $data = getRequestData();
            $usuario = trim((string) ($data['usuario'] ?? ''));
            $password = (string) ($data['password'] ?? '');
            $rol = trim((string) ($data['rol'] ?? 'trabajador'));

            if ($usuario === '' || $password === '') {
                throw new Exception('Faltan campos obligatorios');
            }
            if ($rol !== 'admin' && $rol !== 'trabajador') {
                throw new Exception('Rol invalido. Solo admin o trabajador');
            }
            echo json_encode($db->insert($usuario, $password, $rol));
            break;

        case 'PUT':
            // Actualizar: admin cualquiera, trabajador solo su cuenta.
            $data = getRequestData();
            $id = (int) ($data['id'] ?? 0);
            $usuario = trim((string) ($data['usuario'] ?? ''));
            $password = isset($data['password']) ? (string) $data['password'] : null;
            $rolInput = trim((string) ($data['rol'] ?? ''));

            if ($id <= 0 || $usuario === '') {
                throw new Exception('Faltan campos obligatorios');
            }

            if ($role !== 'admin') {
                $myId = getUserId();
                if ($myId === null || $myId !== $id) {
                    throw new Exception('No autorizado');
                }
                $current = $db->getById($id);
                $rolInput = (string) ($current['rol'] ?? 'trabajador');
            }

            if ($rolInput === '') {
                $rolInput = 'trabajador';
            }
            if ($rolInput !== 'admin' && $rolInput !== 'trabajador') {
                throw new Exception('Rol invalido. Solo admin o trabajador');
            }
            echo json_encode($db->update($id, $usuario, $rolInput, $password));
            break;

        case 'DELETE':
            // Borrar: solo admin.
            if ($role !== 'admin') {
                throw new Exception('Solo admin puede borrar usuarios');
            }
            $data = getRequestData();
            $id = (int) ($data['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('ID invalido');
            }
            echo json_encode($db->delete($id));
            break;

        default:
            // Metodo no soportado.
            http_response_code(405);
            echo json_encode(['error' => 'Metodo no permitido']);
            break;
    }
} catch (Throwable $e) {
    // Error generico (detalle en log).
    error_log('API users error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno']);
}


