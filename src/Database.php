<?php
namespace EntityLite;

use PDO;
use PDOException;

class Database {

    private $host;
    private $username;
    private $password;
    private $dbname;
    private $db;

    public function __construct($host, $username, $password, $dbname) {
        
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;

        // Create the DSN (Data Source Name)
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
        
        try {
            // Initialize the PDO connection
            $this->db = new PDO($dsn, $this->username, $this->password);
            // Set error mode to exception
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Handle connection error
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function getConnection() {
        return $this->db;
    }
}
