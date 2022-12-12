<?php

namespace Mlkali\Sa\Database;

use PDO;
use Envms\FluentPDO\Query;

class DB extends Query{

    public $pdo;
    public $query;

    public function __construct()
    {
        $this->pdo = new PDO('mysql:host='.$_ENV['DB_HOST'].';dbname='.$_ENV['DB_NAME'].';charset='.$_ENV['CHAR'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);   
        
        $this->query = new Query($this->pdo);
    }

}

