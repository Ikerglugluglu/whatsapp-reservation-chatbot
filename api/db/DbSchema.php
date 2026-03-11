<?php
// Migraciones / schema de la base de datos.

require_once __DIR__ . '/DbBase.php';

class DbSchema extends DbBase
{
    public function ensureTables(): void
    {
        // Tabla usuarios.
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            rol VARCHAR(20) DEFAULT 'trabajador'
        )");

        // Normaliza roles existentes.
        $this->pdo->exec("UPDATE usuarios
            SET rol = 'trabajador'
            WHERE rol IS NULL OR rol = '' OR LOWER(rol) NOT IN ('admin', 'trabajador')");

        // Convierte a ENUM.
        $this->pdo->exec("ALTER TABLE usuarios
            MODIFY COLUMN rol ENUM('admin','trabajador') NOT NULL DEFAULT 'trabajador'");

        // Tabla reservas.
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS reservas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            telefono VARCHAR(20),
            fecha_reserva DATETIME NULL,
            dia VARCHAR(20),
            hora VARCHAR(10),
            fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Indice unico en fecha_reserva.
        if (!$this->indexExists('reservas', 'uniq_fecha_reserva')) {
            $this->pdo->exec("ALTER TABLE reservas ADD UNIQUE KEY uniq_fecha_reserva (fecha_reserva)");
        }

        // Tabla historial.
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS historial_reservas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            reserva_id INT NULL,
            nombre VARCHAR(100) NOT NULL,
            telefono VARCHAR(20),
            fecha_reserva DATETIME NULL,
            dia VARCHAR(20),
            hora VARCHAR(10),
            fecha_creacion_reserva DATETIME NULL,
            fecha_historial TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Unicidad por reserva.
        if (!$this->indexExists('historial_reservas', 'uniq_hist_reserva_id')) {
            $this->pdo->exec("ALTER TABLE historial_reservas ADD UNIQUE KEY uniq_hist_reserva_id (reserva_id)");
        }

        // Copia reservas existentes a historial si faltan.
        $this->pdo->exec("INSERT INTO historial_reservas (reserva_id, nombre, telefono, fecha_reserva, dia, hora, fecha_creacion_reserva)
            SELECT r.id, r.nombre, r.telefono, r.fecha_reserva, r.dia, r.hora, r.fecha
            FROM reservas r
            LEFT JOIN historial_reservas h ON h.reserva_id = r.id
            WHERE h.id IS NULL");

        // Triggers para evitar cambios en historial.
        if (!$this->triggerExists('historial_reservas_no_update')) {
            $this->pdo->exec("CREATE TRIGGER historial_reservas_no_update
                BEFORE UPDATE ON historial_reservas
                FOR EACH ROW
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'historial_reservas es de solo lectura'");
        }

        if (!$this->triggerExists('historial_reservas_no_delete')) {
            $this->pdo->exec("CREATE TRIGGER historial_reservas_no_delete
                BEFORE DELETE ON historial_reservas
                FOR EACH ROW
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'historial_reservas es de solo lectura'");
        }
    }

    public function ensureAdminUser(): ?string
    {
        // Crea un admin inicial si no existe ninguno.
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'admin'");
        $count = (int) $stmt->fetchColumn();
        if ($count > 0) {
            return null;
        }

        $username = trim((string) (getenv('ADMIN_USER') ?: 'admin'));
        $password = (string) (getenv('ADMIN_PASS') ?: 'admin1234');
        if ($username === '') {
            $username = 'admin';
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert = $this->pdo->prepare("INSERT INTO usuarios (usuario, password, rol) VALUES (:usuario, :password, 'admin')");
        $insert->execute([
            ':usuario' => $username,
            ':password' => $hash,
        ]);

        return $username;
    }

    private function indexExists(string $table, string $index): bool
    {
        // Comprueba si existe indice.
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM information_schema.statistics
            WHERE table_schema = DATABASE() AND table_name = :table_name AND index_name = :index_name");
        $stmt->execute([
            ':table_name' => $table,
            ':index_name' => $index,
        ]);

        return ((int) $stmt->fetchColumn()) > 0;
    }

    private function triggerExists(string $triggerName): bool
    {
        // Comprueba si existe trigger.
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM information_schema.triggers
            WHERE trigger_schema = DATABASE() AND trigger_name = :trigger_name");
        $stmt->execute([
            ':trigger_name' => $triggerName,
        ]);

        return ((int) $stmt->fetchColumn()) > 0;
    }
}
