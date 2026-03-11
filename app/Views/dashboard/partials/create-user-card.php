<?php
// Formulario para crear usuarios (solo admin).
declare(strict_types=1);
?>
<div class="card shadow-sm mb-4" id="crear-usuario">
    <div class="card-body">
        <h2 class="h5 mb-3">Crear usuario</h2>

        <?php if (!empty($create_user_errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($create_user_errors as $error): ?>
                <li><?= htmlspecialchars((string) $error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (!empty($create_user_success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars((string) $create_user_success) ?></div>
        <?php endif; ?>

        <form method="post" class="row g-3" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? '')) ?>">
            <div class="col-12 col-md-6">
                <label for="nuevo_usuario" class="form-label">Usuario</label>
                <input id="nuevo_usuario" type="text" name="nuevo_usuario" class="form-control" maxlength="50" value="<?= htmlspecialchars((string) ($create_user_form['nuevo_usuario'] ?? '')) ?>" required>
            </div>
            <div class="col-12 col-md-6">
                <label for="nuevo_rol" class="form-label">Rol</label>
                <select id="nuevo_rol" name="nuevo_rol" class="form-select">
                    <option value="trabajador" <?= (($create_user_form['nuevo_rol'] ?? '') === 'trabajador') ? 'selected' : '' ?>>trabajador</option>
                    <option value="admin" <?= (($create_user_form['nuevo_rol'] ?? '') === 'admin') ? 'selected' : '' ?>>admin</option>
                </select>
            </div>
            <div class="col-12 col-md-6">
                <label for="nueva_password" class="form-label">Contrasena</label>
                <input id="nueva_password" type="password" name="nueva_password" class="form-control" minlength="6" required>
            </div>
            <div class="col-12 col-md-6">
                <label for="confirmar_password" class="form-label">Confirmar Contrasena</label>
                <input id="confirmar_password" type="password" name="confirmar_password" class="form-control" minlength="6" required>
            </div>
            <div class="col-12">
                <button name="create_user" value="1" type="submit" class="btn btn-success">Crear usuario</button>
            </div>
        </form>
    </div>
</div>




