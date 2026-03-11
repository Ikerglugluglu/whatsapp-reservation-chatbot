<?php
// Acceso a datos de historial de reservas.

require_once __DIR__ . '/DbBase.php';

class DbHistorialReservas extends DbBase
{
    public function getAll(): array
    {
        // Lista todo el historial.
        $stmt = $this->pdo->query('SELECT * FROM historial_reservas ORDER BY COALESCE(fecha_reserva, fecha_creacion_reserva, fecha_historial) DESC');
        return $stmt->fetchAll();
    }

    public function paginate(int $limit, int $page): array
    {
        $limit = max(1, $limit);
        $page = max(1, $page);

        $totalStmt = $this->pdo->query('SELECT COUNT(*) AS total FROM historial_reservas');
        $totalRows = (int) ($totalStmt ? $totalStmt->fetchColumn() : 0);
        $totalPages = max(1, (int) ceil($totalRows / $limit));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $offset = ($page - 1) * $limit;

        $stmt = $this->pdo->prepare('SELECT * FROM historial_reservas ORDER BY COALESCE(fecha_reserva, fecha_creacion_reserva, fecha_historial) DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return [
            'rows' => $rows,
            'total_rows' => $totalRows,
            'total_pages' => $totalPages,
            'page' => $page,
            'limit' => $limit,
        ];
    }
}



