<?php
/* -->Base Repository Class
   Provides common database operations for entities.
*/
namespace App\Framework;
use App\config\DatabaseConfig;
use PDOException;
use PDO;

class Repository extends DatabaseConfig {
    protected PDO $pdo;

    public function __construct() {
       
    }
}

    