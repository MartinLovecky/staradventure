<?php

namespace Mlkali\Sa\Database;

use PDO;
use Envms\FluentPDO\Query;

class DB extends Query{

    public $pdo;
    public Query $query;
    
    public function __construct()
    {
        $this->query = $this->connect();
    }

    public function connect(): Query
    {
        $dns = 'mysql:host='.$_ENV['DB_HOST'].';dbname='.$_ENV['DB_NAME'].';charset='.$_ENV['CHAR'];
        $this->pdo = new PDO($dns, $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        $fpdo = new Query($this->pdo);
        return $fpdo;
    }
}

