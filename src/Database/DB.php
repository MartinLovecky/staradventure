<?php

namespace Mlkali\Sa\Database;

use PDO;
use Envms\FluentPDO\Query;

class DB extends Query{

    protected $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('mysql:host='.$_ENV['DB_HOST'].';dbname='.$_ENV['DB_NAME'].';charset='.$_ENV['CHAR'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
    }

    public function con(): Query
    {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        return new Query($this->pdo);
    }
}

