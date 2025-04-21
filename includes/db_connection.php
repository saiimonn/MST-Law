<?php
$is_development = false;

if ($is_development) {
    $host = 'localhost';
    $dbname = 'finalProject';
    $username = 'root';
    $password = '';
} else {
    $host = 'localhost';
    $dbname = 's31200250_mstlaw';
    $username = 's31200250_mstlaw';
    $password = 'ILOVEWEBDEV';
}

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}
?>