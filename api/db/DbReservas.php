<?php
// Acceso a datos de la tabla reservas.

require_once __DIR__ . '/DbBase.php';

class DbReservas extends DbBase
{
    public function getActive(int $hours = 24): array
    {
        $hours = max(1, $hours);
        $stmt = $this->pdo->prepare("SELECT * FROM reservas
            WHERE COALESCE(fecha_reserva, fecha) >= DATE_SUB(NOW(), INTERVAL :hours HOUR)
            ORDER BY COALESCE(fecha_reserva, fecha) DESC");
        $stmt->execute([':hours' => $hours]);
        return $stmt->fetchAll();
    }

    public function getToday(): array
    {
        $stmt = $this->pdo->query('SELECT nombre, fecha_reserva, fecha FROM reservas
            WHERE COALESCE(fecha_reserva, fecha) IS NOT NULL
            AND DATE(COALESCE(fecha_reserva, fecha)) = CURDATE()
            ORDER BY COALESCE(fecha_reserva, fecha) ASC');
        return $stmt->fetchAll();
    }

    public function getBetween(string $start, string $end): array
    {
        $stmt = $this->pdo->prepare('SELECT fecha_reserva FROM reservas WHERE fecha_reserva IS NOT NULL AND fecha_reserva BETWEEN :start AND :end ORDER BY fecha_reserva ASC');
        $stmt->execute([
            ':start' => $start,
            ':end' => $end,
        ]);
        return $stmt->fetchAll();
    }

    public function getByTelefono(string $telefono, int $limit = 5): array
    {
        $limit = max(1, $limit);
        $stmt = $this->pdo->prepare('SELECT id, fecha_reserva, dia, hora, fecha FROM reservas WHERE telefono = :telefono ORDER BY COALESCE(fecha_reserva, fecha) DESC LIMIT :limit');
        $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function reserveWithHistory(string $nombre, string $telefono, DateTimeImmutable $dt): void
    {
        $fechaReserva = $dt->format('Y-m-d H:i:s');
        $dia = $this->dayName($dt);
        $hora = $dt->format('H:i');

        $this->pdo->beginTransaction();
        try {
            $check = $this->pdo->prepare('SELECT id FROM reservas WHERE fecha_reserva = :fecha_reserva FOR UPDATE');
            $check->execute([':fecha_reserva' => $fechaReserva]);
            if ($check->fetch()) {
                $this->pdo->rollBack();
                throw new RuntimeException('SLOT_TAKEN');
            }

            $insert = $this->pdo->prepare('INSERT INTO reservas (nombre, telefono, fecha_reserva, dia, hora) VALUES (:nombre, :telefono, :fecha_reserva, :dia, :hora)');
            $insert->execute([
                ':nombre' => $nombre,
                ':telefono' => $telefono,
                ':fecha_reserva' => $fechaReserva,
                ':dia' => $dia,
                ':hora' => $hora,
            ]);

            $reservaId = (int) $this->pdo->lastInsertId();
            $insertHist = $this->pdo->prepare('INSERT INTO historial_reservas (reserva_id, nombre, telefono, fecha_reserva, dia, hora, fecha_creacion_reserva) VALUES (:reserva_id, :nombre, :telefono, :fecha_reserva, :dia, :hora, NOW())');
            $insertHist->execute([
                ':reserva_id' => $reservaId,
                ':nombre' => $nombre,
                ':telefono' => $telefono,
                ':fecha_reserva' => $fechaReserva,
                ':dia' => $dia,
                ':hora' => $hora,
            ]);

            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function purgeExpired(): void
    {
        // Borra reservas con mas de 24h.
        $this->pdo->exec("DELETE FROM reservas
            WHERE COALESCE(fecha_reserva, fecha) < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    }

    public function getAll(): array
    {
        // Lista reservas activas.
        $this->purgeExpired();
        $stmt = $this->pdo->query('SELECT * FROM reservas ORDER BY COALESCE(fecha_reserva, fecha) DESC');
        return $stmt->fetchAll();
    }

    public function getById(int $id): array
    {
        // Obtiene reserva por ID.
        $stmt = $this->pdo->prepare('SELECT * FROM reservas WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            throw new Exception('Reserva no encontrada');
        }
        return $row;
    }

    public function insert(string $nombre, string $telefono, string $fechaReserva): array
    {
        // Inserta reserva a partir de fecha/hora.
        $dt = new DateTimeImmutable($fechaReserva);
        $dia = $this->dayName($dt);
        $hora = $dt->format('H:i');

        $stmt = $this->pdo->prepare('INSERT INTO reservas (nombre, telefono, fecha_reserva, dia, hora) VALUES (:nombre, :telefono, :fecha_reserva, :dia, :hora)');
        $stmt->execute([
            ':nombre' => $nombre,
            ':telefono' => $telefono,
            ':fecha_reserva' => $dt->format('Y-m-d H:i:s'),
            ':dia' => $dia,
            ':hora' => $hora,
        ]);

        return $this->getById((int) $this->pdo->lastInsertId());
    }

    public function update(int $id, string $nombre, string $telefono, string $fechaReserva): array
    {
        // Actualiza reserva.
        $dt = new DateTimeImmutable($fechaReserva);
        $dia = $this->dayName($dt);
        $hora = $dt->format('H:i');

        $stmt = $this->pdo->prepare('UPDATE reservas SET nombre = :nombre, telefono = :telefono, fecha_reserva = :fecha_reserva, dia = :dia, hora = :hora WHERE id = :id');
        $stmt->execute([
            ':id' => $id,
            ':nombre' => $nombre,
            ':telefono' => $telefono,
            ':fecha_reserva' => $dt->format('Y-m-d H:i:s'),
            ':dia' => $dia,
            ':hora' => $hora,
        ]);

        return $this->getById($id);
    }

    public function delete(int $id): array
    {
        // Elimina reserva.
        $this->getById($id);
        $stmt = $this->pdo->prepare('DELETE FROM reservas WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return ['deleted' => true];
    }

    private function dayName(DateTimeImmutable $dt): string
    {
        // Nombre del dia en ES.
        $days = [
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
            7 => 'domingo',
        ];

        return $days[(int) $dt->format('N')] ?? '';
    }
}


