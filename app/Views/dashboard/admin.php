<?php
// Vista del panel admin.
declare(strict_types=1);

function formatDateTimeForViewMvc(?string $value): string
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
        <h1 class="h3 m-0">Panel Administrativo</h1>
        <a href="panel_logout.php" class="btn btn-outline-danger btn-sm">Cerrar sesion</a>
    </div>

    <?php if (($current_role ?? '') === 'admin'): ?>
        <?php require __DIR__ . '/partials/create-user-card.php'; ?>
        <?php require __DIR__ . '/partials/users-table.php'; ?>
    <?php endif; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 mb-2 gap-2">
        <h2 class="h5 m-0">Reservas</h2>
        <div class="d-flex align-items-center gap-2">
            <a href="admin.php" class="btn btn-primary btn-sm">Actualizar ahora</a>
            <span class="small text-muted">Ultima carga: <?= htmlspecialchars(date('Y-m-d H:i')) ?></span>
        </div>
    </div>

    <?php if (!empty($reservation_errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($reservation_errors as $error): ?>
            <li><?= htmlspecialchars((string) $error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (!empty($reservation_success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars((string) $reservation_success) ?></div>
    <?php endif; ?>

    <?php require __DIR__ . '/partials/create-reservation-card.php'; ?>
    <?php require __DIR__ . '/partials/reservations-table.php'; ?>
    <?php require __DIR__ . '/partials/history-section.php'; ?>
</div>



