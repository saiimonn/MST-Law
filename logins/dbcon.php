<?php
$is_development = false;

if($is_development) {
    $conn = mysqli_connect("localhost", "root", "", "finalProject");
} else {
    $conn = mysqli_connect("localhost", "s31200250_mstlaw", "ILOVEWEBDEV", "s31200250_mstlaw");
}

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$conn->set_charset("utf8mb4");
?>