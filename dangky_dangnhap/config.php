<?php
// Kết nối MySQL
$host = 'localhost';
$dbname = 'terrarium_db';
$username = 'root';
$password = ''; // Mặc định XAMPP không có mật khẩu

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
