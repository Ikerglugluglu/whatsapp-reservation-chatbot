<?php
// Servicio de historial (solo lectura).
declare(strict_types=1);

final class HistoryService
{
    private DbHistorialReservas $history;

    public function __construct(DbHistorialReservas $history)
    {
        $this->history = $history;
    }

    public function paginate(int $limit, int $page): array
    {
        // Paginacion de historial.
        return $this->history->paginate($limit, $page);
    }
}
