<?php
// Tabla editable de usuarios (solo admin).
declare(strict_types=1);
?>
<div class="card shadow-sm mb-4" id="tabla-usuarios">
    <div class="card-body">
        <h2 class="h5 mb-3">Usuarios (solo admin)</h2>

        <?php if (!empty($user_table_errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($user_table_errors as $error): ?>
                <li><?= htmlspecialchars((string) $error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (!empty($user_table_success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars((string) $user_table_success) ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Nueva ContraseÃƒÂ±a</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= (int) $user['id'] ?></td>
                        <td>
                            <form method="post" class="d-flex gap-2 align-items-center">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? '')) ?>">
                                <input type="hidden" name="update_user" value="<?= (int) $user['id'] ?>">
                                <input type="text" name="edit_usuario" class="form-control form-control-sm" maxlength="50" value="<?= htmlspecialchars((string) $user['usuario']) ?>" required>
                        </td>
                        <td>
                                <select name="edit_rol" class="form-select form-select-sm">
                                    <option value="trabajador" <?= ($user['rol'] ?? '') === 'trabajador' ? 'selected' : '' ?>>trabajador</option>
                                    <option value="admin" <?= ($user['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>admin</option>
                                </select>
                        </td>
                        <td>
                                <input type="password" name="edit_password" class="form-control form-control-sm" minlength="6" placeholder="Dejar en blanco">
                        </td>
                        <td>
                                <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                            </form>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? '')) ?>">
                                <button name="delete_user" value="<?= (int) $user['id'] ?>" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
