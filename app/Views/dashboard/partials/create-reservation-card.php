<?php
// Formulario para crear reservas.
declare(strict_types=1);
?>
<div class="card shadow-sm mb-4" id="crear-reserva">
    <div class="card-body">
        <h2 class="h5 mb-3">Crear reserva</h2>
        <form method="post" class="row g-3" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? '')) ?>">
            <div class="col-12 col-md-4">
                <label for="create_nombre" class="form-label">Nombre</label>
                <input id="create_nombre" type="text" name="create_nombre" class="form-control" maxlength="100" value="<?= htmlspecialchars((string) ($create_reservation_form['nombre'] ?? '')) ?>" required>
            </div>
            <div class="col-12 col-md-4">
                <label for="create_telefono" class="form-label">Telefono</label>
                <input id="create_telefono" type="text" name="create_telefono" class="form-control" maxlength="20" value="<?= htmlspecialchars((string) ($create_reservation_form['telefono'] ?? '')) ?>">
            </div>
            <div class="col-12 col-md-4">
                <label for="create_fecha_reserva" class="form-label">Fecha reservada</label>
                <input id="create_fecha_reserva" type="datetime-local" name="create_fecha_reserva" class="form-control" value="<?= htmlspecialchars((string) ($create_reservation_form['fecha_reserva'] ?? '')) ?>" required>
            </div>
            <div class="col-12">
                <button name="create_reserva" value="1" type="submit" class="btn btn-success">Crear reserva</button>
            </div>
        </form>
    </div>
</div>


