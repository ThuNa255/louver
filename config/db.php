<?php

$hostName = $_SERVER['HTTP_HOST'] ?? '';

if ($hostName === 'localhost:8080' || $hostName === '127.0.0.1') {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "bagshop";
} 
// Nếu đang chạy trên InfinityFree
else {
    $host = "sql100.infinityfree.com";
    $user = "if0_40541312";
    $pass = "6nivQz2j2G";
    $db   = "if0_40541312_bagshop";
}
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
