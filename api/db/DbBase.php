<?php
// Base de datos (PDO) para la API.

class DbBase
{
    protected PDO $pdo;

    public function __construct()
    {
        // Carga configuracion global.
        require_once __DIR__ . '/../../config.php';

        $host = (string) getenv('DB_HOST');
        $name = (string) getenv('DB_NAME');
        $user = (string) getenv('DB_USER');
        $pass = (string) getenv('DB_PASS');

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $host,
            $name
        );

        // Conecta con errores como excepciones.
        $this->pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}


