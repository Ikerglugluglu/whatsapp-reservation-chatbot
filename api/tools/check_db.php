<?php
// Herramienta CLI para verificar conexion y tablas.

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "Forbidden\n";
    exit;
}

require_once __DIR__ . '/../db/DbBase.php'; // Conexion base.

class DbHealthCheck extends DbBase
{
    public function run(): array
    {
        // Estado de salud de BD.
        $result = [
            'db_connection' => false,
            'query_ok' => false,
            'tables' => [],
        ];

        // Test de conexion.
        $this->pdo->query('SELECT 1')->fetchColumn();
        $result['db_connection'] = true;
        $result['query_ok'] = true;

        // Verifica tablas requeridas.
        $required = ['usuarios', 'reservas', 'historial_reservas'];
        foreach ($required as $table) {
            $stmt = $this->pdo->prepare(
                'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table_name'
            );
            $stmt->execute([':table_name' => $table]);
            $exists = ((int) $stmt->fetchColumn()) > 0;
            $result['tables'][$table] = $exists;
        }

        return $result;
    }
}

try {
    // Ejecuta check.
    $health = (new DbHealthCheck())->run();
    echo json_encode($health, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    // Error a STDERR.
    fwrite(STDERR, "ERROR: " . $e->getMessage() . PHP_EOL);
    exit(1);
}



