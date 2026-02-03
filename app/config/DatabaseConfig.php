<?php
namespace App\config;

use PDO;
use PDOException;

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

class DatabaseConfig{
    private ?PDO $pdo = null;

    protected function connect(){
        $dsn = $_ENV['DSN'] ?? 'mysql:host=sql104.infinityfree.com;port=3306;dbname=if0_41063993_thefestival;charset=utf8mb4';
        $username = $_ENV['USERNAME'] ?? 'if0_41063993';
        $password = $_ENV['PASSWORD'] ?? 'eGCAaJovZTVtur0';
        try{
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->pdo;
        }catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }
    protected function disconnect(){
        $this->pdo = null;
    }
}