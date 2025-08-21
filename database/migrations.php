<?php

class DatabaseMigration {
    private $conn;

    public function __construct() {
        $host = $_ENV['DB_HOST'] ?? 'db';
        $username = $_ENV['DB_USER'] ?? 'user';
        $password = $_ENV['DB_PASSWORD'] ?? 'password';

        try {
            // Conectar sem especificar banco para criar se necessário
            $this->conn = new PDO(
                "mysql:host=" . $host,
                $username,
                $password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            throw new Exception("Connection error: " . $exception->getMessage());
        }
    }

    public function runMigrations() {
        try {
            echo "Running database migrations...\n";

            // Ler e executar o arquivo SQL
            $sql = file_get_contents(__DIR__ . '/schema.sql');
            
            // Dividir em comandos individuais
            $commands = array_filter(array_map('trim', explode(';', $sql)));

            foreach ($commands as $command) {
                if (!empty($command)) {
                    $this->conn->exec($command);
                    echo "Executed: " . substr($command, 0, 50) . "...\n";
                }
            }

            echo "Database migrations completed successfully!\n";

        } catch (Exception $e) {
            echo "Migration error: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}

// Executar migrações se chamado diretamente
if (php_sapi_name() === 'cli') {
    $migration = new DatabaseMigration();
    $migration->runMigrations();
}

