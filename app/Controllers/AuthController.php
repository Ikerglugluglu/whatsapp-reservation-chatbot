<?php
// Controlador de autenticacion del panel.
declare(strict_types=1);

final class AuthController
{
    private DbUsers $userModel;
    private AuthService $authService;

    public function __construct()
    {
        $this->userModel = new DbUsers();
        $this->authService = new AuthService($this->userModel);
    }

    public function login(): void
    {
        // Procesa login y renderiza vista.
        $result = $this->authService->handleLogin();
        if (!empty($result['redirect'])) {
            header('Location: ' . $result['redirect']);
            exit;
        }

        View::render('auth/login', [
            'title' => 'Login Administrativo',
            'error' => (string) ($result['error'] ?? ''),
            'username' => (string) ($result['username'] ?? ''),
            'created' => (string) ($_GET['created'] ?? '') === '1',
            'csrf_token' => Auth::csrfToken(),
        ]);
    }

    public function logout(): void
    {
        // Logout y vuelta al login.
        Auth::logout();
        header('Location: panel_login.php');
        exit;
    }

    public function registerRedirect(): void
    {
        // Redireccion controlada al formulario de crear usuario.
        Auth::startSession();
        if (!Auth::isLogged()) {
            header('Location: panel_login.php');
            exit;
        }
        header('Location: admin.php#crear-usuario');
        exit;
    }
}

