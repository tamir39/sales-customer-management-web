<?php    
    class ConnectDatabase {
        public $conn;
    
        public function __construct()
        {
            try {
                $this->conn = new PDO("mysql:host=localhost:3306;dbname=management", "root", "");
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
    
        public function getConnection()
        {
            return $this->conn;
        }
    
        public function __destruct()
        {
            $this->conn = null;  // Close connection
        }
    }  
?> 