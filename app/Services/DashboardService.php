<?php
// Servicio orquestador del dashboard (admin/usuarios).
declare(strict_types=1);

final class DashboardService
{
    private UserAdminService $users;
    private ReservationAdminService $reservas;
    private HistoryService $history;

    public function __construct(UserAdminService $users, ReservationAdminService $reservas, HistoryService $history)
    {
        $this->users = $users;
        $this->reservas = $reservas;
        $this->history = $history;
    }

    public function handleAdminRequest(string $role): array
    {
        // Maneja POST del panel y prepara datos para la vista.
        $createUserErrors = [];
        $createUserSuccess = '';
        $createUserForm = [
            'nuevo_usuario' => '',
            'nuevo_rol' => 'trabajador',
        ];

        $userTableErrors = [];
        $userTableSuccess = '';

        $reservationErrors = [];
        $reservationSuccess = '';
        $createReservationForm = [
            'nombre' => '',
            'telefono' => '',
            'fecha_reserva' => '',
        ];

        $method = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!Auth::verifyCsrfToken($csrfToken)) {
                http_response_code(403);
                $createUserErrors[] = 'Token CSRF invalido';
                $reservationErrors[] = 'Token CSRF invalido';
            } else {
                if (isset($_POST['create_user'])) {
                    if ($role !== 'admin') {
                        http_response_code(403);
                        $createUserErrors[] = 'No autorizado';
                    } else {
                        $this->users->handleCreate($createUserErrors, $createUserSuccess, $createUserForm);
                    }
                }

                if (isset($_POST['update_user'])) {
                    if ($role !== 'admin') {
                        http_response_code(403);
                        $userTableErrors[] = 'No autorizado';
                    } else {
                        $this->users->handleUpdate($userTableErrors, $userTableSuccess);
                    }
                }

                if (isset($_POST['delete_user'])) {
                    if ($role !== 'admin') {
                        http_response_code(403);
                        $userTableErrors[] = 'No autorizado';
                    } else {
                        $this->users->handleDelete($userTableErrors, $userTableSuccess);
                    }
                }

                if (isset($_POST['create_reserva'])) {
                    $this->reservas->handleCreate($reservationErrors, $reservationSuccess, $createReservationForm);
                }

                if (isset($_POST['delete_reserva'])) {
                    $id = (int) ($_POST['delete_reserva'] ?? 0);
                    $this->reservas->handleDelete($id);
                }

                if (isset($_POST['update_reserva'])) {
                    $this->reservas->handleUpdate($reservationErrors, $reservationSuccess);
                }
            }
        }

        $this->reservas->purgeExpired(24);
        $reservas = $this->reservas->getActive(24);

        $allowedLimits = [10, 20, 25, 50];
        $historialLimit = (int) ($_GET['historial_limit'] ?? 20);
        if (!in_array($historialLimit, $allowedLimits, true)) {
            $historialLimit = 20;
        }
        $historialPage = (int) ($_GET['historial_page'] ?? 1);
        if ($historialPage < 1) {
            $historialPage = 1;
        }

        $history = $this->history->paginate($historialLimit, $historialPage);
        $users = $role === 'admin' ? $this->users->getAll() : [];

        return [
            'create_user_errors' => $createUserErrors,
            'create_user_success' => $createUserSuccess,
            'create_user_form' => $createUserForm,
            'user_table_errors' => $userTableErrors,
            'user_table_success' => $userTableSuccess,
            'users' => $users,
            'reservation_errors' => $reservationErrors,
            'reservation_success' => $reservationSuccess,
            'create_reservation_form' => $createReservationForm,
            'reservas' => $reservas,
            'historial_rows' => $history['rows'],
            'historial_limit' => $history['limit'],
            'historial_page' => $history['page'],
            'historial_total_pages' => $history['total_pages'],
            'historial_total_rows' => $history['total_rows'],
            'allowed_historial_limits' => $allowedLimits,
        ];
    }

    public function getUsuariosData(): array
    {
        // Datos para la vista de usuarios (reservas de hoy).
        $this->reservas->purgeExpired(24);

        $months = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre',
        ];
        $today = new DateTimeImmutable('now');
        $fechaTitulo = $today->format('d') . ' de ' . $months[(int) $today->format('n')] . ' de ' . $today->format('Y');

        return [
            'reservas_hoy' => $this->reservas->getToday(),
            'fecha_titulo' => $fechaTitulo,
        ];
    }
}

