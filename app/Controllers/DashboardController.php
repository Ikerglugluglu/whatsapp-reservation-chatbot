<?php
// Controlador del panel admin/usuarios.
declare(strict_types=1);

final class DashboardController
{
    private DashboardService $dashboardService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService(
            new UserAdminService(new DbUsers()),
            new ReservationAdminService(new DbReservas()),
            new HistoryService(new DbHistorialReservas())
        );
    }

    public function admin(): void
    {
        // Vista principal del panel admin.
        Auth::requireLogin('panel_login.php');
        $role = (string) Auth::role();
        if ($role !== 'admin' && $role !== 'trabajador') {
            header('Location: panel_logout.php');
            exit;
        }

        $viewData = $this->dashboardService->handleAdminRequest($role);
        View::render('dashboard/admin', [
            'title' => 'Panel Administrativo - Bot Padel',
            'current_role' => $role,
            'csrf_token' => Auth::csrfToken(),
            'create_user_errors' => $viewData['create_user_errors'],
            'create_user_success' => $viewData['create_user_success'],
            'create_user_form' => $viewData['create_user_form'],
            'user_table_errors' => $viewData['user_table_errors'],
            'user_table_success' => $viewData['user_table_success'],
            'users' => $viewData['users'],
            'reservation_errors' => $viewData['reservation_errors'],
            'reservation_success' => $viewData['reservation_success'],
            'create_reservation_form' => $viewData['create_reservation_form'],
            'reservas' => $viewData['reservas'],
            'historial_rows' => $viewData['historial_rows'],
            'historial_limit' => $viewData['historial_limit'],
            'historial_page' => $viewData['historial_page'],
            'historial_total_pages' => $viewData['historial_total_pages'],
            'historial_total_rows' => $viewData['historial_total_rows'],
            'allowed_historial_limits' => $viewData['allowed_historial_limits'],
        ]);
    }

    public function usuarios(): void
    {
        // Vista de reservas del dia para trabajadores.
        Auth::requireLogin('panel_login.php');
        $viewData = $this->dashboardService->getUsuariosData();

        View::render('dashboard/usuarios', [
            'title' => 'Reservas de Hoy',
            'reservas_hoy' => $viewData['reservas_hoy'],
            'fecha_titulo' => $viewData['fecha_titulo'],
        ]);
    }
}

