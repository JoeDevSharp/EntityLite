<?php
namespace EntityLite;

use PDO;
use PDOException;

class DbContext {
    private $db;
    private $entities = [];

    public function __construct($db) {
        $this->db = $db;
    }

    public function addEntity($name, $entityClass) {
        $this->entities[$name] = new DbSet($this->db, $entityClass, $name);
    }

    public function __get($name) {
        return isset($this->entities[$name]) ? $this->entities[$name] : null;
    }

     // Ensure the database exists, create it if it doesn't
    public function ensureCreatedDatabase(string $dbname): void {
        try {
            // Create a new connection to the server (without specifying a database)
            $serverConnection = new PDO("mysql:host={$this->db->getAttribute(PDO::ATTR_CONNECTION_STATUS)}", $this->db->getAttribute(PDO::ATTR_USER), $this->db->getAttribute(PDO::ATTR_PASSWORD));
            $serverConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Create database if it does not exist
            $query = "CREATE DATABASE IF NOT EXISTS `$dbname`";
            $serverConnection->exec($query);
        } catch (PDOException $e) {
            throw new \Exception("Failed to create database: " . $e->getMessage());
        }
    }

    // Ensure the database is deleted
    public function ensureDeleteDatabase(string $dbname): void {
        try {
            // Create a new connection to the server (without specifying a database)
            $serverConnection = new PDO("mysql:host={$this->db->getAttribute(PDO::ATTR_CONNECTION_STATUS)}", $this->db->getAttribute(PDO::ATTR_USER), $this->db->getAttribute(PDO::ATTR_PASSWORD));
            $serverConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Drop database if it exists
            $query = "DROP DATABASE IF EXISTS `$dbname`";
            $serverConnection->exec($query);
        } catch (PDOException $e) {
            throw new \Exception("Failed to delete database: " . $e->getMessage());
        }
    }
}