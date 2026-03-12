<?php
// API de reservas: CRUD con control de rol y limpieza de expiradas.

header('Content-Type: application/json');
header('Allow: GET, POST, PUT, DELETE');

require_once __DIR__ . '/config/session.php'; // Sesion PHP segura.
require_once __DIR__ . '/db/DbReservas.php';  // Acceso a datos de reservas.

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

// Requiere autenticacion.
if (!isLogged()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

try {
    // Inicializa DB, limpia expiradas y valida rol.
    $db = new DbReservas();
    $db->purgeExpired();
    $role = (string) getUserRole();
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            // Listado de reservas.
            echo json_encode($db->getAll());
            break;

        case 'POST':
            // Crear reserva: admin o trabajador.
            if ($role !== 'admin' && $role !== 'trabajador') {
                throw new Exception('No autorizado');
            }

            $data = getRequestData();
            $nombre = trim((string) ($data['nombre'] ?? ''));
            $telefono = trim((string) ($data['telefono'] ?? ''));
            $fechaReserva = trim((string) ($data['fecha_reserva'] ?? ''));

            if ($nombre === '' || $fechaReserva === '') {
                throw new Exception('Faltan campos obligatorios');
            }

            echo json_encode($db->insert($nombre, $telefono, $fechaReserva));
            break;

        case 'PUT':
            // Actualizar reserva: admin o trabajador.
            if ($role !== 'admin' && $role !== 'trabajador') {
                throw new Exception('No autorizado');
            }

            $data = getRequestData();
            $id = (int) ($data['id'] ?? 0);
            $nombre = trim((string) ($data['nombre'] ?? ''));
            $telefono = trim((string) ($data['telefono'] ?? ''));
            $fechaReserva = trim((string) ($data['fecha_reserva'] ?? ''));

            if ($id <= 0 || $nombre === '' || $fechaReserva === '') {
                throw new Exception('Faltan campos obligatorios');
            }

            echo json_encode($db->update($id, $nombre, $telefono, $fechaReserva));
            break;

        case 'DELETE':
            // Borrar: solo admin.
            if ($role !== 'admin') {
                throw new Exception('Solo admin puede borrar reservas');
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
    if ($e instanceof RuntimeException && $e->getMessage() === 'SLOT_TAKEN') {
        http_response_code(409);
        echo json_encode(['error' => 'Esa franja de 1 hora ya esta reservada.']);
        exit;
    }
    // Error generico (detalle en log).
    error_log('API reservas error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno']);
}


