<?php

namespace App\Model\Trait;

use App\database\Database;
use PDO;

trait PdoTrait {
    private PDO $db;

    public function __construct()
    {   
        $this->db = Database::getConnection();
    }
}