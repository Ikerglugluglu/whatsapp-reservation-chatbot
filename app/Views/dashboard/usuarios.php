<?php
// Vista de reservas para usuarios.
declare(strict_types=1);

function formatDateTimeForViewUser(?string $value): string
{
    if ($value === null || trim($value) === '') {
        return '';
    }
    try {
        return (new DateTimeImmutable($value))->format('Y-m-d H:i');
    } catch (Throwable $e) {
        return $value;
    }
}
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 m-0">Reservas de Hoy</h1>
        <div class="d-flex align-items-center gap-2">
            <a href="usuarios.php" class="btn btn-primary btn-sm">Actualizar</a>
            <a href="panel_logout.php" class="btn btn-outline-danger btn-sm">Cerrar sesion</a>
        </div>
    </div>

    <p class="text-muted mb-3">Fecha actual: <?= htmlspecialchars((string) ($fecha_titulo ?? '')) ?></p>

    <table class="table table-bordered table-striped bg-white">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Fecha reservada</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($reservas_hoy)): ?>
            <tr>
                <td colspan="2" class="text-center">No hay reservas para hoy.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($reservas_hoy as $row): ?>
            <tr>
                <td><?= htmlspecialchars((string) $row['nombre']) ?></td>
                <td><?= htmlspecialchars(formatDateTimeForViewUser((string) (!empty($row['fecha_reserva']) ? $row['fecha_reserva'] : $row['fecha']))) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>


