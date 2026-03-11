<?php
// Seccion de historial con paginacion.
declare(strict_types=1);
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mt-4 mb-2 gap-2">
    <h2 class="h5 m-0">Historial de reservas (solo lectura)</h2>
    <form method="get" class="d-flex align-items-center gap-2">
        <label for="historial_limit" class="small text-muted m-0">Mostrar</label>
        <input type="hidden" name="historial_page" value="1">
        <select id="historial_limit" name="historial_limit" class="form-select form-select-sm">
            <?php foreach ($allowed_historial_limits as $limit): ?>
            <option value="<?= (int) $limit ?>" <?= (int) $historial_limit === (int) $limit ? 'selected' : '' ?>><?= (int) $limit ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm btn-outline-primary">Aplicar</button>
    </form>
</div>
<table class="table table-bordered table-striped bg-white">
    <thead class="table-dark">
        <tr>
            <th>ID historial</th>
            <th>ID reserva</th>
            <th>Nombre</th>
            <th>Telefono</th>
            <th>Fecha reservada</th>
            <th>Creada en</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($historial_rows as $row): ?>
        <tr>
            <td><?= htmlspecialchars((string) $row['id']) ?></td>
            <td><?= htmlspecialchars((string) ($row['reserva_id'] ?? '')) ?></td>
            <td><?= htmlspecialchars((string) $row['nombre']) ?></td>
            <td><?= htmlspecialchars((string) ($row['telefono'] ?? '')) ?></td>
            <td><?= htmlspecialchars(formatDateTimeForViewMvc((string) ($row['fecha_reserva'] ?? ''))) ?></td>
            <td><?= htmlspecialchars(formatDateTimeForViewMvc((string) (!empty($row['fecha_creacion_reserva']) ? $row['fecha_creacion_reserva'] : $row['fecha_historial']))) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php
$start = max(1, (int) $historial_page - 2);
$end = min((int) $historial_total_pages, (int) $historial_page + 2);
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
    <small class="text-muted">
        Pagina <?= htmlspecialchars((string) $historial_page) ?> de <?= htmlspecialchars((string) $historial_total_pages) ?>
        (<?= htmlspecialchars((string) $historial_total_rows) ?> registros)
    </small>
    <nav aria-label="Paginacion historial">
        <ul class="pagination pagination-sm mb-0">
            <?php $firstUrl = '?' . http_build_query(['historial_limit' => $historial_limit, 'historial_page' => 1]); ?>
            <?php $prevUrl = '?' . http_build_query(['historial_limit' => $historial_limit, 'historial_page' => max(1, (int) $historial_page - 1)]); ?>
            <?php $nextUrl = '?' . http_build_query(['historial_limit' => $historial_limit, 'historial_page' => min((int) $historial_total_pages, (int) $historial_page + 1)]); ?>
            <?php $lastUrl = '?' . http_build_query(['historial_limit' => $historial_limit, 'historial_page' => $historial_total_pages]); ?>

            <li class="page-item <?= (int) $historial_page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= htmlspecialchars($firstUrl) ?>" aria-label="Primera">&laquo;</a>
            </li>
            <li class="page-item <?= (int) $historial_page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= htmlspecialchars($prevUrl) ?>" aria-label="Anterior">&lsaquo;</a>
            </li>

            <?php for ($p = $start; $p <= $end; $p++): ?>
                <?php $pageUrl = '?' . http_build_query(['historial_limit' => $historial_limit, 'historial_page' => $p]); ?>
                <li class="page-item <?= $p === (int) $historial_page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= htmlspecialchars($pageUrl) ?>"><?= (int) $p ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= (int) $historial_page >= (int) $historial_total_pages ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= htmlspecialchars($nextUrl) ?>" aria-label="Siguiente">&rsaquo;</a>
            </li>
            <li class="page-item <?= (int) $historial_page >= (int) $historial_total_pages ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= htmlspecialchars($lastUrl) ?>" aria-label="Ultima">&raquo;</a>
            </li>
        </ul>
    </nav>
</div>



