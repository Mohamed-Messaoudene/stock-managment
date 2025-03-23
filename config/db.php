<?php
require_once 'config.php';

class Database
{
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $db;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->dbname = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];

        try {
            // Step 1: Connect to MySQL server (without specifying a database)
            $connection = new PDO("mysql:host={$this->host}", $this->username, $this->password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Step 2: Check if the database exists, and create it if it doesn't
            $query = "CREATE DATABASE IF NOT EXISTS {$this->dbname}";
            $connection->exec($query);

            // Step 3: Connect to the newly created database
            $this->db = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->db;
    }
}
?>
