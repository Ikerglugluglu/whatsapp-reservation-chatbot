<?php
// Servicio de gestion de usuarios (solo admin).
declare(strict_types=1);

final class UserAdminService
{
    private DbUsers $users;

    public function __construct(DbUsers $users)
    {
        $this->users = $users;
    }

    public function getAll(): array
    {
        // Lista usuarios.
        return $this->users->getAll();
    }

    public function handleCreate(array &$errors, string &$success, array &$form): void
    {
        // Valida y crea un usuario.
        $username = trim((string) ($_POST['nuevo_usuario'] ?? ''));
        $password = (string) ($_POST['nueva_password'] ?? '');
        $confirm = (string) ($_POST['confirmar_password'] ?? '');
        $role = trim((string) ($_POST['nuevo_rol'] ?? 'trabajador'));

        $form['nuevo_usuario'] = $username;
        $form['nuevo_rol'] = $role;

        if ($username === '') {
            $errors[] = 'El usuario es obligatorio';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'El usuario debe tener entre 3 y 50 caracteres';
        }

        if ($password === '') {
            $errors[] = 'La Contrasena es obligatoria';
        } elseif (strlen($password) < 6) {
            $errors[] = 'La Contrasena debe tener al menos 6 caracteres';
        }

        if ($confirm === '') {
            $errors[] = 'Debes confirmar la Contrasena';
        }

        if ($password !== $confirm) {
            $errors[] = 'Las Contrasenas no coinciden';
        }

        if ($role === '') {
            $role = 'trabajador';
        }
        if (!in_array($role, ['admin', 'trabajador'], true)) {
            $errors[] = 'Rol invalido. Solo admin o trabajador';
        }

        if (empty($errors) && $this->users->existsByUsername($username)) {
            $errors[] = 'Ese usuario ya existe';
        }

        if (empty($errors)) {
            $this->users->insert($username, $password, $role);
            $success = 'Usuario creado correctamente';
            $form = [
                'nuevo_usuario' => '',
                'nuevo_rol' => 'trabajador',
            ];
        }
    }

    public function handleUpdate(array &$errors, string &$success): void
    {
        // Valida y actualiza un usuario.
        $id = (int) ($_POST['update_user'] ?? 0);
        $username = trim((string) ($_POST['edit_usuario'] ?? ''));
        $role = trim((string) ($_POST['edit_rol'] ?? 'trabajador'));
        $password = (string) ($_POST['edit_password'] ?? '');

        if ($id <= 0) {
            $errors[] = 'Usuario invalido';
        }
        if ($username === '') {
            $errors[] = 'El usuario es obligatorio';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'El usuario debe tener entre 3 y 50 caracteres';
        }

        if ($role === '') {
            $role = 'trabajador';
        }
        if (!in_array($role, ['admin', 'trabajador'], true)) {
            $errors[] = 'Rol invalido. Solo admin o trabajador';
        }

        if (empty($errors) && $this->users->existsByUsernameExceptId($username, $id)) {
            $errors[] = 'Ese usuario ya existe';
        }

        if ($password !== '' && strlen($password) < 6) {
            $errors[] = 'La Contrasena debe tener al menos 6 caracteres';
        }

        if (empty($errors)) {
            $this->users->update($id, $username, $role, $password === '' ? null : $password);
            $success = 'Usuario actualizado correctamente';
        }
    }

    public function handleDelete(array &$errors, string &$success): void
    {
        // Elimina un usuario.
        $id = (int) ($_POST['delete_user'] ?? 0);
        if ($id <= 0) {
            $errors[] = 'Usuario invalido';
        }

        if (empty($errors)) {
            $this->users->delete($id);
            $success = 'Usuario eliminado correctamente';
        }
    }
}
