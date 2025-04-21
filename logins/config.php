<?php
$is_development = true;

if ($is_development) {
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'finalProject');
} else {
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 's31200250_mstlaw');
    define('DB_PASSWORD', 'ILOVEWEBDEV');
    define('DB_NAME', 's31200250_mstlaw');
}

function getDbConnection() {
    try {
        $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        if (!$conn->set_charset("utf8mb4")) {
            throw new Exception("Error loading character set utf8mb4: " . $conn->error);
        }
        
        return $conn;
        
    } catch (Exception $e) {
        error_log($e->getMessage());
        die("Database connection failed. Please try again later.");
    }
}

$conn = getDbConnection();
?>