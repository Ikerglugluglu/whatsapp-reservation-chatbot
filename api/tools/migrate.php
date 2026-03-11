<?php
// Herramienta CLI para migrar/asegurar schema.

// Run with: php api/tools/migrate.php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "Forbidden\n";
    exit;
}

require_once __DIR__ . '/../db/DbSchema.php'; // Migraciones.

try {
    // Ejecuta migracion.
    $schema = new DbSchema();
    $schema->ensureTables();
    $createdAdmin = $schema->ensureAdminUser();
    echo "OK: schema ensured.\n";
    if ($createdAdmin !== null) {
        echo "INFO: admin creado: {$createdAdmin}\n";
        echo "INFO: password por defecto: admin1234 (cambia en cuanto puedas)\n";
    }
    exit(0);
} catch (Throwable $e) {
    // Error a STDERR.
    fwrite(STDERR, "ERROR: " . $e->getMessage() . PHP_EOL);
    exit(1);
}


