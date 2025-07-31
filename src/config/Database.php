<?php
namespace App\Config;

use mysqli;
use Exception;

class Database 
{
    private static $instance = null;
    private $connection;
    
    // Private constructor - prevents direct creation
    private function __construct() 
    {
        $this->connect();
    }
    
    // The only way to get the database instance
    public static function getInstance(): self 
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect(): void 
    {
        try {
            $this->connection = new mysqli(
                'localhost',  // host
                'root',       // username
                '',           // password
                'crud_db'     // database name
            );
            
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            
            $this->connection->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection(): mysqli 
    {
        return $this->connection;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() 
    {
        throw new Exception("Cannot unserialize singleton");
    }
}