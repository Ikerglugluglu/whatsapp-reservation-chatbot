<?php
// Servicio de autenticacion (login).
declare(strict_types=1);

final class AuthService
{
    private DbUsers $userModel;

    public function __construct(DbUsers $userModel)
    {
        $this->userModel = $userModel;
    }

    public function handleLogin(): array
    {
        // Valida CSRF, credenciales y devuelve resultado para el controlador.
        Auth::startSession();

        $error = '';
        $username = '';

        $method = (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST') {
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');
            if (!Auth::verifyCsrfToken($csrfToken)) {
                http_response_code(403);
                $error = 'Token CSRF invalido';
            } else {
                $username = trim((string) ($_POST['usuario'] ?? ''));
                $password = (string) ($_POST['password'] ?? '');

                if ($username === '' || $password === '') {
                    $error = 'Usuario y Contrasena son obligatorios';
                } else {
                    $user = $this->userModel->getAuthByUsername($username);
                    if (!$user) {
                        $error = 'Usuario no encontrado';
                    } elseif (!password_verify($password, (string) ($user['password'] ?? ''))) {
                        $error = 'Contrasena incorrecta';
                    } else {
                        // OK: inicia sesion.
                        Auth::login($user);
                        return [
                            'redirect' => 'admin.php',
                        ];
                    }
                }
            }
        }

        return [
            'error' => $error,
            'username' => $username,
        ];
    }
}

