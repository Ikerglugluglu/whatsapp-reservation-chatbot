<?php
// Vista de login del panel.
declare(strict_types=1);
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h4 mb-3">Acceso al Panel Administrativo</h2>

                    <?php if (!empty($created)): ?>
                    <div class="alert alert-success">Usuario creado correctamente.</div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars((string) $error) ?></div>
                    <?php endif; ?>

                    <form method="post" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string) ($csrf_token ?? '')) ?>">
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input id="usuario" type="text" name="usuario" class="form-control" value="<?= htmlspecialchars((string) ($username ?? '')) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contrasena</label>
                            <input id="password" type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Iniciar sesion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>




