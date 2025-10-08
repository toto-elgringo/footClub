<?php

namespace Model\Trait;

use database\Database;
use PDO;

trait PdoTrait {
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }
}