<?php

namespace Mlkali\Sa\Database;

use PDO;
use PDOException;
use Envms\FluentPDO\Query;

class Fluent
{
    public ?PDO $pdo;
    public ?Query $query;
    public string $dir = '';

    public function __construct()
    {
        $this->dir = dirname(__DIR__, 2) . '\\';
        try {
            $conn = "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']};sslmode=verify-ca;sslrootcert=ca.pem;charset={$_ENV['CHAR']}";
            $this->pdo = new PDO($conn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if ($e->getCode() == 2002) {
                $this->pdo = null;
            }
        }

        $this->query = $this->pdo ? new Query($this->pdo) : null;
    }

    public function execSQLFile(string $name): string
    {
        $sql = file_get_contents($this->dir . "public/sql/{$name}.sql");
        $this->pdo->exec($sql);

        return "SQL file executed successfully.";
    }
}
