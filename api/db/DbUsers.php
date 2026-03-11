<?php
// Acceso a datos de la tabla usuarios.

require_once __DIR__ . '/DbBase.php';

class DbUsers extends DbBase
{
    private function normalizeRole(string $rol): string
    {
        // Normaliza y valida rol.
        $role = strtolower(trim($rol));
        if ($role === '') {
            return 'trabajador';
        }
        if ($role !== 'admin' && $role !== 'trabajador') {
            throw new Exception('Rol invalido. Solo admin o trabajador');
        }
        return $role;
    }

    public function getAll(): array
    {
        // Devuelve todos los usuarios.
        $stmt = $this->pdo->query('SELECT id, usuario, rol FROM usuarios ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function getById(int $id): array
    {
        // Obtiene usuario por ID.
        $stmt = $this->pdo->prepare('SELECT id, usuario, rol FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            throw new Exception('Usuario no encontrado');
        }
        return $row;
    }

    public function getAuthByUsername(string $usuario): ?array
    {
        // Consulta para login.
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1');
        $stmt->execute([':usuario' => $usuario]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function existsByUsername(string $usuario): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM usuarios WHERE usuario = :usuario LIMIT 1');
        $stmt->execute([':usuario' => $usuario]);
        return (bool) $stmt->fetchColumn();
    }

    public function existsByUsernameExceptId(string $usuario, int $excludeId): bool
    {
        $stmt = $this->pdo->prepare('SELECT id FROM usuarios WHERE usuario = :usuario AND id != :id LIMIT 1');
        $stmt->execute([
            ':usuario' => $usuario,
            ':id' => $excludeId,
        ]);
        return (bool) $stmt->fetchColumn();
    }

    public function insert(string $usuario, string $password, string $rol = 'trabajador'): array
    {
        // Crea usuario con password hasheada.
        $rol = $this->normalizeRole($rol);

        $exists = $this->getAuthByUsername($usuario);
        if ($exists) {
            throw new Exception('Ese usuario ya existe');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO usuarios (usuario, password, rol) VALUES (:usuario, :password, :rol)');
        $stmt->execute([
            ':usuario' => $usuario,
            ':password' => $hash,
            ':rol' => $rol,
        ]);

        return $this->getById((int) $this->pdo->lastInsertId());
    }

    public function update(int $id, string $usuario, string $rol, ?string $password = null): array
    {
        // Actualiza usuario y opcionalmente password.
        $rol = $this->normalizeRole($rol);

        $stmt = $this->pdo->prepare('SELECT id FROM usuarios WHERE usuario = :usuario AND id != :id LIMIT 1');
        $stmt->execute([
            ':usuario' => $usuario,
            ':id' => $id,
        ]);
        if ($stmt->fetch()) {
            throw new Exception('Ese usuario ya existe');
        }

        if ($password !== null && $password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $update = $this->pdo->prepare('UPDATE usuarios SET usuario = :usuario, rol = :rol, password = :password WHERE id = :id');
            $update->execute([
                ':usuario' => $usuario,
                ':rol' => $rol,
                ':password' => $hash,
                ':id' => $id,
            ]);
        } else {
            $update = $this->pdo->prepare('UPDATE usuarios SET usuario = :usuario, rol = :rol WHERE id = :id');
            $update->execute([
                ':usuario' => $usuario,
                ':rol' => $rol,
                ':id' => $id,
            ]);
        }

        return $this->getById($id);
    }

    public function delete(int $id): array
    {
        // Elimina usuario.
        $this->getById($id);
        $stmt = $this->pdo->prepare('DELETE FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return ['deleted' => true];
    }
}


