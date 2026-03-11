<?php
// Tabla editable de reservas.
declare(strict_types=1);
?>
<table class="table table-bordered table-striped bg-white">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Telefono</th>
            <th>Fecha reservada</th>
            <th>Fecha Reserva</th>
            <th>Accion</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($reservas as $row): ?>
        <tr>
            <td><?= htmlspecialchars((string) $row['id']) ?></td>
            <td>
                <form method="post" class="d-flex gap-1">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? '')) ?>">
                    <input type="hidden" name="update_reserva" value="<?= (int) $row['id'] ?>">
                    <input type="text" name="nombre" class="form-control form-control-sm" maxlength="100" value="<?= htmlspecialchars((string) $row['nombre']) ?>" required>
            </td>
            <td>
                    <input type="text" name="telefono" class="form-control form-control-sm" maxlength="20" value="<?= htmlspecialchars((string) ($row['telefono'] ?? '')) ?>">
            </td>
            <td>
                    <?php
                    $fechaBase = !empty($row['fecha_reserva']) ? (string) $row['fecha_reserva'] : (string) $row['fecha'];
                    $fechaLocal = '';
                    try {
                        $fechaLocal = (new DateTimeImmutable($fechaBase))->format('Y-m-d\TH:i');
                    } catch (Throwable $e) {
                        $fechaLocal = '';
                    }
                    ?>
                    <input type="datetime-local" name="fecha_reserva" class="form-control form-control-sm" value="<?= htmlspecialchars($fechaLocal) ?>" required>
            </td>
            <td><?= htmlspecialchars(formatDateTimeForViewMvc((string) $row['fecha'])) ?></td>
            <td>
                    <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                </form>
                <form method="post" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? '')) ?>">
                    <button name="delete_reserva" value="<?= (int) $row['id'] ?>" class="btn btn-sm btn-danger">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>


